<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Infrastructure;
use Wcp360\FileManager\Contract\{FileError,Identity};
/** Optional tools for exclusively owned local storage. No shell, no privileged tenant executor. */
trait ProFeatures
{
    private function jsonState(string $name,array $default=[]): array
    { $file=$this->state.'/'.$name.'.json'; return is_file($file)?json_decode(file_get_contents($file),true,64,JSON_THROW_ON_ERROR):$default; }
    private function storeState(string $name,array $data): void
    {
        $tmp=$this->state.'/'.$name.'.tmp';$bytes=json_encode($data,JSON_THROW_ON_ERROR);
        if(file_put_contents($tmp,$bytes)!==strlen($bytes)||!rename($tmp,$this->state.'/'.$name.'.json'))throw new FileError('IO_ERROR','Écriture des métadonnées impossible.',500);
    }
    /** No following links. Measurements are bounded; state is not browsable by the client. */
    private function measure(string $root): array
    {
        $bytes=0;$count=0;$blocked=0;$top=[];
        $it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root,\FilesystemIterator::SKIP_DOTS));
        foreach($it as $f){if(++$count>20000)throw new FileError('SCAN_LIMIT','Analyse limitée à 20 000 entrées.',413);
            if($f->isLink()||!$f->isFile()){$blocked++;continue;}
            $length=$f->getSize();$bytes+=$length;$rel=str_replace('\\','/',substr($f->getPathname(),strlen($root)+1));
            $first=explode('/',$rel)[0];$top[$first]=($top[$first]??0)+$length;
        }
        arsort($top);$largest=[];foreach(array_slice($top,0,20,true) as $name=>$length)$largest[]=['name'=>(string)$name,'bytes'=>$length];
        return ['bytes'=>$bytes,'files'=>$count,'blocked'=>$blocked,'largest'=>$largest];
    }
    private function diskUsage(array $input=[]): array
    {
        $analysisPath=$input['path']??'';if(!is_string($analysisPath))throw new FileError('INVALID_INPUT','Chemin invalide.');
        $directory=$this->path($analysisPath);if(!is_dir($directory))throw new FileError('NOT_A_DIRECTORY','Dossier requis.');
        $files=$this->measure($this->root);$state=$this->measure($this->state);$quota=$this->jsonState('quota')['bytes']??0;
        $used=$files['bytes']+$state['bytes'];$ratio=$quota>0?round($used/$quota*100,1):null;
        return ['used'=>$used,'files_bytes'=>$files['bytes'],'private_state_bytes'=>$state['bytes'],'quota'=>$quota,'percent'=>$ratio,
            'alert'=>$ratio===null?'unconfigured':($ratio>=100?'exceeded':($ratio>=85?'warning':'ok')),
            'analysis_path'=>$analysisPath,'largest'=>($analysisPath===''?$files:$this->measure($directory))['largest'],'file_count'=>$files['files'],'blocked'=>$files['blocked'],'measured_at'=>time(),
            'scope'=>'Fichiers + corbeille + versions + métadonnées privées. Quota applicatif local, pas un quota Linux.'];
    }
    private function assertCapacity(int $additional): void
    {
        $quota=$this->jsonState('quota')['bytes']??0;if($quota<=0)return;
        $used=$this->measure($this->root)['bytes']+$this->measure($this->state)['bytes'];
        if($used+$additional+65536>$quota)throw new FileError('QUOTA_EXCEEDED','Quota applicatif atteint (marge métadonnées de 64 Kio).',413);
    }
    private function quotaSet(array $a): array
    {
        $bytes=$a['bytes']??null;
        if(!is_int($bytes)||$bytes<0||($bytes>0&&$bytes<1048576))throw new FileError('INVALID_INPUT','Quota : 0 pour désactiver, ou au moins 1 Mio.');
        $this->storeState('quota',['bytes'=>$bytes]);return $this->diskUsage();
    }
    private function permissions(array $a): array
    {
        if(PHP_OS_FAMILY==='Windows')throw new FileError('UNSUPPORTED_PLATFORM','CHMOD Linux indisponible sous Windows.',409);
        $rel=$this->str($a,'path');$root=$this->mutable($rel);$mode=$this->str($a,'mode');
        if(!preg_match('/^[0-7]{3}$/D',$mode))throw new FileError('INVALID_MODE','Utilisez trois chiffres octaux, sans bits privilégiés.');
        $bits=octdec($mode);if(($bits&0600)!==0600||($bits&0002)!==0)throw new FileError('MODE_NOT_ALLOWED','Conserver lecture/écriture propriétaire ; écriture publique interdite.',403);
        $recursive=$a['recursive']??false;if(!is_bool($recursive))throw new FileError('INVALID_INPUT','recursive doit être booléen.');
        $entries=$recursive?$this->inventory($rel)['entries']:[['path'=>$rel,'directory'=>is_dir($root)]];
        $target=$a['target']??'all';if(!in_array($target,['all','files','directories'],true))throw new FileError('INVALID_INPUT','Cible invalide.');
        $entries=array_filter($entries,fn($e)=>$target==='all'||($target==='files'&&!$e['directory'])||($target==='directories'&&$e['directory']));
        $changes=[];
        foreach($entries as $entry){$path=$this->path($entry['path']);if($entry['directory']&&($bits&0100)===0)throw new FileError('MODE_NOT_ALLOWED','Un dossier doit garder la traversée propriétaire.',403);$changes[]=['path'=>$path,'old'=>fileperms($path)&0777];}
        $done=[];
        foreach($changes as $entry){if(!@chmod($entry['path'],$bits)){
            $rollback=true;foreach(array_reverse($done) as $past)$rollback=@chmod($past['path'],$past['old'])&&$rollback;
            throw new FileError($rollback?'CHMOD_FAILED':'CHMOD_ROLLBACK_INCOMPLETE',$rollback?'Modification impossible ; permissions précédentes restaurées.':'Modification partielle ; contrôle administrateur nécessaire.',500);
        }$done[]=$entry;}
        clearstatcache(true);return ['path'=>$rel,'mode'=>$mode,'changed'=>count($done),'recursive'=>$recursive];
    }
    private function searchContents(array $a): array
    {
        $base=$this->str($a,'path');$root=$this->path($base);if(!is_dir($root))throw new FileError('NOT_A_DIRECTORY','Dossier requis.');
        $query=$this->str($a,'query');if(strlen($query)<2||strlen($query)>200||str_contains($query,"\0")||!preg_match('//u',$query))throw new FileError('INVALID_INPUT','Recherchez de 2 à 200 octets de texte.');
        $matches=[];$skipped=0;$scanned=0;$bytes=0;$stack=[$base];$truncated=false;
        while($stack){$rel=array_pop($stack);if(++$scanned>5000){$truncated=true;break;}
            try{$path=$this->path($rel);}catch(FileError){$skipped++;continue;}
            if(is_dir($path)){
                $children=scandir($path);if($children===false)continue;
                foreach($children as $name){if($name==='.'||$name==='..')continue;if(count($stack)>=5000){$truncated=true;break;}$stack[]=($rel===''?'':$rel.'/').$name;}continue;
            }
            if(filesize($path)>self::MAX_EDIT){$skipped++;continue;}
            $bytes+=filesize($path);if($bytes>20971520){$truncated=true;break;}
            $text=file_get_contents($path);if($text===false||str_contains($text,"\0")||!preg_match('//u',$text)){$skipped++;continue;}
            foreach(explode("\n",$text) as $line=>$content){if(mb_stripos($content,$query,0,'UTF-8')!==false){$matches[]=['path'=>$rel,'line'=>$line+1];if(count($matches)>=200){$truncated=true;break 2;}}}
        }
        // No snippets: matches in secrets must not be duplicated into a results panel or audit.
        return ['matches'=>$matches,'scanned'=>$scanned,'skipped'=>$skipped,'truncated'=>$truncated,'bytes_scanned'=>$bytes];
    }
    private function versionDir(string $relative): string { return $this->state.'/versions/'.hash('sha256',$relative); }
    private function saveVersion(string $rel,string $path): void
    {
        $dir=$this->versionDir($rel);if(!is_dir($dir)&&!mkdir($dir,0700,true))throw new FileError('IO_ERROR','Historique indisponible.',500);
        $id=bin2hex(random_bytes(16));$meta=['id'=>$id,'path'=>$rel,'created_at'=>microtime(true),'size'=>filesize($path),'revision'=>$this->revision($path)];
        if(!copy($path,$dir.'/'.$id.'.data'))throw new FileError('IO_ERROR','Sauvegarde de version impossible.',500);
        if(file_put_contents($dir.'/'.$id.'.json',json_encode($meta,JSON_THROW_ON_ERROR))===false)throw new FileError('IO_ERROR','Historique indisponible.',500);
        $records=$this->versions($rel)['items'];foreach(array_slice($records,20) as $old){unlink($dir.'/'.$old['id'].'.data');unlink($dir.'/'.$old['id'].'.json');}
    }
    private function versions(string $rel): array
    {
        $this->relative($rel);$items=[];foreach(glob($this->versionDir($rel).'/*.json')?:[] as $file){$meta=json_decode(file_get_contents($file),true,32,JSON_THROW_ON_ERROR);if(is_file(substr($file,0,-5).'.data'))$items[]=$meta;}
        usort($items,fn($a,$b)=>($b['created_at']<=>$a['created_at'])?:strcmp($b['id'],$a['id']));return ['items'=>$items];
    }
    private function restoreVersion(array $a): array
    {
        $rel=$this->str($a,'path');$this->editable($rel);$id=$this->str($a,'id');if(!preg_match('/^[a-f0-9]{32}$/D',$id))throw new FileError('INVALID_INPUT','Version invalide.');
        $file=$this->versionDir($rel).'/'.$id.'.data';if(!is_file($file))throw new FileError('NOT_FOUND','Version introuvable.',404);
        return $this->write(['path'=>$rel,'content'=>file_get_contents($file),'revision'=>$this->str($a,'revision')],'save');
    }
    private function snapshot(array $a): array
    {
        if(!class_exists(\ZipArchive::class))throw new FileError('ZIP_UNAVAILABLE','Activez l’extension PHP zip pour créer un instantané.',409);
        $rel=$this->str($a,'path');$this->mutable($rel);$inv=$this->inventory($rel);$this->assertCapacity($inv['bytes']);$tmp=tempnam($this->state,'snapshot-');$zip=new \ZipArchive();
        try{
            if($zip->open($tmp,\ZipArchive::OVERWRITE)!==true)throw new FileError('ZIP_FAILED','Création ZIP impossible.',500);
            foreach($inv['entries'] as $e){$name=basename($rel).substr($e['path'],strlen($rel));$ok=$e['directory']?$zip->addEmptyDir($name):$zip->addFile($this->path($e['path']),$name);if(!$ok){$zip->close();throw new FileError('ZIP_FAILED','Ajout ZIP impossible.',500);}}
            if(!$zip->close())throw new FileError('ZIP_FAILED','Finalisation ZIP impossible.',500);
            if(filesize($tmp)>self::MAX_FILE)throw new FileError('FILE_TOO_LARGE','Instantané compressé limité à 20 Mio.',413);
            return ['name'=>basename($rel).'-snapshot.zip','content'=>file_get_contents($tmp)];
        }finally{if(is_file($tmp))unlink($tmp);}
    }
    private function activeLocks(): array
    { return array_filter($this->jsonState('file-locks'),fn($v)=>$v['expires_at']>time()); }
    private function guardLocks(array $a): void
    {
        $path=$a['path']??null;if(!is_string($path))return;
        foreach($this->activeLocks() as $key=>$lock){if($key===$path||str_starts_with($key,$path.'/')){
            if(!is_string($a['lock_token']??null)||!hash_equals($lock['token_hash'],hash('sha256',$a['lock_token'])))throw new FileError('FILE_LOCKED','Fichier verrouillé dans un autre éditeur. Réessayez après sa libération.',423);
        }}
        $dest=$a['destination']??null;if(is_string($dest))foreach($this->activeLocks() as $key=>$lock)if($key===$dest||str_starts_with($key,$dest.'/'))throw new FileError('FILE_LOCKED','Destination verrouillée.',423);
    }
    private function fileLock(Identity $id,string $op,array $a): array
    {
        $rel=$this->str($a,'path');$path=$this->mutable($rel);if(!is_file($path))throw new FileError('NOT_A_FILE','Verrouillage réservé aux fichiers.');
        $locks=$this->activeLocks();$current=$locks[$rel]??null;
        if($op==='lock_acquire'){
            if($current)throw new FileError('FILE_LOCKED','Ce fichier possède déjà un verrou actif.',423);
            if(count($locks)>=500)throw new FileError('LOCK_LIMIT','Trop de verrous actifs.',413);
            $token=bin2hex(random_bytes(32));$locks[$rel]=['subject'=>$id->subjectId,'token_hash'=>hash('sha256',$token),'expires_at'=>time()+900];$this->storeState('file-locks',$locks);return ['token'=>$token,'expires_at'=>$locks[$rel]['expires_at']];
        }
        if(!$current||!hash_equals($current['token_hash'],hash('sha256',$this->str($a,'lock_token'))))throw new FileError('LOCK_TOKEN_INVALID','Jeton de verrou absent ou expiré.',409);
        if($op==='lock_release')unset($locks[$rel]);else $locks[$rel]['expires_at']=time()+900;
        $this->storeState('file-locks',$locks);return ['released'=>$op==='lock_release','expires_at'=>$locks[$rel]['expires_at']??null];
    }
    private function recordAudit(Identity $id,string $op,array $input,string $result): void
    {
        $ctx=is_array($input['_audit']??null)?$input['_audit']:[];$entry=['at'=>gmdate('c'),'subject'=>$id->subjectId,'tenant'=>$id->tenantId,'operation'=>$op,'result'=>$result,'ip'=>$ctx['ip']??null,'request_id'=>$ctx['request_id']??null];
        foreach(['path','destination','mode','id'] as $field)if(is_string($input[$field]??null))$entry[$field]=substr($input[$field],0,4096);
        $file=$this->state.'/audit.jsonl';if(is_file($file)&&filesize($file)>5242880){if(is_file($file.'.1'))unlink($file.'.1');rename($file,$file.'.1');}
        if(file_put_contents($file,json_encode($entry,JSON_THROW_ON_ERROR)."\n",FILE_APPEND)===false)throw new FileError('AUDIT_FAILED','Journal indisponible ; vérifiez le résultat de l’opération avant de réessayer.',500);
    }
    private function auditTrail(): array
    {
        $file=$this->state.'/audit.jsonl';if(!is_file($file))return ['items'=>[]];
        $lines=file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);$items=[];foreach(array_slice($lines?:[],-200) as $line)$items[]=json_decode($line,true,32,JSON_THROW_ON_ERROR);
        return ['items'=>array_reverse($items),'limit'=>200];
    }
    private function purgeTrash(string $id): array
    {
        if(!preg_match('/^[a-f0-9]{32}$/D',$id))throw new FileError('INVALID_INPUT','Identifiant de corbeille invalide.');
        $slot=$this->state.'/trash/'.$id;if(!is_file($slot.'/meta.json'))throw new FileError('NOT_FOUND','Élément introuvable.',404);
        $meta=json_decode(file_get_contents($slot.'/meta.json'),true,32,JSON_THROW_ON_ERROR);
        $this->removeStaging($slot);return ['purged'=>true,'path'=>$meta['path']];
    }
    private function shareCreate(array $a): array
    {
        $rel=$this->str($a,'path');$source=$this->mutable($rel);$this->revision($source);
        $password=$this->str($a,'password');$ttl=$a['ttl']??3600;
        if(!preg_match('//u',$password)||mb_strlen($password,'UTF-8')<12||strlen($password)>256||!is_int($ttl)||$ttl<60||$ttl>86400)throw new FileError('INVALID_INPUT','Mot de passe : 12 caractères minimum, 256 octets maximum ; durée de 60 secondes à 24 heures.');
        $active=$this->shareList()['items'];if(count($active)>=100)throw new FileError('SHARE_LIMIT','Maximum 100 liens actifs.',413);
        $this->assertCapacity(filesize($source));$token=bin2hex(random_bytes(32));$id=hash('sha256',$token);
        $dir=$this->state.'/shares';if(!is_dir($dir)&&!mkdir($dir,0700))throw new FileError('IO_ERROR','Partages indisponibles.',500);
        $meta=['id'=>$id,'path'=>$rel,'name'=>basename($rel),'created_at'=>time(),'expires_at'=>time()+$ttl,'password_hash'=>password_hash(hash('sha256',$password),PASSWORD_DEFAULT),'failures'=>0,'window'=>time(),'downloads'=>0];
        if(!copy($source,$dir.'/'.$id.'.data'))throw new FileError('IO_ERROR','Copie privée impossible.',500);
        if(file_put_contents($dir.'/'.$id.'.json',json_encode($meta,JSON_THROW_ON_ERROR))===false){unlink($dir.'/'.$id.'.data');throw new FileError('IO_ERROR','Partage impossible.',500);}
        return ['token'=>$token,'id'=>$id,'expires_at'=>$meta['expires_at'],'max_downloads'=>5,'url_path'=>'/s/'.$token];
    }
    private function shareList(): array
    {
        $items=[];foreach(glob($this->state.'/shares/*.json')?:[] as $file){$meta=json_decode(file_get_contents($file),true,32,JSON_THROW_ON_ERROR);
            if($meta['expires_at']<=time()||$meta['downloads']>=5){$this->shareRevoke($meta['id']);continue;}
            unset($meta['password_hash'],$meta['failures'],$meta['window']);$items[]=$meta;
        }return ['items'=>$items];
    }
    private function shareRevoke(string $id): array
    {
        if(!preg_match('/^[a-f0-9]{64}$/D',$id))throw new FileError('INVALID_INPUT','Identifiant invalide.');
        $metaFile=$this->state.'/shares/'.$id.'.json';$meta=is_file($metaFile)?json_decode(file_get_contents($metaFile),true,32,JSON_THROW_ON_ERROR):[];
        foreach(['data','json'] as $ext){$file=$this->state.'/shares/'.$id.'.'.$ext;if(is_file($file)&&!unlink($file))throw new FileError('IO_ERROR','Révocation impossible.',500);}
        return ['revoked'=>true,'path'=>$meta['path']??''];
    }
    /** Standalone-only public endpoint: opaque capability + password, fixed immutable snapshot, bounded lifetime/use. */
    public function sharedDownload(string $token,string $password,string $ip): array
    {
        if(!preg_match('/^[a-f0-9]{64}$/D',$token)||strlen($password)>256)throw new FileError('SHARE_DENIED','Lien ou mot de passe invalide.',403);
        $lock=fopen($this->state.'/operations.lock','c');if(!$lock||!flock($lock,LOCK_EX))throw new FileError('STORAGE_BUSY','Stockage indisponible.',503);
        try{
            $id=hash('sha256',$token);$file=$this->state.'/shares/'.$id.'.json';
            if(!is_file($file))throw new FileError('SHARE_DENIED','Lien ou mot de passe invalide.',403);
            $meta=json_decode(file_get_contents($file),true,32,JSON_THROW_ON_ERROR);
            if($meta['expires_at']<=time()||$meta['downloads']>=5)throw new FileError('SHARE_DENIED','Lien expiré ou épuisé.',403);
            if(time()-$meta['window']>=60){$meta['window']=time();$meta['failures']=0;}
            if($meta['failures']>=5)throw new FileError('SHARE_THROTTLED','Trop de tentatives. Réessayez dans une minute.',429);
            if(!password_verify(hash('sha256',$password),$meta['password_hash'])){
                $meta['failures']++;file_put_contents($file,json_encode($meta,JSON_THROW_ON_ERROR));
                throw new FileError('SHARE_DENIED','Lien ou mot de passe invalide.',403);
            }
            $data=$this->state.'/shares/'.$id.'.data';if(!is_file($data))throw new FileError('SHARE_DENIED','Partage indisponible.',403);
            $content=file_get_contents($data);if($content===false)throw new FileError('IO_ERROR','Lecture impossible.',500);
            $meta['downloads']++;if(file_put_contents($file,json_encode($meta,JSON_THROW_ON_ERROR))===false)throw new FileError('IO_ERROR','Partage indisponible.',500);
            $this->recordAudit(new Identity('standalone','shared-link',false),'shared_download',['path'=>$meta['path'],'_audit'=>['ip'=>$ip]],'success');
            return ['name'=>$meta['name'],'content'=>$content];
        }finally{flock($lock,LOCK_UN);fclose($lock);}
    }

}
