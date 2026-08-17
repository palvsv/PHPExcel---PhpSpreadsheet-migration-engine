<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
// require __DIR__ . '/config.php';

use ExcelMigrator\Migrator;
use ExcelMigrator\Rules\CoordinateMethodRule;
// use ExcelMigrator\Rules\RangeMethodRule;
use ExcelMigrator\Rules\IOFactoryRule;
use ExcelMigrator\Rules\SpreadsheetRule;
use ExcelMigrator\Rules\NewExpressionRule;
use ExcelMigrator\Rules\MethodCallRule;
use ExcelMigrator\Rules\StaticCallRule;
use ExcelMigrator\Rules\ConstantFetchRule;
// use ExcelMigrator\Rules\ColumnIndexRule;

if ($argc < 2) {
    echo "Usage:\n";
    echo "php migrate.php <file-or-directory>\n";
    exit(1);
}

$target = $argv[1];
$dryRun = in_array('--dry-run', $argv, true);

$migrator = new Migrator($dryRun);

// $migrator->addRule(new CoordinateMethodRule());
// $migrator->addRule(new RangeMethodRule());
$migrator->addRule(new IOFactoryRule());
$migrator->addRule(new StaticCallRule());
$migrator->addRule(new ConstantFetchRule());
// $migrator->addRule(new ColumnIndexRule());
$migrator->addRule(new SpreadsheetRule());
// NEW
$migrator->addRule(new MethodCallRule());
$migrator->addRule(new NewExpressionRule());

$migrator->migrate($target);