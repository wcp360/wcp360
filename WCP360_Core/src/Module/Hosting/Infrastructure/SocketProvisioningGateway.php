<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;
use Wcp360\Core\Module\Hosting\Domain\ProvisioningGateway;
final class SocketProvisioningGateway implements ProvisioningGateway
{
    public function send(array $request):string
    {
        if(PHP_OS_FAMILY!=='Linux'||getenv('WCP_ISOLATED_TEST_CONTAINER')!=='1'||!is_file('/.dockerenv'))throw new \RuntimeException('Provisioning requires the isolated Linux test container.');
        $socket=@stream_socket_client('unix:///run/wcp360-provision-candidate/public/agent.sock',$errno,$error,3);
        if($socket===false)throw new \RuntimeException('Candidate agent unavailable.');
        try{
            stream_set_timeout($socket,20);
            $wire=json_encode($request,JSON_THROW_ON_ERROR);
            $offset=0;
            while($offset<strlen($wire)){
                $written=fwrite($socket,substr($wire,$offset));
                if($written===false||$written===0)throw new \RuntimeException('Uncertain dispatch.');
                $offset+=$written;
            }
            if(!stream_socket_shutdown($socket,STREAM_SHUT_WR))throw new \RuntimeException('Uncertain dispatch.');
            $raw=stream_get_contents($socket,4097);
            if($raw===false||strlen($raw)>4096||stream_get_meta_data($socket)['timed_out'])throw new \RuntimeException('Uncertain response.');
            $reply=json_decode($raw,true,8,JSON_THROW_ON_ERROR);
            if(!is_array($reply)||array_keys($reply)!==['schema','job_id','site_id','status','replayed']||$reply['schema']!==$request['schema']||$reply['job_id']!==$request['job_id']||$reply['site_id']!==$request['site_id']||!is_bool($reply['replayed'])||!in_array($reply['status'],['ACTIVE','REJECTED','UNKNOWN_OUTCOME'],true))throw new \RuntimeException('Candidate response rejected.');
            return $reply['status'];
        }finally{fclose($socket);}
    }
}
