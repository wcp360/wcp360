<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;
use Symfony\Component\Process\Process;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
final readonly class GoPlanRenderer
{
    public function __construct(private string $binary) {}
    /** @return array<string, string> */
    public function render(string $siteId,string $host,ApplicationServer $server=ApplicationServer::PhpFpm,bool $acknowledged=false): array
    {
        if(!is_file($this->binary)) throw new \RuntimeException('Build the candidate planner first.');
        $cleanEnv=array_fill_keys(array_keys(array_merge(getenv()?:[],$_ENV,$_SERVER)),false);
        foreach(['SystemRoot','SYSTEMROOT','WINDIR','ComSpec','TEMP','TMP']as $name){$value=getenv($name);if(is_string($value))$cleanEnv[$name]=$value;}
        $input=['site_id'=>$siteId,'hostname'=>$host];
        if($server===ApplicationServer::FrankenPhpWorker){$input['application_server']=$server->value;$input['worker_acknowledged']=$acknowledged;}
        $process=new Process([$this->binary,'plan'],null,$cleanEnv,json_encode($input,JSON_THROW_ON_ERROR),5.0);
        $output='';$process->disableOutput();
        $process->run(static function(string $type,string $data)use(&$output):void{
            if($type!==Process::OUT)return;
            if(strlen($output)+strlen($data)>65536)throw new \RuntimeException('Planner output limit exceeded.');
            $output.=$data;
        });
        if(!$process->isSuccessful()) throw new \RuntimeException('Planner failed. No system effect was dispatched.');
        return \Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument::decode($output,$server)->fields();
    }
}
