<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function(ContainerConfigurator $c): void {
    $c->extension('framework',[
        'secret'=>'%env(APP_SECRET)%','csrf_protection'=>true,
        'session'=>['handler_id'=>null,'cookie_httponly'=>true,'cookie_samesite'=>'strict','cookie_secure'=>'auto','name'=>'FMSESSID'],
        'router'=>['utf8'=>true], 'http_method_override'=>false,
    ]);
    $c->extension('security',[
        'password_hashers'=>[Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface::class=>'auto'],
        'providers'=>['local'=>['memory'=>['users'=>[
            '%env(FM_USERNAME)%'=>['password'=>'%env(FM_PASSWORD_HASH)%','roles'=>['ROLE_FILE_MANAGER','ROLE_FILE_MANAGER_WRITE','ROLE_FILE_MANAGER_ADMIN']]
        ]]]],
        'firewalls'=>['main'=>[
            'lazy'=>true,'provider'=>'local','login_throttling'=>['max_attempts'=>5,'interval'=>'1 minute'],
            'form_login'=>['login_path'=>'fm_login','check_path'=>'fm_login','enable_csrf'=>true,'default_target_path'=>'fm_home','always_use_default_target_path'=>true],
            'logout'=>['path'=>'fm_logout','target'=>'fm_login','enable_csrf'=>true],
        ]],
        'access_control'=>[
            ['path'=>'^/s/[a-f0-9]{64}$','roles'=>'PUBLIC_ACCESS'],
            ['path'=>'^/login$','roles'=>'PUBLIC_ACCESS'],
            ['path'=>'^/','roles'=>'ROLE_FILE_MANAGER'],
        ],
    ]);
    $c->extension('monolog',['channels'=>['file_manager'],'handlers'=>['main'=>['type'=>'stream','path'=>'%kernel.logs_dir%/audit.jsonl','level'=>'info','channels'=>['file_manager'],'formatter'=>'monolog.formatter.json']]]);
};
