#!/usr/bin/env php
<?php

declare(strict_types=1);
// This development sample is started by the operator, never included/executed by Core.
require dirname(__DIR__,3).'/WCP360_SDK/php/CandidateJobSummary.php';
try{
    $raw=stream_get_contents(STDIN,16385);
    $summary=\Wcp360\Sdk\Candidate\CandidateJobSummary::decode($raw);
    foreach($summary->counts as $row)echo $row['state'].': '.$row['total'].PHP_EOL;
}catch(\Throwable){fwrite(STDERR,"Invalid job summary.\n");exit(2);}
