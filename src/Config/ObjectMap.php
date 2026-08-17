<?php

declare(strict_types=1);

namespace ExcelMigrator\Config;

final class ObjectMap
{
    public const MAP = [

        'PHPExcel_Writer_Excel2007' => [
            'Xlsx',
            'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
        ],

        'PHPExcel_Writer_Excel5' => [
            'Xls',
            'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
        ],

        'PHPExcel_Writer_CSV' => [
            'Csv',
            'PhpOffice\\PhpSpreadsheet\\Writer\\Csv',
        ],
        'PHPExcel_Worksheet' => [
            'Worksheet',
            'PhpOffice\\PhpSpreadsheet\\Worksheet\\Worksheet',
        ],
        'PHPExcel_RichText' => [
            'RichText',
            'PhpOffice\\PhpSpreadsheet\\RichText\\RichText',
        ],
        'PHPExcel_Worksheet_Drawing' => [
            'Drawing',
            'PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing',
        ],
        
    ];
}