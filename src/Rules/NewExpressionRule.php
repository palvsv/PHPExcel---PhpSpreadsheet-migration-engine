<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Config\ObjectMap;
use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;

final class NewExpressionRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof New_) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        $oldClass = $node->class->toString();

        $config = ObjectMap::MAP[$oldClass] ?? null;

        if ($config === null) {
            return null;
        }

        /*
         * ObjectMap format:
         *
         * 'PHPExcel_Writer_Excel2007' => [
         *     'Xlsx',
         *     'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx'
         * ]
         */

        [$newClass, $import] = $config;

        $this->replaceNode(
            $node->class,
            $newClass
        );

        $this->addImport($import);

        return null;
    }
}