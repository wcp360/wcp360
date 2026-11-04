<?php
declare(strict_types=1);
// Deliberately small compatible application. The application owns per-request cleanup.
if (!function_exists('frankenphp_handle_request')) throw new RuntimeException('Worker SAPI required.');
$coreProbe=require '/opt/wcp360/core-boundary-worker.php';
$requests=0;
$handler=static function()use(&$requests,$coreProbe):void{
    ++$requests;
    $previous=$GLOBALS['request_marker']??null;
    try {
        $GLOBALS['request_marker']=$_SERVER['HTTP_X_REQUEST_MARKER']??'none';
        $core=null;
        if(preg_match('#^/core/(user|admin)/(normal|forged|early-error|subrequest)$#D',$_SERVER['REQUEST_URI']??'',$match)===1){
            $core=$coreProbe($match[1],$match[2]);
            http_response_code($core['status']);
            foreach($core['headers'] as $name=>$value){
                if($value!==null)header($name.': '.$value);
            }
        }
        preg_match('/^Uid:\s+([0-9]+)\s+([0-9]+)/m',(string)file_get_contents('/proc/self/status'),$uid);
        header('Content-Type: application/json');
        echo json_encode([
            'requests'=>$requests,
            'pid'=>getmypid(),
            'euid'=>(int)($uid[2]??0),
            'marker'=>$GLOBALS['request_marker'],
            'previous'=>$previous,
            'neighbor_readable'=>is_readable('/srv/wcp360-candidate/neighbor/private.txt'),
            'core'=>$core,
        ],JSON_THROW_ON_ERROR);
    } finally {
        unset($GLOBALS['request_marker']);
    }
};
$max=(int)($_SERVER['MAX_REQUESTS']??getenv('MAX_REQUESTS')?:500);
for($i=0;$i<$max;++$i){
    $continue=frankenphp_handle_request($handler);
    gc_collect_cycles();
    if(!$continue)break;
}
