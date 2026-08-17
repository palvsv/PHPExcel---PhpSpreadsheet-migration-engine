<?php

declare(strict_types=1);

namespace ExcelMigrator\Config;

final class StaticCallMap
{
    public const MAP = [

        'PHPExcel_Cell::stringFromColumnIndex' => [
            'class' => 'Coordinate',
            'method' => 'stringFromColumnIndex',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate',
            'zeroBased' => true,
        ],

        'PHPExcel_Shared_Date::ExcelToPHP' => [
            'class' => 'Date',
            'method' => 'excelToTimestamp',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Date',
        ],

        'PHPExcel_ReferenceHelper::getInstance' => [
            'class' => 'ReferenceHelper',
            'method' => 'getInstance',
            'import' => 'PhpOffice\\PhpSpreadsheet\\ReferenceHelper',
        ],
        'PHPExcel_Style_NumberFormat::toFormattedString' => [
            'class' => 'NumberFormat',
            'method' => 'toFormattedString',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
        ],

    ];
}