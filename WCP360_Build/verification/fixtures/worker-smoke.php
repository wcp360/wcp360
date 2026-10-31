<?php
declare(strict_types=1);
// Deliberately small compatible application. The application owns per-request cleanup.
if (!function_exists('frankenphp_handle_request')) throw new RuntimeException('Worker SAPI required.');
$requests=0;
$handler=static function()use(&$requests):void{
    ++$requests;
    $previous=$GLOBALS['request_marker']??null;
    try {
        $GLOBALS['request_marker']=$_SERVER['HTTP_X_REQUEST_MARKER']??'none';
        header('Content-Type: application/json');
        echo json_encode([
            'requests'=>$requests,
            'pid'=>getmypid(),
            'marker'=>$GLOBALS['request_marker'],
            'previous'=>$previous,
            'neighbor_readable'=>is_readable('/srv/wcp360-candidate/neighbor/private.txt'),
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
