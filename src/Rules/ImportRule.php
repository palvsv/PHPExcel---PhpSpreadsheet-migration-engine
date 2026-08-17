<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Rule;
use ExcelMigrator\Config\PhpExcelMap;
use PhpParser\Node;

class ImportRule extends Rule
{
    /**
     * Classes found while scanning.
     */
    private array $requiredImports = [];

    /**
     * Existing use statements.
     */
    private array $existingImports = [];

    /**
     * Namespace node.
     */
    private ?Node\Stmt\Namespace_ $namespace = null;

    public function beforeTraverse(array $nodes)
    {
        $this->requiredImports = [];
        $this->existingImports = [];
        $this->namespace = null;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->existingImports[] = $use->name->toString();
            }
        }

         if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = $node;
        }

        if ($node instanceof Node\Name) {

            $shortName = $node->toString();

            if (isset(PhpExcelMap::IMPORT_MAP[$shortName])) {

                $this->requiredImports[$shortName]
                    = PhpExcelMap::IMPORT_MAP[$shortName];

            }

        }

    }

    public function afterTraverse(array $nodes)
    {
        if (!$this->namespace) {
            return;
        }

        $newImports = array_diff(
            $this->requiredImports,
            $this->existingImports
        );

        if (!$newImports) {
            return;
        }

        $code = "\n";
        foreach ($newImports as $import) {
            $code .= "use {$import};\n";
        }
        // $this->patch($this->namespace->getEndFilePos(),$this->namespace->getEndFilePos(),"\n" . $code);
        $this->replaceNode(
            $this->namespace,
            "\n" . $code
        );
    }
}