<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST') reject('method');
if(!same_origin_ok($config)||!honeypot_ok()||!csrf_ok()) reject('security');
if(!rate_limit_ok('contact',$config)) reject('rate_limit');
$name=text_value('name',160); $email=text_value('email',254); $phone=text_value('phone',40); $message=text_value('message',6000);
if(mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || !phone_ok($phone) || mb_strlen($message)<10) reject('validation');
$body="NOUVEAU CONTACT NETTOYAGE 360\n\nNom / société / copropriété : $name\nEmail : $email\nTéléphone : $phone\n\nMessage :\n$message\n\nReçu le : ".date('c')."\n";
$ok=send_mail_with_attachments($config,'Contact depuis le site',$body,$email,[]); rotate_csrf();
if(!$ok) reject('mail'); redirect_result('../merci?type=contact');
