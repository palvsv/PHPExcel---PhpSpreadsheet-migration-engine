<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use PhpParser\Node;

final class IOFactoryRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\StaticCall) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        if ($node->class->toString() !== 'PHPExcel_IOFactory') {
            return null;
        }

        $this->replaceNode(
            $node->class,
            'IOFactory'
        );

        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\IOFactory'
        );

        return null;
    }
}