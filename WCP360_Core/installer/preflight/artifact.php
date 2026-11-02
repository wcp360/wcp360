#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Module\Setup\Application\CandidateArtifactVerifier;

$root = dirname(__DIR__, 2);
try {
    @require $root.'/src/Shared/Platform/TargetPlatform.php';
    @require $root.'/src/Module/Setup/Infrastructure/ArtifactInput.php';
    @require $root.'/src/Module/Setup/Application/CandidateArtifactVerifier.php';
    $inputs = CandidateArtifactVerifier::parseArguments(array_slice($argv, 1));
    $report = CandidateArtifactVerifier::verify($inputs['paths'], $inputs['expected']);
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    exit($report['candidate_verified'] === true ? 0 : 1);
} catch (InvalidArgumentException) {
    fwrite(STDERR, "Usage: artifact.php --artifact=PATH --manifest=PATH --signature=PATH --public-key=HEX --component=COMPONENT --version=VERSION --target=TARGET\n");
    exit(64);
} catch (Throwable) {
    // Fixed fallback also covers missing imports without revealing checkout paths.
    echo '{"schema":"wcp360.candidate-artifact-verification/1","candidate_verified":false,"failure":"verification_unavailable_or_unsafe","canonical_execution_plan":false,"native_apply_allowed":false,"host_changes":false,"qualified_release":false}'.PHP_EOL;
    exit(1);
}
