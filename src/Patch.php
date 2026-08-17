<?php

declare(strict_types=1);

namespace ExcelMigrator;

final class Patch
{
    public function __construct(
        public int $start,
        public int $end,
        public string $replacement,
        public string $rule,

        // New fields
        public int $line = 0,
        public string $original = ''
    ) {
    }

    public function length(): int
    {
        return $this->end - $this->start + 1;
    }
}