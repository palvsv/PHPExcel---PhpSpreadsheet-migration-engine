<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Config\MethodMap;
use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class MethodCallRule extends Rule
{
    public function enterNode(Node $node)
    {
        if (!$node instanceof MethodCall) {
            return null;
        }

        if (!$node->name instanceof Node\Identifier) {
            return null;
        }

        $method = $node->name->toString();

        $config = MethodMap::METHODS[$method] ?? null;

        if ($config === null) {
            return null;
        }

        switch ($config['type']) {

            case 'coordinate':
                $this->convertCoordinate($node, $config);
                break;

            case 'range':
                $this->convertRange($node, $config);
                break;

            case 'rename':
                $this->convertRename($node, $config);
                break;
        }

        return null;
    }

    /**
     * ------------------------------------------------------------
     * Coordinate methods
     * ------------------------------------------------------------
     *
     * Example:
     *
     * $sheet->setCellValueByColumnAndRow($col, $row, $value)
     *
     * becomes:
     *
     * $sheet->setCellValue(
     *     Coordinate::stringFromColumnIndex(($col)+1) . ($row),
     *     $value
     * )
     *
     * We deliberately DO NOT replace the whole MethodCall.
     * Instead:
     *
     *   1. Rename method
     *   2. Replace first two arguments with coordinate expression
     *
     * This allows nested MethodCalls to be migrated independently.
     */
    private function convertCoordinate(
        MethodCall $node,
        array $config
    ): void {

        $requiredArgs = $config['args'] ?? 2;

        if (count($node->args) < $requiredArgs) {
            return;
        }

        /*
         * --------------------------------------------------------
         * 1. Replace only the method name
         * --------------------------------------------------------
         */
        $this->replaceNode(
            $node->name,
            $config['new']
        );

        /*
         * --------------------------------------------------------
         * 2. Replace the first two arguments
         * --------------------------------------------------------
         */
        $column = $this->expr(
            $node->args[0]->value
        );

        $row = $this->expr(
            $node->args[1]->value
        );

        $coordinate = $this->coordinateExpression(
            $column,
            $row
        );

        /*
         * Include the argument node positions.
         *
         * Example:
         *
         * setCellValueByColumnAndRow(
         *     0,
         *     1,
         *     $value
         * )
         *
         * We replace:
         *
         *     0, 1
         *
         * with:
         *
         *     Coordinate::stringFromColumnIndex((0)+1) . (1)
         */
        $start = $node->args[0]->getStartFilePos();
        $end   = $node->args[1]->getEndFilePos();

        $this->patch(
            $start,
            $end,
            $coordinate
        );

        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate'
        );
    }

    /**
     * ------------------------------------------------------------
     * Range methods
     * ------------------------------------------------------------
     *
     * Example:
     *
     * mergeCellsByColumnAndRow(
     *     $col1,
     *     $row1,
     *     $col2,
     *     $row2
     * )
     *
     * becomes:
     *
     * mergeCells(
     *     Coordinate::stringFromColumnIndex(($col1)+1) . ($row1)
     *     . ":"
     *     . Coordinate::stringFromColumnIndex(($col2)+1) . ($row2)
     * )
     */
    private function convertRange(
        MethodCall $node,
        array $config
    ): void {

        $requiredArgs = $config['args'] ?? 4;

        if (count($node->args) < $requiredArgs) {
            return;
        }

        /*
         * --------------------------------------------------------
         * 1. Replace method name
         * --------------------------------------------------------
         */
        $this->replaceNode(
            $node->name,
            $config['new']
        );

        /*
         * --------------------------------------------------------
         * 2. Build range expression
         * --------------------------------------------------------
         */
        $range = $this->rangeExpression(
            $this->expr($node->args[0]->value),
            $this->expr($node->args[1]->value),
            $this->expr($node->args[2]->value),
            $this->expr($node->args[3]->value)
        );

        /*
         * --------------------------------------------------------
         * 3. Replace the four coordinate arguments
         * --------------------------------------------------------
         */
        $start = $node->args[0]->getStartFilePos();
        $end   = $node->args[3]->getEndFilePos();

        $this->patch(
            $start,
            $end,
            $range
        );

        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate'
        );
    }

    /**
     * ------------------------------------------------------------
     * Simple rename
     * ------------------------------------------------------------
     */
    private function convertRename(
        MethodCall $node,
        array $config
    ): void {

        $this->replaceNode(
            $node->name,
            $config['new']
        );
    }
}