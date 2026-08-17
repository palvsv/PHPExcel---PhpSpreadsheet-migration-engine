<?php

declare(strict_types=1);

namespace ExcelMigrator\Builders;

final class CodeBuilder
{
    private function __construct()
    {
        // Static class
    }

    /**
     * Build an object method call.
     *
     * Example:
     * $sheet->setCellValue('A1', $value)
     */
    public static function methodCall(
        string $object,
        string $method,
        array $arguments = []
    ): string {

        return sprintf(
            '%s->%s(%s)',
            $object,
            $method,
            implode(', ', $arguments)
        );

    }

    /**
     * Build a static method call.
     *
     * Example:
     * IOFactory::createWriter(...)
     */
    public static function staticCall(
        string $class,
        string $method,
        array $arguments = []
    ): string {

        return sprintf(
            '%s::%s(%s)',
            $class,
            $method,
            implode(', ', $arguments)
        );

    }

    /**
     * Build a class instantiation.
     *
     * Example:
     * new Spreadsheet()
     */
    public static function newObject(
        string $class,
        array $arguments = []
    ): string {

        return sprintf(
            'new %s(%s)',
            $class,
            implode(', ', $arguments)
        );

    }

    /**
     * Build a function call.
     *
     * Example:
     * strtoupper($value)
     */
    public static function functionCall(
        string $function,
        array $arguments = []
    ): string {

        return sprintf(
            '%s(%s)',
            $function,
            implode(', ', $arguments)
        );

    }

}