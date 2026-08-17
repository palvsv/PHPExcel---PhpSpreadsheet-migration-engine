<?php

declare(strict_types=1);

namespace ExcelMigrator\Config;

final class ConstantMap
{
    public const MAP = [

        'PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE' => [
            'class' => 'PageSetup',
            'constant' => 'ORIENTATION_LANDSCAPE',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup',
        ],

        'PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT' => [
            'class' => 'PageSetup',
            'constant' => 'ORIENTATION_PORTRAIT',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup',
        ],

        'PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4' => [
            'class' => 'PageSetup',
            'constant' => 'PAPERSIZE_A4',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup',
        ],

        'PHPExcel_Style_Fill::FILL_SOLID' => [
            'class' => 'Fill',
            'constant' => 'FILL_SOLID',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Fill',
        ],

        'PHPExcel_Style_Border::BORDER_THIN' => [
            'class' => 'Border',
            'constant' => 'BORDER_THIN',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Border',
        ],

        'PHPExcel_Style_Border::BORDER_THICK' => [
            'class' => 'Border',
            'constant' => 'BORDER_THICK',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Border',
        ],

        'PHPExcel_Style_Alignment::HORIZONTAL_CENTER' => [
            'class' => 'Alignment',
            'constant' => 'HORIZONTAL_CENTER',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
        ],

        'PHPExcel_Style_Alignment::HORIZONTAL_RIGHT' => [
            'class' => 'Alignment',
            'constant' => 'HORIZONTAL_RIGHT',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
        ],

        'PHPExcel_Style_Border::BORDER_DOUBLE' => [
            'class' => 'Border',
            'constant' => 'BORDER_DOUBLE',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Border',
        ],

        'PHPExcel_Style_Alignment::HORIZONTAL_LEFT' => [
            'class' => 'Alignment',
            'constant' => 'HORIZONTAL_LEFT',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
        ],

        'PHPExcel_Style_Alignment::VERTICAL_CENTER' => [
            'class' => 'Alignment',
            'constant' => 'VERTICAL_CENTER',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
        ],
        'PHPExcel_Style_Alignment::VERTICAL_TOP' => [
            'class' => 'Alignment',
            'constant' => 'VERTICAL_TOP',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
        ],
        
        'PHPExcel_Worksheet::BREAK_ROW' => [
            'class' => 'Worksheet',
            'constant' => 'BREAK_ROW',
            'import' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\Worksheet',
        ],

    ];
}