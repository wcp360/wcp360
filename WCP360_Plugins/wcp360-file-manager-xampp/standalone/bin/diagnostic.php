<?php
declare(strict_types=1);
if (PHP_SAPI!=='cli') { http_response_code(404); exit; }
$errors=[];
echo "PHP : ".PHP_VERSION." (".PHP_OS_FAMILY.")\n";
echo "Configuration PHP : ".(php_ini_loaded_file() ?: 'aucune')."\n";
if (PHP_VERSION_ID<80200) { $errors[]='PHP 8.2 minimum requis. Votre version de PHP est trop ancienne.'; }
foreach (['json','fileinfo','session','ctype','iconv','filter','xml'] as $ext) {
    if (!extension_loaded($ext)) { $errors[]='Extension PHP manquante : '.$ext.'. Activez-la dans le php.ini indique ci-dessus.'; }
}
$base=dirname(__DIR__,2);
if (!is_file($base.'/vendor/autoload.php')) { $errors[]='Dossier vendor absent. Extrayez entierement le nouveau ZIP, pas seulement src ou standalone.'; }
if (!is_writable($base.'/standalone')) { $errors[]='Le dossier standalone doit etre accessible en ecriture.'; }
$errno=0;$error='';
$socket=@stream_socket_server('tcp://127.0.0.1:8088',$errno,$error);
if ($socket===false) { $errors[]='Le port 8088 est occupe ou indisponible. Fermez une ancienne fenetre File Manager. Aucun processus ne sera arrete automatiquement.'; }
else { fclose($socket); }
if ($errors) { foreach($errors as $message) { fwrite(STDERR,'ERREUR : '.$message."\n"); } exit(1); }
require $base.'/vendor/autoload.php';
echo "Dependances presentes. Diagnostic reussi.\n";
