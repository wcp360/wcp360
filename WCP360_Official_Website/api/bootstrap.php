<?php
declare(strict_types=1);
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) { http_response_code(404); exit; }
$config = require __DIR__ . '/config.php';

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
header('Cache-Control: no-store');

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('N360SESSID');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function text_value(string $key, int $max=1000): string {
    $v = trim((string)($_POST[$key] ?? ''));
    $v = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/u', '', $v) ?? '';
    return mb_substr($v,0,$max,'UTF-8');
}
function header_value(string $v): string { return trim(str_replace(["\\r","\\n"],' ', $v)); }
function phone_ok(string $v): bool {
    $digits=preg_replace('/\\D+/','',$v)??'';
    if(str_starts_with($digits,'0033')) $digits=substr($digits,4);
    elseif(str_starts_with($digits,'33')) $digits=substr($digits,2);
    elseif(str_starts_with($digits,'0')) $digits=substr($digits,1);
    return preg_match('/^[1-9]\\d{8}$/',$digits)===1;
}
function allowed_value(string $value, array $allowed): bool { return in_array($value, $allowed, true); }
function valid_iso_date(string $value): bool {
    if ($value === '') return true;
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $d !== false && $d->format('Y-m-d') === $value;
}
function csrf_ok(): bool {
    $sent=(string)($_POST['csrf']??''); $stored=(string)($_SESSION['csrf']??'');
    return $sent!=='' && $stored!=='' && hash_equals($stored,$sent);
}
function rotate_csrf(): void { $_SESSION['csrf']=bin2hex(random_bytes(32)); }
function redirect_result(string $path): never { header('Location: '.$path, true, 303); exit; }
function same_origin_ok(array $config): bool {
    $origin=$_SERVER['HTTP_ORIGIN']??''; if($origin==='') return true;
    $originHost=parse_url($origin,PHP_URL_HOST);
    $requestHost=preg_replace('/:\d+$/','',(string)($_SERVER['HTTP_HOST']??''));
    if(!is_string($originHost) || $requestHost==='') return false;
    return hash_equals(strtolower($requestHost),strtolower($originHost));
}
function honeypot_ok(): bool { return trim((string)($_POST['website']??''))===''; }
function rate_limit_ok(string $form, array $config): bool {
    $host=preg_replace('/:\d+$/','',(string)($_SERVER['HTTP_HOST']??''));
    if(in_array($host,['127.0.0.1','localhost'],true)) return true;
    $ip=(string)($_SERVER['REMOTE_ADDR']??'unknown');
    $key=hash('sha256',$form.'|'.$ip); $file=sys_get_temp_dir().'/n360-rate-'.$key.'.json';
    $now=time(); $window=(int)$config['rate_limit_window']; $limit=(int)$config['rate_limit_count'];
    $fh=@fopen($file,'c+'); if(!$fh) return true;
    if(!flock($fh,LOCK_EX)){fclose($fh);return true;}
    $raw=stream_get_contents($fh); $times=json_decode($raw?:'[]',true); if(!is_array($times)) $times=[];
    $times=array_values(array_filter($times,fn($t)=>is_int($t)&&$t>=$now-$window));
    if(count($times)>=$limit){flock($fh,LOCK_UN);fclose($fh);return false;}
    $times[]=$now; ftruncate($fh,0); rewind($fh); fwrite($fh,json_encode($times)); fflush($fh); flock($fh,LOCK_UN); fclose($fh); return true;
}
function encode_subject(string $s): string { return '=?UTF-8?B?'.base64_encode($s).'?='; }
function build_mail_payload(array $config,string $subject,string $body,string $replyTo,array $attachments=[]): array {
    $to=header_value($config['recipient']); $from=header_value($config['from_email']); $fromName=header_value($config['from_name']); $reply=header_value($replyTo);
    $boundary='n360_'.bin2hex(random_bytes(12));
    $headers=[]; $headers[]='MIME-Version: 1.0'; $headers[]='From: '.$fromName.' <'.$from.'>'; $headers[]='Reply-To: '.$reply; $headers[]='Content-Type: multipart/mixed; boundary="'.$boundary.'"';
    $msg='--'.$boundary."\r\n".'Content-Type: text/plain; charset=UTF-8'."\r\n".'Content-Transfer-Encoding: base64'."\r\n\r\n".chunk_split(base64_encode($body));
    foreach($attachments as $a){
        $msg.='--'.$boundary."\r\n".'Content-Type: '.$a['mime'].'; name="'.$a['name'].'"'."\r\n".'Content-Disposition: attachment; filename="'.$a['name'].'"'."\r\n".'Content-Transfer-Encoding: base64'."\r\n\r\n".chunk_split(base64_encode($a['data']));
    }
    $msg.='--'.$boundary.'--'."\r\n";
    return ['to'=>$to,'from'=>$from,'subject'=>encode_subject($subject),'headers'=>$headers,'body'=>$msg];
}
function smtp_read($socket): string {
    $response='';
    while(($line=fgets($socket, 1024))!==false){
        $response.=$line;
        if(strlen($line)<4 || $line[3]!=='-') break;
    }
    if($response==='') throw new RuntimeException('empty SMTP response');
    return $response;
}
function smtp_write_all($socket,string $data): void {
    $length=strlen($data); $offset=0;
    while($offset<$length){
        $written=fwrite($socket,substr($data,$offset));
        if($written===false||$written===0) throw new RuntimeException('SMTP write failed');
        $offset+=$written;
    }
}
function smtp_expect($socket,?string $command,array $expected): string {
    if($command!==null) smtp_write_all($socket,$command."\r\n");
    $response=smtp_read($socket); $code=(int)substr($response,0,3);
    if(!in_array($code,$expected,true)) throw new RuntimeException('SMTP response '.$code);
    return $response;
}
function send_via_smtp(array $config,array $payload): bool {
    $host=trim((string)($config['smtp_host']??'')); $port=(int)($config['smtp_port']??465); $security=strtolower(trim((string)($config['smtp_security']??'ssl')));
    $username=(string)($config['smtp_username']??''); $password=(string)($config['smtp_password']??''); $timeout=(int)($config['smtp_timeout']??15);
    if($host===''||$port<1||$username===''||$password===''||!in_array($security,['ssl','tls'],true)) return false;
    $context=stream_context_create(['ssl'=>['verify_peer'=>true,'verify_peer_name'=>true,'peer_name'=>$host,'allow_self_signed'=>false]]);
    $prefix=$security==='ssl'?'ssl://':''; $errno=0; $error='';
    $socket=@stream_socket_client($prefix.$host.':'.$port,$errno,$error,$timeout,STREAM_CLIENT_CONNECT,$context);
    if(!is_resource($socket)){
        error_log('NETTOYAGE360 SMTP connection failed on '.$host.':'.$port.' ('.$errno.'): '.$error);
        return false;
    }
    stream_set_timeout($socket,$timeout);
    try{
        smtp_expect($socket,null,[220]);
        smtp_expect($socket,'EHLO www.nettoyage360.fr',[250]);
        if($security==='tls'){
            smtp_expect($socket,'STARTTLS',[220]);
            if(stream_socket_enable_crypto($socket,true,STREAM_CRYPTO_METHOD_TLS_CLIENT)!==true) throw new RuntimeException('SMTP TLS failed');
            smtp_expect($socket,'EHLO www.nettoyage360.fr',[250]);
        }
        smtp_expect($socket,'AUTH LOGIN',[334]);
        smtp_expect($socket,base64_encode($username),[334]);
        smtp_expect($socket,base64_encode($password),[235]);
        smtp_expect($socket,'MAIL FROM:<'.$payload['from'].'>',[250]);
        smtp_expect($socket,'RCPT TO:<'.$payload['to'].'>',[250,251]);
        smtp_expect($socket,'DATA',[354]);
        $messageHeaders=array_merge([
            'Date: '.date(DATE_RFC2822),
            'To: <'.$payload['to'].'>',
            'Subject: '.$payload['subject'],
        ],$payload['headers']);
        $data=implode("\r\n",$messageHeaders)."\r\n\r\n".$payload['body'];
        $data=preg_replace('/(?m)^\./','..',$data)??$data;
        smtp_expect($socket,rtrim($data,"\r\n")."\r\n.",[250]);
        smtp_expect($socket,'QUIT',[221]);
        fclose($socket); return true;
    }catch(Throwable $error){
        error_log('NETTOYAGE360 SMTP failed: '.$error->getMessage());
        fclose($socket); return false;
    }
}
function send_mail_with_attachments(array $config,string $subject,string $body,string $replyTo,array $attachments=[]): bool {
    $payload=build_mail_payload($config,$subject,$body,$replyTo,$attachments);
    if(($config['mail_transport']??'mail')==='smtp') return send_via_smtp($config,$payload);
    return @mail($payload['to'],$payload['subject'],$payload['body'],implode("\r\n",$payload['headers']));
}
function reject(string $code): never { error_log('NETTOYAGE360 form rejected: '.$code); redirect_result('../formulaire-erreur?code='.rawurlencode($code)); }
