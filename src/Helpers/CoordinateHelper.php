<?php

declare(strict_types=1);

namespace ExcelMigrator\Helpers;

final class CoordinateHelper
{
    private function __construct()
    {
        // Static helper
    }

    /**
     * Build a coordinate expression.
     *
     * Input:
     *   $column = "$c"
     *   $row    = "$r"
     *
     * Output:
     *   Coordinate::stringFromColumnIndex(($c)+1) . ($r)
     */
    public static function cell(
        string $column,
        string $row
    ): string {

        return sprintf(
            'Coordinate::stringFromColumnIndex((%s)+1) . (%s)',
            $column,
            $row
        );

    }

    /**
     * Build an Excel range.
     *
     * Example:
     * A1:B10
     */
    public static function range(
        string $column1,
        string $row1,
        string $column2,
        string $row2
    ): string {

        return sprintf(
            '%s . ":" . %s',
            self::cell($column1, $row1),
            self::cell($column2, $row2)
        );

    }
}