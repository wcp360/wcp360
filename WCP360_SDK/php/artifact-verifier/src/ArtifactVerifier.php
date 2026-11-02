<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

/** Offline verification result is metadata only; it does not authorize installation. */
final class ArtifactVerifier
{
    public function __construct(private readonly EnvelopeVerifier $envelopes,private readonly ZipInspector $zip=new ZipInspector()) {}
    public function verify(string $archive,string $envelope,array $expected):array
    {
        $result=$this->envelopes->verify($envelope,$expected);$meta=$result['statement']['artifact'];
        if(str_contains($archive,'://') || str_contains($archive,"\0") || is_link($archive) || !is_file($archive)) throw new ArtifactException('local_archive_required');
        $file=@fopen($archive,'rb');if(!$file) throw new ArtifactException('archive_unreadable');
        try {
            $stat=fstat($file);
            if(!$stat || ($stat['mode']&0170000)!==0100000 || $stat['size']!==$meta['size']) throw new ArtifactException('artifact_size_mismatch');
            $hash=hash_init('sha256');$bytes=0;
            while(!feof($file)) {
                $chunk=@fread($file,1048576);
                if($chunk===false || ($chunk===''&&!feof($file))) throw new ArtifactException('archive_read_failed');
                $bytes+=strlen($chunk);
                if($bytes>$meta['size']) throw new ArtifactException('artifact_size_mismatch');
                hash_update($hash,$chunk);
            }
            if($bytes!==$meta['size'] || !hash_equals($meta['sha256'],hash_final($hash))) throw new ArtifactException('artifact_digest_mismatch');
        } finally {fclose($file);}
        return $result+['zip'=>$this->zip->inspect($archive)];
    }
}
