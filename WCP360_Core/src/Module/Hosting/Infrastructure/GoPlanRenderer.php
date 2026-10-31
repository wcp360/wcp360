<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;
use Symfony\Component\Process\Process;
final readonly class GoPlanRenderer
{
    public function __construct(private string $binary) {}
    public function render(string $siteId,string $host): array
    {
        if(!is_file($this->binary)) throw new \RuntimeException('Build the candidate planner first.');
        $cleanEnv=array_fill_keys(array_keys(array_merge(getenv()?:[],$_ENV,$_SERVER)),false);
        foreach(['SystemRoot','SYSTEMROOT','WINDIR','ComSpec','TEMP','TMP']as $name){$value=getenv($name);if(is_string($value))$cleanEnv[$name]=$value;}
        $process=new Process([$this->binary,'plan'],null,$cleanEnv,json_encode(['site_id'=>$siteId,'hostname'=>$host],JSON_THROW_ON_ERROR),5.0);
        $output='';$process->disableOutput();
        $process->run(static function(string $type,string $data)use(&$output):void{
            if($type!==Process::OUT)return;
            if(strlen($output)+strlen($data)>65536)throw new \RuntimeException('Planner output limit exceeded.');
            $output.=$data;
        });
        if(!$process->isSuccessful()) throw new \RuntimeException('Planner failed. No system effect was dispatched.');
        $plan=json_decode($output,true,16,JSON_THROW_ON_ERROR);
        if(!is_array($plan) || array_keys($plan)!==['schema','nginx','php_fpm'] || $plan['schema']!=='wcp360.candidate.plan/1') throw new \RuntimeException('Planner output rejected.');
        return $plan;
    }
}
