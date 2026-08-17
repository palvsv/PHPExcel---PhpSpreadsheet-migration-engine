<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;

final class ColumnIndexRule extends Rule
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

        if ($node->class->toString() !== 'PHPExcel_Cell') {
            return null;
        }

        if ($node->name->toString() !== 'stringFromColumnIndex') {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $arg = $node->args[0]->value;

        $argument = $this->expr($arg);

        $this->replaceNode(
            $node->class,
            'Coordinate'
        );

        $this->replaceNode(
            $node->name,
            'stringFromColumnIndex'
        );

        $this->replaceNode(
            $arg,
            '(' . $argument . ') + 1'
        );

        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate'
        );

        return null;
    }
}