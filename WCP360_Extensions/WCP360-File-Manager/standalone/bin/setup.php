<?php
declare(strict_types=1);
if (PHP_SAPI!=='cli') { exit(1); }
if (PHP_VERSION_ID<80200) { fwrite(STDERR,"PHP 8.2 minimum requis.\n"); exit(1); }
umask(0077);
$var=dirname(__DIR__).'/var';
if (is_file($var.'/config.php')) { fwrite(STDERR,"Configuration déjà présente : aucune modification.\n"); exit(1); }
foreach ([$var,$var.'/files',$var.'/state'] as $dir) { if (!is_dir($dir) && !mkdir($dir,0700,true)) { throw new RuntimeException('Création impossible.'); } }
fwrite(STDOUT,'Identifiant administrateur [admin] : ');
$user=trim(fgets(STDIN) ?: '') ?: 'admin';
if (!preg_match('/^[a-zA-Z0-9_.@-]{1,100}$/D',$user)) { throw new RuntimeException('Identifiant invalide.'); }
// Generate a strong password instead of reading and echoing an interactive password.
$password=bin2hex(random_bytes(16));
$config=['APP_SECRET'=>bin2hex(random_bytes(32)),'FM_USERNAME'=>$user,'FM_PASSWORD_HASH'=>password_hash($password,PASSWORD_DEFAULT),'FM_ROOT'=>$var.'/files','FM_STATE'=>$var.'/state'];
if (file_put_contents($var.'/config.php',"<?php\nreturn ".str_replace([var_export($var.'/files',true),var_export($var.'/state',true)], ["__DIR__.'/files'","__DIR__.'/state'"],var_export($config,true)).";\n")===false) { throw new RuntimeException('Écriture impossible.'); }
chmod($var.'/config.php',0600);
echo "Installation prête. Conservez ce mot de passe dans votre gestionnaire :\n$password\n";
echo "Démarrage local : php -S 127.0.0.1:8088 -t standalone/public standalone/public/index.php\n";
