<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use PhpParser\Node;

class SpreadsheetRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\New_) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        if ($node->class->toString() !== 'PHPExcel') {
            return null;
        }

        // $this->patch(
        //     $node->class->getStartFilePos(),
        //     $node->class->getEndFilePos(),
        //     'Spreadsheet'
        // );
        $this->replaceNode(
            $node->class,
            'Spreadsheet'
        );
        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Spreadsheet'
        );
        return null;
    }
}