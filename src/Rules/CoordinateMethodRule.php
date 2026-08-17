<?php

declare(strict_types=1);

namespace ExcelMigrator\Rules;

use ExcelMigrator\Builders\CodeBuilder;
use ExcelMigrator\Rule;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

final class CoordinateMethodRule extends Rule
{
    /**
     * oldMethod => newMethod
     */
    private const METHODS = [

        'setCellValueByColumnAndRow'       => 'setCellValue',
        'setCellValueExplicitByColumnAndRow' => 'setCellValueExplicit',

        'getCellByColumnAndRow'            => 'getCell',
        'getStyleByColumnAndRow'           => 'getStyle',
        'getCommentByColumnAndRow'         => 'getComment',
        'getHyperlinkByColumnAndRow'       => 'getHyperlink',

        'cellExistsByColumnAndRow'         => 'cellExists',

    ];

    public function enterNode(Node $node)
    {
        if (!$node instanceof MethodCall) {
            return null;
        }

        if (!$node->name instanceof Node\Identifier) {
            return null;
        }

        $oldMethod = $node->name->toString();

        if (!isset(self::METHODS[$oldMethod])) {
            return null;
        }

        if (count($node->args) < 2) {
            return null;
        }

        $newMethod = self::METHODS[$oldMethod];

        $object = $this->expr($node->var);

        $column = $this->expr($node->args[0]->value);
        $row    = $this->expr($node->args[1]->value);

        $arguments = [];

        $arguments[] = $this->coordinate(
            $column,
            $row
        );

        for ($i = 2; $i < count($node->args); $i++) {

            $arguments[] = $this->expr(
                $node->args[$i]->value
            );

        }

        $replacement = CodeBuilder::methodCall(
            $object,
            $newMethod,
            $arguments
        );

        // $this->patch(
        //     $node->getStartFilePos(),
        //     $node->getEndFilePos(),
        //     $replacement
        // );
        $this->replaceNode(
            $node,
            $replacement
        );
        $this->addImport(
            'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate'
        );

        return null;
    }
}