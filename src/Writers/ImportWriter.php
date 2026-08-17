<?php

declare(strict_types=1);

namespace ExcelMigrator\Writers;

final class ImportWriter
{
    public static function insert(
        string $code,
        array $imports
    ): string {

        if (empty($imports)) {
            return $code;
        }

        /*
         * Remove imports that already exist in the source file.
         */
        $importsToAdd = [];

        foreach (array_keys($imports) as $class) {

            $pattern = '/^\s*use\s+'
                . preg_quote($class, '/')
                . '\s*;\s*$/mi';

            if (!preg_match($pattern, $code)) {
                $importsToAdd[] = $class;
            }
        }

        /*
         * Nothing new to add.
         */
        if (empty($importsToAdd)) {
            return $code;
        }

        sort($importsToAdd);

        $uses = '';

        foreach ($importsToAdd as $class) {
            $uses .= "use {$class};\n";
        }

        /*
         * Case 1:
         *
         * namespace App;
         */
        if (
            preg_match(
                '/^namespace\s+[^;]+;/m',
                $code,
                $m,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $line = $m[0][0];
            $pos  = $m[0][1];

            $insert = $pos + strlen($line);

            return substr($code, 0, $insert)
                . "\n\n"
                . $uses
                . substr($code, $insert);
        }

        /*
         * Case 2:
         *
         * No namespace
         */
        if (
            preg_match(
                '/<\?php/',
                $code,
                $m,
                PREG_OFFSET_CAPTURE
            )
        ) {
            $insert = $m[0][1] + strlen('<?php');

            return substr($code, 0, $insert)
                . "\n\n"
                . $uses
                . substr($code, $insert);
        }

        return $code;
    }
}