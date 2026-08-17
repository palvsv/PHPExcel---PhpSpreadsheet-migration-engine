<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Config\StaticCallMap;
use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;

final class StaticCallRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof StaticCall) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        if (!$node->name instanceof Node\Identifier) {
            return null;
        }

        $class = $node->class->toString();
        $method = $node->name->toString();

        $key = $class . '::' . $method;

        $config = StaticCallMap::MAP[$key] ?? null;

        if ($config === null) {
            return null;
        }

        /*
         * --------------------------------------------------------
         * Special case:
         *
         * PHPExcel:
         *
         * PHPExcel_Cell::stringFromColumnIndex($value)
         *
         * uses zero-based column indexes.
         *
         * PhpSpreadsheet:
         *
         * Coordinate::stringFromColumnIndex()
         *
         * expects one-based indexes.
         *
         * Therefore:
         *
         * $value
         *
         * becomes:
         *
         * ($value) + 1
         * --------------------------------------------------------
         */
        if (!empty($config['zeroBased'])) {

            if (count($node->args) < 1) {
                return null;
            }

            $argument = $node->args[0]->value;

            $argumentCode = $this->expr($argument);

            $replacement =
                '(' .
                $argumentCode .
                ') + 1';

            $this->replace(
                $argument,
                $replacement
            );
        }

        /*
         * --------------------------------------------------------
         * Replace class
         * --------------------------------------------------------
         */
        $this->replaceNode(
            $node->class,
            $config['class']
        );

        /*
         * --------------------------------------------------------
         * Replace method
         * --------------------------------------------------------
         */
        $this->replaceNode(
            $node->name,
            $config['method']
        );

        /*
         * --------------------------------------------------------
         * Add import
         * --------------------------------------------------------
         */
        $this->addImport(
            $config['import']
        );

        return null;
    }
}