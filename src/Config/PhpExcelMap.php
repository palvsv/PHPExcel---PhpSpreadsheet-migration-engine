<?php

declare(strict_types=1);

namespace ExcelMigrator\Config;

final class PhpExcelMap
{
    public const CLASS_MAP = [

        'PHPExcel' => 'Spreadsheet',
        'PHPExcel_IOFactory' => 'IOFactory',

        'PHPExcel_Writer_Excel2007' => 'Xlsx',
        'PHPExcel_Writer_Excel5' => 'Xls',
        'PHPExcel_Writer_CSV' => 'Csv',

        'PHPExcel_Reader_Excel2007' => 'Xlsx',
        'PHPExcel_Reader_Excel5' => 'Xls',

        'PHPExcel_Worksheet' => 'Worksheet',

        'PHPExcel_Cell' => 'Coordinate',
        'PHPExcel_Cell_DataType' => 'DataType',

        'PHPExcel_Style_Fill' => 'Fill',
        'PHPExcel_Style_Border' => 'Border',
        'PHPExcel_Style_Alignment' => 'Alignment',
        'PHPExcel_Style_Color' => 'Color',
        'PHPExcel_Style_NumberFormat' => 'NumberFormat',

    ];


    public const IMPORT_MAP = [

        'Spreadsheet'
            => 'PhpOffice\\PhpSpreadsheet\\Spreadsheet',

        'IOFactory'
            => 'PhpOffice\\PhpSpreadsheet\\IOFactory',

        'Xlsx'
            => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',

        'Xls'
            => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',

        'Csv'
            => 'PhpOffice\\PhpSpreadsheet\\Writer\\Csv',

        'Coordinate'
            => 'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate',

        'DataType'
            => 'PhpOffice\\PhpSpreadsheet\\Cell\\DataType',

        'Worksheet'
            => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\Worksheet',

        'Fill'
            => 'PhpOffice\\PhpSpreadsheet\\Style\\Fill',

        'Border'
            => 'PhpOffice\\PhpSpreadsheet\\Style\\Border',

        'Alignment'
            => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',

        'Color'
            => 'PhpOffice\\PhpSpreadsheet\\Style\\Color',

        'NumberFormat'
            => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',

    ];
}