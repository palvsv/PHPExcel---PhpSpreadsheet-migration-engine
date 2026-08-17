<?php

declare(strict_types=1);

namespace ExcelMigrator\Reports;

class ConsoleReporter
{
    public function title(string $file): void
    {
        echo PHP_EOL;
        echo str_repeat('=', 60) . PHP_EOL;
        echo basename($file) . PHP_EOL;
        echo str_repeat('=', 60) . PHP_EOL;
    }

    public function rule(string $name, int $count): void
    {
        printf("%-30s %5d\n", $name, $count);
    }

    public function imports(int $count): void
    {
        printf("%-30s %5d\n", 'Imports Added', $count);
    }

    public function elapsed(float $seconds): void
    {
        printf("%-30s %5.2f sec\n", 'Elapsed', $seconds);
    }

    public function validation(bool $ok): void
    {
        printf(
            "%-30s %s\n",
            'Validation',
            $ok ? 'PASS' : 'FAILED'
        );
    }
}