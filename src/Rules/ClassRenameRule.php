<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use PhpParser\Node;
use ExcelMigrator\Config\PhpExcelMap;

class ClassRenameRule extends Rule
{
    /**
     * PHPExcel -> PhpSpreadsheet class mapping
     */
    private array $map = [];

    public function __construct()
    {
        $this->map = PhpExcelMap::CLASS_MAP;
    }

    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Name) {
            return null;
        }

        $old = $node->toString();

        if (!isset($this->map[$old])) {
            return null;
        }

        // $this->patch(
        //     $node->getStartFilePos(),
        //     $node->getEndFilePos(),
        //     $this->map[$old]
        // );
        $this->replaceNode(
            $node,
            $this->map[$old]
        );
        return null;
    }
}