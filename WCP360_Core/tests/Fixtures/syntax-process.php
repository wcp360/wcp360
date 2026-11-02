<?php

declare(strict_types=1);

// Synthetic child for the bounded syntax runner: standard streams only.
$mode = $argv[1] ?? '';

switch ($mode) {
    case 'stdin':
        $input = stream_get_contents(STDIN);
        if ($input === false) {
            exit(2);
        }
        fwrite(STDOUT, $input);
        exit(0);

    case 'exit-seven':
        fwrite(STDOUT, 'synthetic-private-configuration');
        fwrite(STDERR, 'synthetic-private-diagnostic');
        exit(7);

    case 'exact-limits':
        fwrite(STDOUT, str_repeat('O', 262144));
        fwrite(STDERR, str_repeat('E', 16384));
        exit(0);

    case 'stdout-flood':
        fwrite(STDOUT, str_repeat('O', 262145));
        fflush(STDOUT);
        sleep(10);
        exit(0);

    case 'stderr-flood':
        fwrite(STDOUT, 'synthetic-output-before-diagnostic-overflow');
        fwrite(STDERR, str_repeat('E', 16385));
        fflush(STDOUT);
        fflush(STDERR);
        sleep(10);
        exit(0);

    case 'continuous-stdout':
        // Bounded duration and volume; nonblocking writes cannot trap this fixture on a full pipe.
        stream_set_blocking(STDOUT, false);
        $deadline = hrtime(true) + 10_000_000_000;
        $remaining = 16777216;
        $chunk = str_repeat('O', 65536);
        while (hrtime(true) < $deadline) {
            if ($remaining > 0) {
                $written = fwrite(STDOUT, substr($chunk, 0, min(strlen($chunk), $remaining)));
                if ($written !== false) {
                    $remaining -= $written;
                }
            }
            usleep(1000);
        }
        exit(0);

    case 'timeout':
        fwrite(STDOUT, 'synthetic-output-before-timeout');
        fflush(STDOUT);
        sleep(10);
        exit(0);

    case 'environment':
        $values = [];
        foreach ([
            'WCP360_SYNTAX_TEST_PUTENV',
            'WCP360_SYNTAX_TEST_ENV',
            'WCP360_SYNTAX_TEST_SERVER',
            'WCP360_SYNTAX_TEST_EXPLICIT',
            'HOME',
            'XDG_CONFIG_HOME',
            'XDG_DATA_HOME',
            'XDG_CACHE_HOME',
            'XDG_STATE_HOME',
            'XDG_RUNTIME_DIR',
            'PHP_INI_SCAN_DIR',
        ] as $name) {
            $values[$name] = getenv($name);
        }
        fwrite(STDOUT, json_encode($values, JSON_THROW_ON_ERROR));
        exit(0);

    default:
        fwrite(STDERR, 'unknown synthetic mode');
        exit(2);
}
