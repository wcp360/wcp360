<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Infrastructure;
use Wcp360\FileManager\Contract\{FileBackend,FileError,Identity};
/** Single-tenant storage. The root MUST NOT be mutable by other processes or served as executable web content.
 * For hostile tenants, use an OS-isolated executor with descriptor-relative operations; PHP realpath is insufficient.
 */
final class LocalBackend implements FileBackend
{
    use ProFeatures;
    public const MAX_FILE = 20971520;
    public const MAX_EDIT = 1048576;
    private string $root;
    private string $state;
    private const TEXT = ['txt','md','html','css','js','json','xml','yml','yaml','csv','ini','log','php','env','htaccess','conf','svg','ts'];
    public function __construct(string $root, string $state, private readonly string $owner)
    {
        $r = realpath($root); $s = realpath($state);
        if (!$r || !$s || !is_dir($r) || !is_dir($s) || $r === '/' || $s === '/' || preg_match('~^[a-zA-Z]:[/\\\\]?$~', $r) || preg_match('~^[a-zA-Z]:[/\\\\]?$~', $s) || is_link($root) || is_link($state)
            || $this->within($r,$s) || $this->within($s,$r)) { throw new \RuntimeException('Configure distinct existing private root and state directories.'); }
        $this->root = $this->canonical($r); $this->state = $this->canonical($s);
    }
    private function canonical(string $path): string
    { return rtrim(str_replace('\\', '/', $path), '/'); }
    private function within(string $path,string $root): bool
    {
        $path=$this->canonical($path); $root=$this->canonical($root);
        if (PHP_OS_FAMILY==='Windows') { $path=strtolower($path); $root=strtolower($root); }
        return $path === $root || str_starts_with($path,$root.'/');
    }
    public function execute(Identity $identity,string $operation,array $input): array
    {
        if ($identity->tenantId !== 'standalone' || !hash_equals($this->owner,$identity->subjectId)) { throw new FileError('ACCESS_DENIED','Accès refusé.',403); }
        if (!in_array($operation,['list','read','download','trash_list','properties','capabilities','disk_usage','search','versions','audit','share_list'],true) && !$identity->canWrite) { throw new FileError('READ_ONLY','Accès en lecture seule.',403); }
        if (in_array($operation,['chmod','quota_set','audit','trash_purge'],true) && !$identity->canManage) { throw new FileError('ADMIN_REQUIRED','Autorisation administrateur requise.',403); }
        $lock = fopen($this->state.'/operations.lock','c');
        if ($lock === false || !flock($lock,LOCK_EX)) { throw new FileError('STORAGE_BUSY','Stockage indisponible.',503); }
        try {
            clearstatcache(true);
            $mutating=!in_array($operation,['list','read','download','trash_list','properties','capabilities','disk_usage','search','versions','audit','share_list'],true);
            try {
            if ($mutating && !str_starts_with($operation,'lock_')) { $this->guardLocks($input); }
            $result = match($operation) {
                'capabilities' => ['platform'=>PHP_OS_FAMILY,'chmod'=>PHP_OS_FAMILY!=='Windows' && $identity->canManage,'admin'=>$identity->canManage,'zip'=>class_exists(\ZipArchive::class),'write'=>$identity->canWrite,'integration'=>'standalone-private-storage'],
                'disk_usage' => $this->diskUsage($input),
                'quota_set' => $this->quotaSet($input),
                'chmod' => $this->permissions($input),
                'search' => $this->searchContents($input),
                'versions' => $this->versions($this->str($input,'path')),
                'version_restore' => $this->restoreVersion($input),
                'snapshot' => $this->snapshot($input),
                'lock_acquire','lock_renew','lock_release' => $this->fileLock($identity,$operation,$input),
                'audit' => $this->auditTrail(),
                'trash_purge' => $this->purgeTrash($this->str($input,'id')),
                'share_create' => $this->shareCreate($input),
                'share_list' => $this->shareList(),
                'share_revoke' => $this->shareRevoke($this->str($input,'id')),
                'list' => $this->listing($input),
                'properties' => $this->properties($this->str($input,'path')),
                'copy' => $this->copyEntry($input),
                'read','download' => $this->read($this->str($input,'path'),$operation === 'read'),
                'save','create','upload' => $this->write($input,$operation),
                'mkdir' => $this->mkdir($this->str($input,'path')),
                'rename' => $this->rename($input),
                'trash' => $this->trash($input),
                'trash_list' => $this->trashList(),
                'restore' => $this->restore($this->str($input,'id')),
                default => throw new FileError('UNKNOWN_OPERATION','Opération inconnue.',404)
            };
            if ($mutating) { if(!isset($input['path']) && isset($result['path']))$input['path']=$result['path']; $this->recordAudit($identity,$operation,$input,'success'); }
            return $result;
            } catch (\Throwable $e) {
                if ($mutating) { $this->recordAudit($identity,$operation,$input,$e instanceof FileError?$e->errorCode:'INTERNAL_ERROR'); }
                throw $e;
            }
        } finally { flock($lock,LOCK_UN); fclose($lock); }
    }
    private function str(array $a,string $k): string
    { if (!isset($a[$k]) || !is_string($a[$k])) { throw new FileError('INVALID_INPUT','Paramètre invalide : '.$k); } return $a[$k]; }
    private function relative(string $path): string
    {
        if (strlen($path)>4096 || preg_match('/[\x00-\x1f\x7f\\\\:]/',$path) || str_starts_with($path,'/')) { throw new FileError('INVALID_PATH','Chemin relatif invalide.'); }
        if ($path === '') { return ''; }
        foreach (explode('/',$path) as $part) {
            // Windows aliases/reserved devices can bypass lexical identity or address device streams.
            if (preg_match('/[. ]$/D', $part) || preg_match('/[<>"|?*]/', $part)
                || preg_match('/^(CON|PRN|AUX|NUL|COM[1-9¹²³]|LPT[1-9¹²³])(?:\.|$)/iu', $part)) {
                throw new FileError('INVALID_PATH','Nom de fichier réservé ou ambigu.');
            }
            if ($part === '' || $part === '.' || $part === '..' || strlen($part)>255 || !preg_match('//u',$part)) { throw new FileError('INVALID_PATH','Chemin invalide.'); }
        }
        return $path;
    }
    private function path(string $path,bool $new = false): string
    {
        $path=$this->relative($path); $current=$this->root;
        $parts=$path === '' ? [] : explode('/',$path);
        foreach ($parts as $i=>$part) {
            $current.='/'.$part;
            $st=@lstat($current);
            if ($st === false) {
                if ($new && $i === count($parts)-1 && is_dir(dirname($current))) { return $current; }
                throw new FileError('NOT_FOUND','Élément introuvable.',404);
            }
            $type=$st['mode'] & 0170000;
            if (!in_array($type,[0100000,0040000],true) || ($type===0100000 && $st['nlink']!==1)) { throw new FileError('UNSAFE_ENTRY','Lien ou fichier spécial interdit.',403); }
            $real=realpath($current);
            if ($real===false || !$this->within($real,$this->root)) { throw new FileError('ACCESS_DENIED','Accès refusé.',403); }
        }
        return $current;
    }
    private function mutable(string $path): string
    { if ($path==='') { throw new FileError('ROOT_PROTECTED','La racine est protégée.',403); } return $this->path($path); }
    private function revision(string $path): string
    {
        if (!is_file($path)) { throw new FileError('NOT_A_FILE','Un fichier est requis.'); }
        if (filesize($path)>self::MAX_FILE) { throw new FileError('FILE_TOO_LARGE','Fichier supérieur à 20 Mio.',413); }
        $h=hash_file('sha256',$path);
        if ($h===false) { throw new FileError('IO_ERROR','Lecture impossible.',500); } return $h;
    }
    private function checkRevision(string $path,array $input): void
    { if (!hash_equals($this->revision($path),$this->str($input,'revision'))) { throw new FileError('CONCURRENCY_CONFLICT','Le fichier a changé. Rechargez-le avant de continuer.',409); } }
    private function editable(string $path): void
    {
        $name=basename($path); $ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));
        if (!in_array($ext,self::TEXT,true) && !in_array($name,['.env','.htaccess'],true)) { throw new FileError('NOT_EDITABLE','Ce type de fichier ne peut pas être édité.',415); }
    }
    private function text(string $content): void
    { if (strlen($content)>self::MAX_EDIT) { throw new FileError('FILE_TOO_LARGE','Édition limitée à 1 Mio.',413); }
      if (str_contains($content,"\0") || !preg_match('//u',$content)) { throw new FileError('NOT_TEXT','Édition limitée au texte UTF-8.',415); } }
    private function read(string $relative,bool $edit): array
    {
        $p=$this->path($relative); $revision=$this->revision($p);
        if ($edit) { $this->editable($p); if (filesize($p)>self::MAX_EDIT) { throw new FileError('FILE_TOO_LARGE','Édition limitée à 1 Mio.',413); } }
        $content=file_get_contents($p);
        if ($content===false) { throw new FileError('IO_ERROR','Lecture impossible.',500); }
        if ($edit) { $this->text($content); }
        return ['name'=>basename($p),'path'=>$relative,'content'=>$content,'revision'=>$revision];
    }
    private function listing(array $a): array
    {
        $relative=$this->str($a,'path'); $dir=$this->path($relative);
        if (!is_dir($dir)) { throw new FileError('NOT_A_DIRECTORY','Dossier requis.'); }
        $query=$a['query']??''; if (!is_string($query) || strlen($query)>200) { throw new FileError('INVALID_INPUT','Recherche invalide.'); }
        $page=filter_var($a['page']??1,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>100000]]);
        if ($page===false) { throw new FileError('INVALID_INPUT','Page invalide.'); }
        $hidden=filter_var($a['hidden']??true,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
        $directories=filter_var($a['directories']??false,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
        if ($hidden===null || $directories===null) { throw new FileError('INVALID_INPUT','Filtre invalide.'); }
        $items=[]; $scanned=0;
        foreach (new \DirectoryIterator($dir) as $f) {
            if ($f->isDot()) { continue; }
            if (++$scanned>10000) { throw new FileError('DIRECTORY_TOO_LARGE','Dossier limité à 10 000 entrées.',413); }
            $name=$f->getFilename(); if (!preg_match('//u',$name)) { continue; }
            if (!$hidden && str_starts_with($name,'.')) { continue; }
            if ($query!=='' && stripos($name,$query)===false) { continue; }
            $rel=($relative===''?'':$relative.'/').$name;
            try { $safe=$this->path($rel); }
            catch (FileError) { if ($directories) { continue; } $items[]=['name'=>$name,'path'=>$rel,'kind'=>'blocked','size'=>null,'mtime'=>null,'mode'=>null]; continue; }
            if ($directories && !is_dir($safe)) { continue; }
            $items[]=['uid'=>PHP_OS_FAMILY==='Windows'?null:fileowner($safe),'gid'=>PHP_OS_FAMILY==='Windows'?null:filegroup($safe),'name'=>$name,'path'=>$rel,'kind'=>is_dir($safe)?'directory':'file','size'=>is_dir($safe)?null:filesize($safe),'mtime'=>filemtime($safe),'mode'=>substr(sprintf('%o',fileperms($safe)),-4)];
        }
        usort($items,fn($a,$b)=>(($a['kind']!=='directory')<=>($b['kind']!=='directory')) ?: strnatcasecmp($a['name'],$b['name']));
        return ['path'=>$relative,'items'=>array_slice($items,($page-1)*100,100),'total'=>count($items),'page'=>$page,'page_size'=>100];
    }
    private function write(array $a,string $op): array
    {
        $rel=$this->str($a,'path'); if ($rel==='') { throw new FileError('ROOT_PROTECTED','La racine est protégée.',403); }
        $p=$this->path($rel,true); $content=$this->str($a,'content');
        if (strlen($content)>self::MAX_FILE) { throw new FileError('FILE_TOO_LARGE','Import limité à 20 Mio.',413); }
        if ($op!=='upload') { $this->editable($p); $this->text($content); }
        if ($op==='save') { $p=$this->mutable($rel); $this->checkRevision($p,$a); }
        elseif (file_exists($p)) { throw new FileError('ALREADY_EXISTS','Ce nom existe déjà.',409); }
        $this->assertCapacity(strlen($content));
        if ($op==='save') { $this->saveVersion($rel,$p); }
        $mode=$op==='save' ? (fileperms($p)&0777) : 0600;
        $tmp=tempnam(dirname($p),'.fm-');
        if ($tmp===false) { throw new FileError('IO_ERROR','Création temporaire impossible.',500); }
        try {
            if (file_put_contents($tmp,$content)!==strlen($content) || !chmod($tmp,$mode) || !rename($tmp,$p)) { throw new FileError('IO_ERROR','Enregistrement impossible.',500); }
        } finally { if (file_exists($tmp)) { unlink($tmp); } }
        return ['path'=>$rel,'revision'=>hash('sha256',$content)];
    }
    private function mkdir(string $rel): array
    {
        if ($rel==='') { throw new FileError('ROOT_PROTECTED','La racine est protégée.',403); }
        $p=$this->path($rel,true); if (file_exists($p)) { throw new FileError('ALREADY_EXISTS','Ce nom existe déjà.',409); }
        if (!mkdir($p,0700)) { throw new FileError('IO_ERROR','Création impossible.',500); } return ['path'=>$rel];
    }
    private function rename(array $a): array
    {
        $rel=$this->str($a,'path'); $source=$this->mutable($rel); $dest=$this->str($a,'destination');
        if ($dest==='') { throw new FileError('ROOT_PROTECTED','La racine est protégée.',403); }
        $target=$this->path($dest,true);
        if (file_exists($target)) { throw new FileError('ALREADY_EXISTS','La destination existe déjà.',409); }
        if (is_dir($source) && $this->within($target,$source)) { throw new FileError('INVALID_PATH','Déplacement dans son propre dossier interdit.'); }
        if (!rename($source,$target)) { throw new FileError('IO_ERROR','Déplacement impossible.',500); } return ['path'=>$dest];
    }
    private function trash(array $a): array
    {
        $rel=$this->str($a,'path'); $source=$this->mutable($rel);
        if (is_file($source)) { $this->checkRevision($source,$a); }
        // Validate the whole tree before moving it; reject links and bound resource consumption.
        if (is_dir($source)) { $this->inventory($rel); }
        $id=bin2hex(random_bytes(16)); $slot=$this->state.'/trash/'.$id;
        if (!is_dir($this->state.'/trash') && !mkdir($this->state.'/trash',0700)) { throw new FileError('IO_ERROR','Corbeille indisponible.',500); }
        if (!mkdir($slot,0700)) { throw new FileError('IO_ERROR','Corbeille indisponible.',500); }
        $meta=json_encode(['id'=>$id,'path'=>$rel,'deleted_at'=>time()],JSON_THROW_ON_ERROR);
        if (file_put_contents($slot.'/meta.json',$meta)!==strlen($meta)) { throw new FileError('IO_ERROR','Corbeille indisponible.',500); }
        if (!rename($source,$slot.'/data')) { throw new FileError('IO_ERROR','Mise à la corbeille impossible. Vérifiez le même système de fichiers.',500); }
        return ['id'=>$id,'path'=>$rel];
    }
    private function trashList(): array
    {
        $items=[];
        foreach (glob($this->state.'/trash/*/meta.json') ?: [] as $f) {
            if (count($items)>=1000) { throw new FileError('TRASH_TOO_LARGE','Corbeille supérieure à 1 000 éléments. Intervention administrateur requise.',413); }
            if (file_exists(dirname($f).'/data')) { $items[]=json_decode(file_get_contents($f),true,16,JSON_THROW_ON_ERROR); }
        }
        usort($items,fn($a,$b)=>$b['deleted_at']<=>$a['deleted_at']); return ['items'=>$items];
    }
    private function restore(string $id): array
    {
        if (!preg_match('/^[a-f0-9]{32}$/D',$id)) { throw new FileError('INVALID_INPUT','Identifiant invalide.'); }
        $slot=$this->state.'/trash/'.$id;
        if (!is_file($slot.'/meta.json') || !file_exists($slot.'/data')) { throw new FileError('NOT_FOUND','Élément introuvable.',404); }
        $meta=json_decode(file_get_contents($slot.'/meta.json'),true,16,JSON_THROW_ON_ERROR); $p=$this->path($meta['path'],true);
        if (file_exists($p)) { throw new FileError('ALREADY_EXISTS','Le chemin original existe déjà.',409); }
        if (!rename($slot.'/data',$p)) { throw new FileError('IO_ERROR','Restauration impossible.',500); }
        unlink($slot.'/meta.json'); rmdir($slot); return ['path'=>$meta['path']];
    }
    /** Bounded traversal, all paths validated, no symlink following. */
    private function inventory(string $relative): array
    {
        $result=[];$bytes=0;$stack=[[$relative,0]];
        while ($stack) {
            [$rel,$depth]=array_pop($stack);
            if ($depth>32 || count($result)>=2000) { throw new FileError('TREE_LIMIT','Limite : 2 000 éléments et 32 niveaux par opération.',413); }
            $path=$this->path($rel); $directory=is_dir($path);
            $size=$directory?0:filesize($path); $bytes+=$size;
            if ($bytes>104857600 || $size>self::MAX_FILE) { throw new FileError('TREE_LIMIT','Limite : 100 Mio au total et 20 Mio par fichier.',413); }
            $result[]=['path'=>$rel,'directory'=>$directory,'size'=>$size];
            if ($directory) {
                $names=scandir($path);
                if ($names===false) { throw new FileError('IO_ERROR','Lecture du dossier impossible.',500); }
                if (count($names)>2002) { throw new FileError('TREE_LIMIT','Dossier trop volumineux pour cette opération.',413); }
                foreach ($names as $name) { if ($name!=='.' && $name!=='..') { $stack[]=[($rel===''?'':$rel.'/').$name,$depth+1]; } }
            }
        }
        return ['entries'=>$result,'bytes'=>$bytes];
    }
    private function properties(string $relative): array
    {
        $path=$this->path($relative); $dir=is_dir($path);
        return ['name'=>$relative===''?'Mes fichiers':basename($path),'path'=>$relative,'kind'=>$dir?'directory':'file',
            'size'=>$dir?null:filesize($path),'mtime'=>filemtime($path),'mode'=>substr(sprintf('%o',fileperms($path)),-4),
            'uid'=>PHP_OS_FAMILY==='Windows'?null:fileowner($path),'gid'=>PHP_OS_FAMILY==='Windows'?null:filegroup($path),
            'platform'=>PHP_OS_FAMILY,'revision'=>!$dir && filesize($path)<=self::MAX_FILE?$this->revision($path):null];
    }
    private function copyEntry(array $input): array
    {
        $relative=$this->str($input,'path'); $source=$this->mutable($relative);
        $destination=$this->str($input,'destination');
        if ($destination==='') { throw new FileError('ROOT_PROTECTED','La racine est protégée.',403); }
        $target=$this->path($destination,true);
        if (file_exists($target)) { throw new FileError('ALREADY_EXISTS','La destination existe déjà.',409); }
        if ($this->within($target,$source)) { throw new FileError('INVALID_PATH','Copie dans son propre dossier interdite.'); }
        $inventory=$this->inventory($relative);
        $this->assertCapacity($inventory['bytes']);
        $stage=dirname($target).'/.fm-copy-'.bin2hex(random_bytes(16));
        if (!mkdir($stage,0700)) { throw new FileError('IO_ERROR','Création temporaire impossible.',500); }
        try {
            foreach ($inventory['entries'] as $entry) {
                $suffix=substr($entry['path'],strlen($relative)); $to=$stage.'/data'.$suffix;
                $from=$this->path($entry['path']);
                if ($entry['directory']) {
                    if (!mkdir($to,0700)) { throw new FileError('IO_ERROR','Copie du dossier impossible.',500); }
                } else {
                    if (!copy($from,$to) || !chmod($to,0600)) { throw new FileError('IO_ERROR','Copie du fichier impossible.',500); }
                }
            }
            if (!rename($stage.'/data',$target)) { throw new FileError('IO_ERROR','Finalisation de copie impossible.',500); }
        } finally { $this->removeStaging($stage); }
        return ['path'=>$destination,'entries'=>count($inventory['entries']),'bytes'=>$inventory['bytes']];
    }
    /** Only called on an unpredictable staging directory created by this operation. */
    private function removeStaging(string $path): void
    {
        if (is_link($path) || !is_dir($path)) { if (file_exists($path) || is_link($path)) { unlink($path); } return; }
        foreach (scandir($path) ?: [] as $name) { if ($name!=='.' && $name!=='..') { $this->removeStaging($path.'/'.$name); } }
        rmdir($path);
    }

}
