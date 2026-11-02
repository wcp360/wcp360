#!/usr/bin/env php
<?php

declare(strict_types=1);
$repo=dirname(__DIR__,2);
$manifest=$repo.'/documentation/manifests/WCP360-BM-0001.md';
if(!is_file($manifest)){fwrite(STDERR,"BLOCKED: authoritative baseline manifest is missing.\n");exit(78);}
$content=(string)file_get_contents($manifest);
if(preg_match('/^CODE-BASELINE\\s*=\\s*NONE\\s*$/m',$content)){
    fwrite(STDERR,"BLOCKED: CODE-BASELINE is NONE. This development candidate cannot produce a stable release or latest pointer.\n");
    exit(78);
}
fwrite(STDERR,"BLOCKED: a reviewed stable release publisher and immutable qualification evidence are not implemented by this candidate.\n");
exit(78);
