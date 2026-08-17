<?php

declare(strict_types=1);

namespace ExcelMigrator;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use ExcelMigrator\Builders\ExpressionPrinter;
use ExcelMigrator\Helpers\CoordinateHelper;

abstract class Rule extends NodeVisitorAbstract
{
    protected array $patches = [];

    /**
     * Imports requested by this rule.
     */
    protected array $imports = [];
    /**
     * Number of replacements.
     */
    protected int $count = 0;

    final protected function patch(
        int $start,
        int $end,
        string $replacement
    ): void {
        $this->patches[] = new Patch(
            $start,
            $end,
            $replacement,
            static::class,
            0,
            ''
        );
        $this->count++;
    }

    /**
     * Register an import.
     */
    final protected function addImport(string $class): void
    {
        $this->imports[$class] = true;
    }

    /**
     * Return imports.
     */
    final public function getImports(): array
    {
        return array_keys($this->imports);
    }

    final public function reset(): void
    {
        $this->patches = [];
        $this->imports = [];
        $this->count = 0;
    }

    final public function getPatches(): array
    {
        return $this->patches;
    }
    public function getCount(): int
    {
        return $this->count;
    }

    final protected function expr(Node\Expr $expr): string
    {
        return ExpressionPrinter::print($expr);
    }

    final protected function coordinate(
        string $column,
        string $row
    ): string {
        return CoordinateHelper::cell(
            $column,
            $row
        );
    }

    protected function replaceNode(
    Node $node,
    string $replacement
    ): void {

        echo PHP_EOL;
        echo "Replacing node: " . get_class($node) . PHP_EOL;

        if ($node instanceof Node\Name) {
            echo "Code : " . $node->toString() . PHP_EOL;
        } else {
            echo "Node type is not Name" . PHP_EOL;
        }

        echo "Start : " . $node->getStartFilePos() . PHP_EOL;
        echo "End   : " . $node->getEndFilePos() . PHP_EOL;

        $this->patches[] = new Patch(
            $node->getStartFilePos(),
            $node->getEndFilePos(),
            $replacement,
            static::class,
            $node->getStartLine(),
            ''
        );

        $this->count++;
    }

    protected function replace(
        Node $node,
        string $replacement
    ): void {

        $this->patches[] = new Patch(
            $node->getStartFilePos(),
            $node->getEndFilePos(),
            $replacement,
            static::class,
            $node->getStartLine(),
            ''
        );
        $this->count++;
    }

    protected function coordinateExpression(string $column,string $row): string
    {
        return 'Coordinate::stringFromColumnIndex((' .$column .')+1) . (' .$row .')';
    }

    protected function rangeExpression(string $col1,string $col2,string $row1,string $row2): string 
    {
        return 'Coordinate::stringFromColumnIndex((' .$col1 .')+1) . (' .$row1 .'.' .'":".' .'Coordinate::stringFromColumnIndex((' .$col2 .')+1) . (' .$row2.')';
    }
}