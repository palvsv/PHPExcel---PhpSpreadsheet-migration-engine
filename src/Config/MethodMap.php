<?php

declare(strict_types=1);

namespace ExcelMigrator\Config;

final class MethodMap
{
    /**
     * method => configuration
     */
    public const METHODS = [

        /*
        |--------------------------------------------------------------------------
        | Coordinate methods
        |--------------------------------------------------------------------------
        */

        'setCellValueByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'setCellValue',
            'args' => 3,
        ],

        'setCellValueExplicitByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'setCellValueExplicit',
            'args' => 4,
        ],

        'getCellByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getCell',
            'args' => 2,
        ],

        'cellExistsByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'cellExists',
            'args' => 2,
        ],

        'freezePaneByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'freezePane',
            'args' => 2,
        ],

        'getStyleByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getStyle',
            'args' => 2,
        ],

        'getCommentByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getComment',
            'args' => 2,
        ],

        'getHyperlinkByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getHyperlink',
            'args' => 2,
        ],

        'getDataValidationByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getDataValidation',
            'args' => 2,
        ],

        'getProtectionByColumnAndRow' => [
            'type' => 'coordinate',
            'new'  => 'getProtection',
            'args' => 2,
        ],

        /*
        |--------------------------------------------------------------------------
        | Range methods
        |--------------------------------------------------------------------------
        */

        'mergeCellsByColumnAndRow' => [
            'type' => 'range',
            'new'  => 'mergeCells',
            'args' => 4,
        ],

        'setAutoFilterByColumnAndRow' => [
            'type' => 'range',
            'new'  => 'setAutoFilter',
            'args' => 4,
        ],
    ];
}