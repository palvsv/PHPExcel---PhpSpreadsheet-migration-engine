<?php

declare(strict_types=1);

namespace ExcelMigrator\Builders;

use PhpParser\Node\Expr;
use PhpParser\PrettyPrinter\Standard;

final class ExpressionPrinter
{
    private static ?Standard $printer = null;

    private function __construct()
    {
        // Prevent instantiation
    }

    private static function printer(): Standard
    {
        if (self::$printer === null) {
            self::$printer = new Standard();
        }

        return self::$printer;
    }

    public static function print(Expr $expr): string
    {
        return self::printer()->prettyPrintExpr($expr);
    }
}