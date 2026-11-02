<?php

declare(strict_types=1);
namespace Wcp360\Sdk\Candidate;
/** Standalone wire decoder. No Core, Symfony, database or daemon dependency. */
final readonly class CandidateJobSummary
{
    private const STATES=['QUEUED','RUNNING','SUCCEEDED','FAILED','UNKNOWN_OUTCOME'];
    /** @param list<array{state: string, total: int}> $counts */
    public function __construct(public array $counts) {}
    public static function decode(string $json):self
    {
        if(strlen($json)>16384)throw new \InvalidArgumentException('Summary too large.');
        $data=json_decode($json,true,8,JSON_THROW_ON_ERROR);
        if(!is_array($data)||array_keys($data)!==['schema','counts']||$data['schema']!=='wcp360.candidate.summary/1'||!is_array($data['counts'])||!array_is_list($data['counts'])||count($data['counts'])>count(self::STATES))throw new \InvalidArgumentException('Unsupported summary.');
        $seen=[];
        foreach($data['counts'] as $row){
            if(!is_array($row)||array_keys($row)!==['state','total']||!in_array($row['state'],self::STATES,true)||isset($seen[$row['state']])||!is_int($row['total'])||$row['total']<0)throw new \InvalidArgumentException('Invalid count.');
            $seen[$row['state']]=true;
        }
        return new self($data['counts']);
    }
}
