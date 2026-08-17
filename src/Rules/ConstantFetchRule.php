<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Config\ConstantMap;
use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;

final class ConstantFetchRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof ClassConstFetch) {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        if (!$node->name instanceof Node\Identifier) {
            return null;
        }

        $class = $node->class->toString();
        $constant = $node->name->toString();

        $key = $class . '::' . $constant;

        $config = ConstantMap::MAP[$key] ?? null;

        if ($config === null) {
            return null;
        }

        $this->replaceNode(
            $node->class,
            $config['class']
        );

        $this->addImport(
            $config['import']
        );

        return null;
    }
}