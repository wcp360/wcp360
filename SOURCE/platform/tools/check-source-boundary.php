<?php

declare(strict_types=1);

$root = dirname(__DIR__) . '/src';
$forbidden = [
    '/\bshell_exec\s*\(/i',
    '/\bexec\s*\(/i',
    '/\bsystem\s*\(/i',
    '/\bpassthru\s*\(/i',
    '/\bproc_open\s*\(/i',
    '/\bpopen\s*\(/i',
    '/\bpcntl_exec\s*\(/i',
    '/\bsudo\b/i',
    '/\bsystemctl\b/i',
];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$checked = 0;
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    ++$checked;
    $content = (string) file_get_contents($file->getPathname());
    foreach ($forbidden as $pattern) {
        if (preg_match($pattern, $content) === 1) {
            fwrite(STDERR, 'FORBIDDEN primitive in ' . $file->getPathname() . "\n");
            exit(1);
        }
    }
}
fwrite(STDOUT, "PASS  source boundary: {$checked} PHP source files contain no generic execution primitive\n");
