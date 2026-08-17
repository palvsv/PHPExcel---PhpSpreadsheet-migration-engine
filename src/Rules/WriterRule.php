<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use PhpParser\Node;

class WriterRule extends Rule
{
    private array $writers = [

        'PHPExcel_Writer_Excel2007' => 'Xlsx',

        'PHPExcel_Writer_Excel5' => 'Xls',

        'PHPExcel_Writer_CSV' => 'Csv',

    ];

    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\New_) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        $class = $node->class->toString();

        if (!isset($this->writers[$class])) {
            return null;
        }

        // $this->patch(

        //     $node->class->getStartFilePos(),

        //     $node->class->getEndFilePos(),

        //     $this->writers[$class]

        // );
        $this->replaceNode(
            $node->class,
            $this->writers[$class]
        );
        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx'
        );
        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Writer\\Xls'
        );
        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Writer\\Csv'
        );
        return null;
    }
}