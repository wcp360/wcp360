<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST') reject('method');
if(!same_origin_ok($config)||!honeypot_ok()||!csrf_ok()) reject('security');
if(!rate_limit_ok('devis',$config)) reject('rate_limit');
$profile=text_value('profile',80); $service=text_value('service',180); $surface=text_value('surface',80); $frequency=text_value('frequency',100); $address=text_value('address',300); $date=text_value('date',30); $name=text_value('name',160); $phone=text_value('phone',40); $email=text_value('email',254); $details=text_value('details',7000); $landingContext=text_value('landing_context',700);
$profileIsValid=$profile==='' || allowed_value($profile,$config['profiles']);
$addressIsValid=$address==='' || mb_strlen($address)>=2;
$dateIsValid=$date==='' || valid_iso_date($date);
if(!$profileIsValid||!allowed_value($service,$config['services'])||!$addressIsValid||mb_strlen($name)<2||!phone_ok($phone)||!filter_var($email,FILTER_VALIDATE_EMAIL)||!$dateIsValid) reject('validation');
if($frequency!=='' && !allowed_value($frequency,$config['frequencies'])) reject('frequency');
$attachments=[]; $total=0; $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
if(isset($_FILES['photos']) && is_array($_FILES['photos']['name'])){
    $n=count($_FILES['photos']['name']); if($n>(int)$config['max_upload_files']) reject('files_count'); $finfo=new finfo(FILEINFO_MIME_TYPE);
    for($i=0;$i<$n;$i++){
        $err=$_FILES['photos']['error'][$i]??UPLOAD_ERR_NO_FILE; if($err===UPLOAD_ERR_NO_FILE) continue; if($err!==UPLOAD_ERR_OK) reject('upload');
        $size=(int)($_FILES['photos']['size'][$i]??0); if($size<1||$size>(int)$config['max_upload_bytes_each']) reject('file_size'); $total+=$size; if($total>(int)$config['max_upload_bytes_total']) reject('files_total');
        $tmp=(string)($_FILES['photos']['tmp_name'][$i]??''); if(!is_uploaded_file($tmp)) reject('upload_source'); $mime=$finfo->file($tmp)?:''; if(!isset($allowed[$mime])) reject('file_type'); $image=@getimagesize($tmp); if($image===false) reject('image_invalid'); $width=(int)($image[0]??0); $height=(int)($image[1]??0); if($width<1||$height<1||($width*$height)>(int)$config['max_upload_pixels']) reject('image_dimensions');
        $data=file_get_contents($tmp); if($data===false) reject('file_read'); $attachments[]=['name'=>'photo-'.(count($attachments)+1).'.'.$allowed[$mime],'mime'=>$mime,'data'=>$data];
    }
}
$body="NOUVELLE DEMANDE DE DEVIS NETTOYAGE 360\n\nProfil : ".($profile?:'—')."\nType de service : $service\nSuperficie : ".($surface?:'—')."\nFréquence : ".($frequency?:'—')."\nVille / arrondissement : ".($address?:'—')."\nDate souhaitée : ".($date?:'—')."\nNom complet / société : $name\nTéléphone : $phone\nEmail : $email\n\nDescription du besoin :\n".($details?:'—')."\n\nOrigine de la demande : ".($landingContext?:'Formulaire devis principal')."\nPhotos jointes : ".count($attachments)."\nReçu le : ".date('c')."\n";
$ok=send_mail_with_attachments($config,'Demande de devis — '.$service,$body,$email,$attachments); rotate_csrf();
if(!$ok) reject('mail'); redirect_result('../devis-envoye');
