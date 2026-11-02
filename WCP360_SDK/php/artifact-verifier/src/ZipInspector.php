<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

/** Conservative ZIP admission profile. Reads streams; never extracts or executes. */
final class ZipInspector
{
    public function __construct(private readonly int $maxEntries=5000,private readonly int $maxExpanded=268435456,private readonly int $maxEntry=67108864,private readonly int $maxRatio=200)
    {
        if($maxEntries<1 || $maxEntries>5000 || $maxExpanded<1 || $maxExpanded>268435456 || $maxEntry<1 || $maxEntry>67108864 || $maxRatio<1 || $maxRatio>200) throw new ArtifactException('invalid_zip_limits');
    }
    public function inspect(string $path):array
    {
        if(!extension_loaded('zip')) throw new ArtifactException('zip_required');
        if(str_contains($path,'://') || str_contains($path,"\0") || !is_file($path)) throw new ArtifactException('local_archive_required');
        clearstatcache(true,$path);$size=filesize($path);
        if($size===false || $size<22 || $size>Statement::MAX_ARCHIVE) throw new ArtifactException('archive_size_limit');
        $this->container($path,$size);
        $zip=new \ZipArchive();
        if(@$zip->open($path,\ZipArchive::RDONLY|\ZipArchive::CHECKCONS)!==true) throw new ArtifactException('invalid_zip');
        try {
            if($zip->numFiles<1 || $zip->numFiles>$this->maxEntries) throw new ArtifactException('zip_entry_limit');
            $nodes=[];$expanded=0;$files=0;
            for($i=0;$i<$zip->numFiles;$i++) {
                $stat=$zip->statIndex($i);
                if(!$stat || !is_string($stat['name'] ?? null)) throw new ArtifactException('invalid_zip_entry');
                $name=$stat['name'];$directory=str_ends_with($name,'/');
                $this->path($name,$directory,$nodes);
                $opsys=0;$attributes=0;
                if(!$zip->getExternalAttributesIndex($i,$opsys,$attributes)) throw new ArtifactException('missing_zip_attributes');
                if($opsys===\ZipArchive::OPSYS_UNIX) {
                    $mode=($attributes>>16)&0xffff;$kind=$mode&0170000;
                    if(($mode&07000)!==0 || !in_array($kind,[0,0100000,0040000],true) || ($kind===0040000 && !$directory) || ($kind===0100000 && $directory)) throw new ArtifactException('unsafe_zip_entry_type');
                }
                $bytes=$stat['size'] ?? null;$compressed=$stat['comp_size'] ?? null;
                if(!is_int($bytes) || !is_int($compressed) || $bytes<0 || $compressed<0 || $bytes>$this->maxEntry || $bytes>max(1,$compressed)*$this->maxRatio || !in_array($stat['comp_method'] ?? null,[\ZipArchive::CM_STORE,\ZipArchive::CM_DEFLATE],true) || ($stat['encryption_method'] ?? 0)!==0) throw new ArtifactException('zip_entry_policy');
                $expanded+=$bytes;
                if($expanded>$this->maxExpanded) throw new ArtifactException('zip_expanded_limit');
                if($directory) {if($bytes!==0) throw new ArtifactException('invalid_zip_directory');continue;}
                $files++;$stream=@$zip->getStreamIndex($i);
                if(!is_resource($stream)) throw new ArtifactException('zip_stream_unavailable');
                try {
                    $read=0;$crc=hash_init('crc32b');
                    while(!feof($stream)) {
                        $chunk=@fread($stream,65536);
                        if($chunk===false || ($chunk==='' && !feof($stream))) throw new ArtifactException('zip_stream_failed');
                        $read+=strlen($chunk);
                        if($read>$bytes || $read>$this->maxEntry) throw new ArtifactException('zip_actual_size_limit');
                        hash_update($crc,$chunk);
                    }
                    if($read!==$bytes || hash_final($crc)!==sprintf('%08x',($stat['crc'] ?? -1)&0xffffffff)) throw new ArtifactException('zip_crc_mismatch');
                } finally {fclose($stream);}
            }
            if($files===0) throw new ArtifactException('empty_zip');
            return ['entries'=>$zip->numFiles,'files'=>$files,'expanded_size'=>$expanded];
        } finally {$zip->close();}
    }
    private function path(string $name,bool $directory,array &$nodes):void
    {
        if($name==='' || strlen($name)>512 || str_starts_with($name,'/') || preg_match('/[^\x20-\x7e]|[<>:"\\\\|?*]/',$name)) throw new ArtifactException('unsafe_zip_path');
        $parts=explode('/',$directory ? substr($name,0,-1) : $name);
        if(count($parts)>16) throw new ArtifactException('zip_path_depth');
        $prefix='';
        foreach($parts as $index=>$part) {
            if($part==='' || $part==='.' || $part==='..' || strlen($part)>240 || str_ends_with($part,'.') || str_ends_with($part,' ') || preg_match('/^(?:con|prn|aux|nul|com[0-9]|lpt[0-9]|conin\$|conout\$|clock\$)(?:\.|$)/iD',$part)) throw new ArtifactException('unsafe_zip_path');
            $prefix.=($prefix===''?'':'/').$part;$key=strtolower($prefix);$last=$index===count($parts)-1;
            $kind=$last&&!$directory ? 'file' : 'directory';
            if(isset($nodes[$key])) {
                $old=$nodes[$key];
                if($old['spelling']!==$prefix || $old['kind']==='file' || $kind==='file' || ($last && $old['explicit'])) throw new ArtifactException('zip_path_collision');
            }
            $nodes[$key]=['spelling'=>$prefix,'kind'=>$kind,'explicit'=>$last || ($nodes[$key]['explicit'] ?? false)];
        }
    }
    private function container(string $path,int $size):void
    {
        $file=@fopen($path,'rb');if(!$file) throw new ArtifactException('archive_unreadable');
        try {
            if(fread($file,4)!=="PK\x03\x04") throw new ArtifactException('unsupported_zip_container');
            $length=min($size,65557);fseek($file,$size-$length);$tail=fread($file,$length);
            $pos=strrpos($tail,"PK\x05\x06");
            if($pos===false || strlen($tail)-$pos<22) throw new ArtifactException('invalid_zip_container');
            $end=unpack('vdisk/vcd_disk/ventries_disk/ventries/Vcd_size/Vcd_offset/vcomment',substr($tail,$pos+4,18));
            if($end['disk']!==0 || $end['cd_disk']!==0 || $end['entries_disk']!==$end['entries'] || $end['entries']>5000 || $end['entries']<1 || $end['cd_offset']+$end['cd_size']!==$size-$length+$pos || $pos+22+$end['comment']!==strlen($tail)) throw new ArtifactException('unsupported_zip_container');
        } finally {fclose($file);}
    }
}
