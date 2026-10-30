<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Wcp360\FileManager\Contract\{Identity,FileError};
use Wcp360\FileManager\Infrastructure\LocalBackend;
final class LocalBackendTest extends TestCase
{
    private string $base; private LocalBackend $backend; private Identity $id;
    protected function setUp(): void
    {
        $this->base=sys_get_temp_dir().'/fm-test-'.bin2hex(random_bytes(8));
        mkdir($this->base);mkdir($this->base.'/root');mkdir($this->base.'/state');mkdir($this->base.'/root-other');
        $this->backend=new LocalBackend($this->base.'/root',$this->base.'/state','alice');$this->id=new Identity('standalone','alice',true);
    }
    protected function tearDown(): void { $this->remove($this->base); }
    private function remove(string $p): void { if(is_link($p)||!is_dir($p)){unlink($p);return;}foreach(scandir($p) as $n){if($n!=='.'&&$n!=='..')$this->remove($p.'/'.$n);}rmdir($p); }
    private function runOp(string $op,array $a=[]): array { return $this->backend->execute($this->id,$op,$a); }
    private function denied(string $code,callable $call): void
    { try{$call();self::fail('Expected '.$code);}catch(FileError $e){self::assertSame($code,$e->errorCode);} }
    public function testCreateSaveConflictAndDownload(): void
    {
        $created=$this->runOp('create',['path'=>'hello.txt','content'=>'bonjour']);
        $read=$this->runOp('read',['path'=>'hello.txt']);self::assertSame('bonjour',$read['content']);
        $saved=$this->runOp('save',['path'=>'hello.txt','content'=>'salut','revision'=>$created['revision']]);
        self::assertNotSame($created['revision'],$saved['revision']);
        $this->denied('CONCURRENCY_CONFLICT',fn()=>$this->runOp('save',['path'=>'hello.txt','content'=>'perdu','revision'=>$created['revision']]));
        self::assertSame('salut',$this->runOp('download',['path'=>'hello.txt'])['content']);
    }
    public static function badPaths(): array {return array_map(fn($p)=>[$p],['../root-other/secret.txt','/etc/passwd','a/../../x','a//b','./a',"x\0.txt",'a\\b','php://filter','a/','a/../b','file.','file ','CON','con.txt','NUL.txt','COM1','LPT9.log','bad?.txt','bad*.txt']);}
    #[DataProvider('badPaths')]
    public function testInvalidPaths(string $path): void { $this->denied('INVALID_PATH',fn()=>$this->runOp('create',['path'=>$path,'content'=>''])); }
    public function testSiblingPrefixAndSymlinks(): void
    {
        file_put_contents($this->base.'/root-other/secret.txt','secret');
        symlink($this->base.'/root-other',$this->base.'/root/escape');
        $this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('read',['path'=>'escape/secret.txt']));
        $this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('create',['path'=>'escape/new.txt','content'=>'']));
        self::assertSame('blocked',$this->runOp('list',['path'=>''])['items'][0]['kind']);
    }
    public function testDanglingSymlink(): void
    {symlink($this->base.'/absent',$this->base.'/root/link.txt');$this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('create',['path'=>'link.txt','content'=>'attack']));}
    public function testHardlinks(): void
    {file_put_contents($this->base.'/root-other/private.txt','secret');link($this->base.'/root-other/private.txt',$this->base.'/root/link.txt');$this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('read',['path'=>'link.txt']));}
    public function testTenantAndSubjectAndReadonly(): void
    {
        foreach([new Identity('other','alice',true),new Identity('standalone','bob',true)] as $id){$this->denied('ACCESS_DENIED',fn()=>$this->backend->execute($id,'list',['path'=>'']));}
        $this->denied('READ_ONLY',fn()=>$this->backend->execute(new Identity('standalone','alice',false),'mkdir',['path'=>'x']));
        self::assertSame(0,$this->backend->execute(new Identity('standalone','alice',false),'list',['path'=>''])['total']);
    }
    public function testRenameTrashRestoreCollision(): void
    {
        $this->runOp('mkdir',['path'=>'docs']);$c=$this->runOp('create',['path'=>'a.txt','content'=>'hello']);
        $this->runOp('rename',['path'=>'a.txt','destination'=>'docs/b.txt']);
        $t=$this->runOp('trash',['path'=>'docs/b.txt','revision'=>$c['revision']]);
        self::assertCount(1,$this->runOp('trash_list')['items']);
        $this->runOp('create',['path'=>'docs/b.txt','content'=>'new']);
        $this->denied('ALREADY_EXISTS',fn()=>$this->runOp('restore',['id'=>$t['id']]));
        $this->runOp('rename',['path'=>'docs/b.txt','destination'=>'docs/new.txt']);
        $this->runOp('restore',['id'=>$t['id']]);self::assertSame('hello',$this->runOp('read',['path'=>'docs/b.txt'])['content']);
        self::assertCount(0,$this->runOp('trash_list')['items']);
    }
    public function testEmptyDirectoryRestore(): void
    {$this->runOp('mkdir',['path'=>'x']);$t=$this->runOp('trash',['path'=>'x']);$this->runOp('restore',['id'=>$t['id']]);self::assertDirectoryExists($this->base.'/root/x');}
    public function testNonEmptyDirectoryAndRoot(): void
    {
        $this->runOp('mkdir',['path'=>'x']);$this->runOp('create',['path'=>'x/a.txt','content'=>'']);
        $trash=$this->runOp('trash',['path'=>'x']);
        self::assertDirectoryDoesNotExist($this->base.'/root/x');
        $this->runOp('restore',['id'=>$trash['id']]);
        self::assertFileExists($this->base.'/root/x/a.txt');
        $this->denied('ROOT_PROTECTED',fn()=>$this->runOp('trash',['path'=>'']));
        $this->denied('INVALID_PATH',fn()=>$this->runOp('rename',['path'=>'x','destination'=>'x/nested']));
    }
    public function testNoOverwriteAndMissingParent(): void
    {
        $this->runOp('create',['path'=>'a.txt','content'=>'a']);
        $this->denied('ALREADY_EXISTS',fn()=>$this->runOp('upload',['path'=>'a.txt','content'=>'b']));
        $this->denied('NOT_FOUND',fn()=>$this->runOp('create',['path'=>'missing/a.txt','content'=>'a']));
    }
    public function testTextAndUploadLimits(): void
    {
        $this->denied('FILE_TOO_LARGE',fn()=>$this->runOp('create',['path'=>'a.txt','content'=>str_repeat('x',1048577)]));
        $this->denied('NOT_TEXT',fn()=>$this->runOp('create',['path'=>'a.txt','content'=>"\0"]));
        $this->denied('NOT_EDITABLE',fn()=>$this->runOp('create',['path'=>'a.exe','content'=>'x']));
        $this->denied('FILE_TOO_LARGE',fn()=>$this->runOp('upload',['path'=>'a.bin','content'=>str_repeat('x',LocalBackend::MAX_FILE+1)]));
        $this->runOp('upload',['path'=>'a.bin','content'=>"\0\xff"]);self::assertSame("\0\xff",$this->runOp('download',['path'=>'a.bin'])['content']);
    }
    public function testDotfiles(): void
    {$this->runOp('create',['path'=>'.env','content'=>'APP=demo']);self::assertSame('APP=demo',$this->runOp('read',['path'=>'.env'])['content']);}
    public function testPaginationAndSearch(): void
    {for($i=0;$i<105;$i++)file_put_contents($this->base.'/root/doc'.$i.'.txt','');$l=$this->runOp('list',['path'=>'','page'=>2]);self::assertSame(105,$l['total']);self::assertCount(5,$l['items']);self::assertSame(1,$this->runOp('list',['path'=>'','query'=>'doc104'])['total']);}
    public function testCorbeilleIdentifierTraversal(): void
    {$this->denied('INVALID_INPUT',fn()=>$this->runOp('restore',['id'=>'../../root']));}
    public function testWindowsBoundaryNormalization(): void
    {
        $method=new ReflectionMethod(LocalBackend::class,'within');
        self::assertTrue($method->invoke($this->backend,'C:\\data\\root\\file.txt','C:\\data\\root'));
        self::assertFalse($method->invoke($this->backend,'C:\\data\\root-other\\file.txt','C:\\data\\root'));
    }
    public function testNestedStateRejected(): void
    {$this->expectException(RuntimeException::class);new LocalBackend($this->base.'/root',$this->base.'/root','alice');}
    public function testDirectoryCopyAndOriginalIndependence(): void
    {
        $this->runOp('mkdir',['path'=>'source']);$this->runOp('mkdir',['path'=>'source/sub']);
        $created=$this->runOp('create',['path'=>'source/sub/a.txt','content'=>'original']);
        $r=$this->runOp('copy',['path'=>'source','destination'=>'clone']);
        self::assertSame(3,$r['entries']);self::assertSame('original',$this->runOp('read',['path'=>'clone/sub/a.txt'])['content']);
        $this->runOp('save',['path'=>'source/sub/a.txt','content'=>'changed','revision'=>$created['revision']]);
        self::assertSame('original',$this->runOp('read',['path'=>'clone/sub/a.txt'])['content']);
        $this->denied('ALREADY_EXISTS',fn()=>$this->runOp('copy',['path'=>'source','destination'=>'clone']));
        $this->denied('INVALID_PATH',fn()=>$this->runOp('copy',['path'=>'source','destination'=>'source/nested']));
        $this->denied('ROOT_PROTECTED',fn()=>$this->runOp('copy',['path'=>'','destination'=>'all']));
    }
    public function testCopyRejectsLinksWithoutLeavingStaging(): void
    {
        $this->runOp('mkdir',['path'=>'source']);symlink($this->base.'/root-other',$this->base.'/root/source/link');
        $this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('copy',['path'=>'source','destination'=>'clone']));
        $this->denied('UNSAFE_ENTRY',fn()=>$this->runOp('trash',['path'=>'source']));
        self::assertFileDoesNotExist($this->base.'/root/clone');self::assertSame([],glob($this->base.'/root/.fm-copy-*'));
    }
    public function testCopySizeAndDepthLimits(): void
    {
        $h=fopen($this->base.'/root/large.bin','w');ftruncate($h,LocalBackend::MAX_FILE+1);fclose($h);
        $this->denied('TREE_LIMIT',fn()=>$this->runOp('copy',['path'=>'large.bin','destination'=>'copy.bin']));
        $path='deep';mkdir($this->base.'/root/'.$path);for($i=0;$i<33;$i++){$path.='/x';mkdir($this->base.'/root/'.$path);}
        $this->denied('TREE_LIMIT',fn()=>$this->runOp('trash',['path'=>'deep']));self::assertDirectoryExists($this->base.'/root/deep');
    }
    public function testHiddenDirectoryFilterAndProperties(): void
    {
        $this->runOp('mkdir',['path'=>'visible']);$this->runOp('mkdir',['path'=>'.hidden']);
        $this->runOp('create',['path'=>'.env','content'=>'secret']);
        self::assertSame(1,$this->runOp('list',['path'=>'','hidden'=>'0'])['total']);
        self::assertSame(2,$this->runOp('list',['path'=>'','directories'=>'1'])['total']);
        self::assertSame(1,$this->runOp('list',['path'=>'','directories'=>'1','hidden'=>'0'])['total']);
        $props=$this->runOp('properties',['path'=>'.env']);self::assertSame(6,$props['size']);self::assertSame(hash('sha256','secret'),$props['revision']);
        self::assertArrayHasKey('uid',$props);self::assertArrayNotHasKey('content',$props);
        $this->denied('INVALID_INPUT',fn()=>$this->runOp('list',['path'=>'','hidden'=>'invalid']));
        $this->denied('READ_ONLY',fn()=>$this->backend->execute(new Identity('standalone','alice',false),'copy',['path'=>'visible','destination'=>'x']));
    }
    private function admin(): void { $this->id=new Identity('standalone','alice',true,true); }
    public function testChmodAdminAndRecursiveFilter(): void
    {
        $this->runOp('mkdir',['path'=>'d']);$this->runOp('create',['path'=>'d/a.txt','content'=>'abc']);
        $this->denied('ADMIN_REQUIRED',fn()=>$this->runOp('chmod',['path'=>'d/a.txt','mode'=>'640']));$this->admin();
        $r=$this->runOp('chmod',['path'=>'d','mode'=>'640','recursive'=>true,'target'=>'files']);self::assertSame(1,$r['changed']);
        self::assertSame('0640',$this->runOp('properties',['path'=>'d/a.txt'])['mode']);
        $this->denied('INVALID_MODE',fn()=>$this->runOp('chmod',['path'=>'d','mode'=>'4755']));
        $this->denied('MODE_NOT_ALLOWED',fn()=>$this->runOp('chmod',['path'=>'d','mode'=>'777']));
        $this->denied('MODE_NOT_ALLOWED',fn()=>$this->runOp('chmod',['path'=>'d','mode'=>'600']));
        self::assertSame('0700',$this->runOp('properties',['path'=>'d'])['mode']);
    }
    public function testQuotaPreventsDataWrite(): void
    {
        $this->admin();$r=$this->runOp('quota_set',['bytes'=>1048576]);self::assertSame(1048576,$r['quota']);
        $this->runOp('upload',['path'=>'large.bin','content'=>str_repeat('x',900000)]);
        $this->denied('QUOTA_EXCEEDED',fn()=>$this->runOp('upload',['path'=>'rejected.bin','content'=>str_repeat('x',200000)]));
        self::assertFileDoesNotExist($this->base.'/root/rejected.bin');
        self::assertSame('warning',$this->runOp('disk_usage')['alert']);
    }
    public function testVersionRestoreAndOptimisticConcurrency(): void
    {
        $c=$this->runOp('create',['path'=>'v.txt','content'=>'before']);$s=$this->runOp('save',['path'=>'v.txt','content'=>'after','revision'=>$c['revision']]);
        $v=$this->runOp('versions',['path'=>'v.txt'])['items'];self::assertCount(1,$v);
        $this->denied('CONCURRENCY_CONFLICT',fn()=>$this->runOp('version_restore',['path'=>'v.txt','id'=>$v[0]['id'],'revision'=>$c['revision']]));
        $this->runOp('version_restore',['path'=>'v.txt','id'=>$v[0]['id'],'revision'=>$s['revision']]);self::assertSame('before',$this->runOp('read',['path'=>'v.txt'])['content']);
        self::assertCount(2,$this->runOp('versions',['path'=>'v.txt'])['items']);
    }
    public function testVersionRetentionBound(): void
    {
        $c=$this->runOp('create',['path'=>'v.txt','content'=>'0']);for($i=1;$i<=23;$i++)$c=$this->runOp('save',['path'=>'v.txt','content'=>(string)$i,'revision'=>$c['revision']]);
        self::assertCount(20,$this->runOp('versions',['path'=>'v.txt'])['items']);
    }
    public function testContentSearchNoSnippetsAndLinkEscape(): void
    {
        $this->runOp('create',['path'=>'a.txt','content'=>"hello\nTOKEN=value"]);file_put_contents($this->base.'/root-other/leak.txt','TOKEN=private');symlink($this->base.'/root-other',$this->base.'/root/link');
        $r=$this->runOp('search',['path'=>'','query'=>'token']);self::assertCount(1,$r['matches']);self::assertSame(['path'=>'a.txt','line'=>2],$r['matches'][0]);self::assertGreaterThanOrEqual(1,$r['skipped']);
    }
    public function testLockBlocksOtherEditorAndParentMutation(): void
    {
        $this->runOp('mkdir',['path'=>'d']);$c=$this->runOp('create',['path'=>'d/a.txt','content'=>'first']);$lock=$this->runOp('lock_acquire',['path'=>'d/a.txt']);
        $this->denied('FILE_LOCKED',fn()=>$this->runOp('save',['path'=>'d/a.txt','content'=>'bad','revision'=>$c['revision']]));
        $this->denied('FILE_LOCKED',fn()=>$this->runOp('trash',['path'=>'d']));
        $this->runOp('save',['path'=>'d/a.txt','content'=>'good','revision'=>$c['revision'],'lock_token'=>$lock['token']]);
        $this->runOp('lock_release',['path'=>'d/a.txt','lock_token'=>$lock['token']]);$this->runOp('rename',['path'=>'d','destination'=>'moved']);self::assertDirectoryExists($this->base.'/root/moved');
    }
    public function testAuditIncludesPathIpButNoSecrets(): void
    {
        $this->admin();$this->runOp('create',['path'=>'private.txt','content'=>'very-secret-content','_audit'=>['ip'=>'127.0.0.1','request_id'=>'request-test']]);
        $items=$this->runOp('audit')['items'];self::assertSame('private.txt',$items[0]['path']);self::assertSame('127.0.0.1',$items[0]['ip']);self::assertStringNotContainsString('very-secret-content',json_encode($items));
    }
    public function testSnapshotContents(): void
    {
        if(!class_exists(ZipArchive::class)){$this->markTestSkipped('zip unavailable');}
        $this->runOp('mkdir',['path'=>'d']);$this->runOp('create',['path'=>'d/a.txt','content'=>'snapshot']);$r=$this->runOp('snapshot',['path'=>'d']);$tmp=$this->base.'/test.zip';file_put_contents($tmp,$r['content']);$z=new ZipArchive();self::assertTrue($z->open($tmp));self::assertSame('snapshot',$z->getFromName('d/a.txt'));$z->close();
    }
    public function testProtectedShareIsImmutableAndRevocable(): void
    {
        $c=$this->runOp('create',['path'=>'a.txt','content'=>'shared original']);$r=$this->runOp('share_create',['path'=>'a.txt','password'=>'strong-test-password','ttl'=>3600]);
        $this->runOp('save',['path'=>'a.txt','content'=>'edited','revision'=>$c['revision']]);
        $this->denied('SHARE_DENIED',fn()=>$this->backend->sharedDownload($r['token'],'incorrect','127.0.0.1'));
        self::assertSame('shared original',$this->backend->sharedDownload($r['token'],'strong-test-password','127.0.0.1')['content']);
        $this->runOp('share_revoke',['id'=>$r['id']]);$this->denied('SHARE_DENIED',fn()=>$this->backend->sharedDownload($r['token'],'strong-test-password','127.0.0.1'));
    }
    public function testShareDownloadLimitAndThrottling(): void
    {
        $this->runOp('create',['path'=>'a.txt','content'=>'shared']);$r=$this->runOp('share_create',['path'=>'a.txt','password'=>'strong-test-password']);
        for($i=0;$i<5;$i++)$this->backend->sharedDownload($r['token'],'strong-test-password','127.0.0.1');
        $this->denied('SHARE_DENIED',fn()=>$this->backend->sharedDownload($r['token'],'strong-test-password','127.0.0.1'));
        $r=$this->runOp('share_create',['path'=>'a.txt','password'=>'strong-test-password']);for($i=0;$i<5;$i++)$this->denied('SHARE_DENIED',fn()=>$this->backend->sharedDownload($r['token'],'bad','127.0.0.1'));
        $this->denied('SHARE_THROTTLED',fn()=>$this->backend->sharedDownload($r['token'],'strong-test-password','127.0.0.1'));
    }
    public function testTrashPurgeRequiresAdminAndRemovesTree(): void
    {
        $this->runOp('mkdir',['path'=>'d']);$this->runOp('create',['path'=>'d/a.txt','content'=>'old']);$t=$this->runOp('trash',['path'=>'d']);
        $this->denied('ADMIN_REQUIRED',fn()=>$this->runOp('trash_purge',['id'=>$t['id']]));$this->admin();
        $this->runOp('trash_purge',['id'=>$t['id']]);self::assertSame([],$this->runOp('trash_list')['items']);
        $this->denied('NOT_FOUND',fn()=>$this->runOp('restore',['id'=>$t['id']]));
        $this->denied('INVALID_INPUT',fn()=>$this->runOp('trash_purge',['id'=>'../../']));
    }
    public function testLongSharePasswordsAreNotTruncated(): void
    {
        $this->runOp('create',['path'=>'a.txt','content'=>'shared']);$prefix=str_repeat('x',80);$r=$this->runOp('share_create',['path'=>'a.txt','password'=>$prefix.'first']);
        $this->denied('SHARE_DENIED',fn()=>$this->backend->sharedDownload($r['token'],$prefix.'other','127.0.0.1'));
        self::assertSame('shared',$this->backend->sharedDownload($r['token'],$prefix.'first','127.0.0.1')['content']);
    }
}
