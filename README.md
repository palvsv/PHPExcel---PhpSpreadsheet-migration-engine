# PHPExcel → PhpSpreadsheet Migration Engine

An AST-based migration engine for automatically converting legacy **PHPExcel** PHP code to **PhpSpreadsheet**.

This tool is designed for large legacy PHP applications where PHPExcel is used across many files and manually migrating every class, method, constant, coordinate operation, and object creation would be time-consuming and error-prone.

The migration engine parses PHP source code using [`nikic/php-parser`](https://github.com/nikic/PHP-Parser), analyzes the PHP syntax tree, applies migration rules, and writes the migrated PHP source code.

---

## Table of Contents

* [Overview](#overview)
* [Why This Tool](#why-this-tool)
* [Key Concept](#key-concept)
* [How It Works](#how-it-works)
* [Features](#features)
* [Project Structure](#project-structure)
* [Requirements](#requirements)
* [Installation](#installation)
* [Setup](#setup)
* [Basic Usage](#basic-usage)
* [Migration Workflow](#migration-workflow)
* [Example: Before and After](#example-before-and-after)
* [What Gets Migrated](#what-gets-migrated)
* [Class Mapping](#class-mapping)
* [Method Mapping](#method-mapping)
* [Constant Mapping](#constant-mapping)
* [Coordinate Conversion](#coordinate-conversion)
* [Import Handling](#import-handling)
* [Migration Rules](#migration-rules)
* [Adding a New Migration Rule](#adding-a-new-migration-rule)
* [Updating the Migration Engine](#updating-the-migration-engine)
* [Running the Migrator](#running-the-migrator)
* [Backups and Safety](#backups-and-safety)
* [Migration Reports](#migration-reports)
* [Debugging](#debugging)
* [Common Migration Issues](#common-migration-issues)
* [Important PhpSpreadsheet Differences](#important-phpspreadsheet-differences)
* [What This Tool Does Not Do](#what-this-tool-does-not-do)
* [Recommended Migration Strategy](#recommended-migration-strategy)
* [Testing After Migration](#testing-after-migration)
* [Adding Support for Legacy Code](#adding-support-for-legacy-code)
* [Limitations](#limitations)
* [Contributing](#contributing)
* [Roadmap](#roadmap)
* [License](#license)

---

# Overview

PHPExcel has been deprecated for many years and has been replaced by PhpSpreadsheet.

Migrating a large application manually can be difficult because PHPExcel code may contain:

* Legacy class names
* Legacy static method calls
* Legacy constants
* Legacy worksheet APIs
* Legacy coordinate APIs
* Legacy writer APIs
* Legacy reader APIs
* Old object creation syntax
* Old namespace assumptions
* PHPExcel-specific helper methods

For a small project, manual migration may be practical.

For a large application with hundreds or thousands of PHP files, manually changing every occurrence is slow and increases the possibility of missing an important API difference.

This project provides an automated migration layer.

The engine parses PHP code into an **Abstract Syntax Tree (AST)** and modifies the syntax tree according to PHPExcel → PhpSpreadsheet migration rules.

---

# Why This Tool

A simple search-and-replace migration is not enough for a large PHPExcel application.

For example:

```php
PHPExcel_Cell::stringFromColumnIndex($column);
```

cannot always be safely migrated with a simple text replacement.

PhpSpreadsheet may require:

```php
\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
```

Similarly:

```php
PHPExcel_IOFactory::load($file);
```

becomes:

```php
\PhpOffice\PhpSpreadsheet\IOFactory::load($file);
```

and:

```php
new PHPExcel();
```

becomes:

```php
new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

There can also be differences in:

* namespaces
* method names
* constants
* parameters
* object types
* return types
* worksheet APIs
* writer APIs

The purpose of this migration engine is to automate the **mechanical and repetitive** part of the migration while leaving application-specific behavior for manual verification.

---

# Key Concept

The central idea of this project is:

> **Parse PHP code instead of modifying PHP code as plain text.**

The engine uses `nikic/php-parser`.

The general process is:

```text
PHP Source
    |
    v
PHP Parser
    |
    v
Abstract Syntax Tree (AST)
    |
    v
Migration Rules
    |
    +---- Class mapping
    |
    +---- Method mapping
    |
    +---- Constant mapping
    |
    +---- Coordinate conversion
    |
    +---- Object creation
    |
    +---- Import handling
    |
    +---- Other rules
    |
    v
Modified AST
    |
    v
Pretty Printer
    |
    v
Migrated PHP Source
```

This approach is much safer than blindly replacing strings.

---

# How It Works

## 1. Read PHP source

The migrator reads the PHP file:

```php
<?php

$objPHPExcel = new PHPExcel();
```

## 2. Parse the source

`nikic/php-parser` converts the source code into an AST.

Conceptually:

```text
new PHPExcel()
       |
       v
New_
       |
       +---- PHPExcel
```

## 3. Apply migration rules

The migration engine detects the PHPExcel class and applies the appropriate mapping.

```text
PHPExcel
   ↓
PhpOffice\PhpSpreadsheet\Spreadsheet
```

## 4. Modify the AST

The AST is changed rather than performing a text replacement.

## 5. Generate PHP

The modified AST is converted back into PHP source code.

Result:

```php
<?php

$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

---

# Features

The migration engine currently focuses on the most common PHPExcel migration patterns.

## Class migration

Example:

```php
PHPExcel
```

becomes:

```php
\PhpOffice\PhpSpreadsheet\Spreadsheet
```

Other PHPExcel classes are mapped according to the migration map.

---

## Static class calls

Example:

```php
PHPExcel_IOFactory::load($file);
```

becomes:

```php
\PhpOffice\PhpSpreadsheet\IOFactory::load($file);
```

---

## Constants

PHPExcel constants can be mapped to their PhpSpreadsheet equivalents.

Example:

```php
PHPExcel_Settings::PCLZIP
```

can be migrated according to the configured constant map.

---

## Object creation

Example:

```php
new PHPExcel_Worksheet_Drawing();
```

becomes the corresponding PhpSpreadsheet class.

---

## Method migration

Methods whose names or APIs changed between PHPExcel and PhpSpreadsheet can be migrated through migration rules.

Example:

```php
$worksheet->setCellValueByColumnAndRow(
    $column,
    $row,
    $value
);
```

can be converted to the appropriate PhpSpreadsheet API.

---

## Coordinate migration

PHPExcel and PhpSpreadsheet have different APIs for several coordinate-related operations.

For example, code using:

```php
PHPExcel_Cell::stringFromColumnIndex($column);
```

can be migrated to:

```php
\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
```

---

## Namespace/import handling

The migrator can introduce the required PhpSpreadsheet classes and namespaces where appropriate.

---

# Project Structure

A typical project structure looks like:

```text
PHPExcelMigrator/
│
├── migrate.php
│
├── composer.json
│
├── composer.lock
│
├── src/
│   │
│   ├── Migrator.php
│   │
│   ├── PhpExcelMap.php
│   │
│   ├── ImportWriter.php
│   │
│   └── Rules/
│       │
│       ├── Rule.php
│       ├── ClassRule.php
│       ├── MethodRule.php
│       ├── ConstantRule.php
│       ├── CoordinateRule.php
│       └── ...
│
└── README.md
```

The exact structure may change as the project evolves.

---

# Requirements

## PHP

Recommended:

```text
PHP 8.2+
```

The migration engine itself is intended to run on a modern PHP version.

---

## Composer

Composer is required.

Check:

```bash
composer --version
```

---

## nikic/php-parser

The migration engine uses:

```text
nikic/php-parser
```

Install it through Composer.

---

## PhpSpreadsheet

The target application must have PhpSpreadsheet installed.

Install:

```bash
composer require phpoffice/phpspreadsheet
```

---

# Installation

Clone the repository:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
```

Enter the project:

```bash
cd YOUR_REPOSITORY
```

Install dependencies:

```bash
composer install
```

If starting a new migration project:

```bash
composer require nikic/php-parser
```

---

# Setup

Before running the migration, make sure the project contains:

```text
vendor/
composer.json
composer.lock
src/
migrate.php
```

The migrator should be able to load Composer's autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

---

# Basic Usage

The migration engine is intended to work on specific files or directories rather than blindly modifying an entire application.

A basic migration command can be structured as:

```bash
php migrate.php path/to/file.php
```

For example:

```bash
php migrate.php reports/BreakdownReport.php
```

Or:

```bash
php migrate.php reports/SightReportDTC.php
```

For directory-based migration:

```bash
php migrate.php reports/
```

The exact command-line options depend on the version of the migrator.

---

# Migration Workflow

The recommended migration process is:

```text
1. Backup the application
        |
        v
2. Install PhpSpreadsheet
        |
        v
3. Run the migrator
        |
        v
4. Review generated changes
        |
        v
5. Fix application-specific differences
        |
        v
6. Run PHP syntax checks
        |
        v
7. Run application tests
        |
        v
8. Test generated Excel files
```

Do not assume that a successful AST migration means the application is fully migrated.

The migrator handles source-code transformations.

It cannot understand every business rule inside the application.

---

# Example: Before and After

## Example 1 — PHPExcel object

### Before

```php
$objPHPExcel = new PHPExcel();
```

### After

```php
$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

---

# Example 2 — IOFactory

### Before

```php
$objPHPExcel = PHPExcel_IOFactory::load($file);
```

### After

```php
$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
```

---

# Example 3 — Drawing

### Before

```php
$oDrawing = new PHPExcel_Worksheet_Drawing();
```

### After

```php
$oDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
```

---

# Example 4 — Coordinate conversion

### Before

```php
$columnName = PHPExcel_Cell::stringFromColumnIndex($column);
```

### After

```php
$columnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
```

---

# Example 5 — Settings

Legacy PHPExcel code may contain:

```php
PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
```

The migration engine maps the relevant PHPExcel API to the PhpSpreadsheet equivalent where a direct replacement exists.

However, not every PHPExcel setting has a one-to-one equivalent in PhpSpreadsheet.

Therefore, settings-related migrations should always be manually tested.

---

# What Gets Migrated

The engine is primarily designed to migrate source-code structures such as:

| PHPExcel Feature           | Migration |
| -------------------------- | --------- |
| PHPExcel classes           | Yes       |
| PHPExcel static classes    | Yes       |
| PHPExcel constants         | Yes       |
| Object creation            | Yes       |
| Static method calls        | Yes       |
| Method mappings            | Yes       |
| Coordinate APIs            | Yes       |
| Namespace changes          | Yes       |
| Imports                    | Yes       |
| Writer classes             | Yes       |
| Reader classes             | Yes       |
| Worksheet classes          | Yes       |
| Drawing classes            | Yes       |
| Application-specific logic | No        |
| Database logic             | No        |
| Business rules             | No        |
| Excel output validation    | No        |

The important distinction is:

> The migrator changes PHPExcel API usage. It does not rewrite your application's business logic.

---

# Class Mapping

The class mappings are maintained in the migration map.

A simplified example:

```php
PHPExcel => PhpOffice\PhpSpreadsheet\Spreadsheet
PHPExcel_IOFactory => PhpOffice\PhpSpreadsheet\IOFactory
PHPExcel_Worksheet_Drawing => PhpOffice\PhpSpreadsheet\Worksheet\Drawing
```

The exact mapping should be maintained centrally rather than duplicated throughout the migration rules.

This makes the engine easier to maintain when new PHPExcel classes are discovered.

---

# Method Mapping

Some PHPExcel methods were renamed or reorganized.

For example, a legacy application may contain:

```php
$worksheet->setCellValueByColumnAndRow(
    $column,
    $row,
    $value
);
```

PhpSpreadsheet provides different APIs for this operation.

A migration rule can therefore convert the AST representation into the required PhpSpreadsheet call.

Method mappings should be handled by rules rather than by global text replacement.

---

# Constant Mapping

PHPExcel constants may also require migration.

Example:

```php
PHPExcel_Settings::PCLZIP
```

A constant mapping rule can identify:

```text
PHPExcel_Settings
        +
PCLZIP
```

and replace it with the appropriate PhpSpreadsheet representation.

Because constants may have been removed or changed, each mapping should be verified against the PhpSpreadsheet version used by the target project.

---

# Coordinate Conversion

Coordinate handling is one of the areas where automatic migration is particularly useful.

PHPExcel code often contains:

```php
PHPExcel_Cell::stringFromColumnIndex($column)
```

PhpSpreadsheet uses:

```php
\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column)
```

Similarly, coordinate-related methods may need to be redirected to the `Coordinate` class.

This is why the migrator uses AST rules rather than simple string replacement.

---

# Import Handling

When a class is migrated to a namespaced PhpSpreadsheet class, the resulting code may require imports.

For example:

```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
```

The migration engine contains import-writing functionality to help manage these generated imports.

The goal is to prevent duplicated or conflicting imports while applying multiple migration rules to the same file.

---

# Migration Rules

The migration engine is rule-based.

A rule is responsible for one type of migration.

Conceptually:

```text
AST Node
   |
   v
Rule
   |
   +---- Does this rule apply?
   |
   +---- Yes
   |       |
   |       v
   |    Transform node
   |
   +---- No
           |
           v
        Next rule
```

This makes the migration engine extensible.

Instead of adding more and more special cases to `Migrator.php`, a new migration behavior can be implemented as a dedicated rule.

---

# Rule Interface

Rules should implement the project's common rule interface.

Conceptually:

```php
interface Rule
{
    public function apply($node);
}
```

The exact method signature should follow the current implementation in the repository.

The important design principle is that each rule should have one clear responsibility.

---

# Adding a New Migration Rule

Suppose you discover a PHPExcel API that is not currently supported.

Example:

```php
PHPExcel_SomeClass::someMethod($value);
```

First determine the PhpSpreadsheet equivalent.

Then:

1. Add the class mapping.
2. Add the method mapping if necessary.
3. Create a dedicated rule if the transformation is more complex.
4. Register the rule with the migrator.
5. Test the rule against a small PHP file.
6. Test the migrated file inside the real application.

---

# Example Rule Development

Create a rule:

```text
src/Rules/SomeRule.php
```

The rule should:

* inspect the relevant AST node
* verify that the node actually represents the PHPExcel API
* modify only the required portion
* return the transformed node

Avoid rules that perform broad string replacements.

Prefer:

```text
AST node detection
        ↓
Exact class/method detection
        ↓
AST transformation
```

over:

```text
Search string
        ↓
Replace string
```

---

# Updating the Migration Engine

The migration engine will evolve as more legacy PHPExcel code is encountered.

When you find a new unsupported API:

## Step 1 — Identify the old API

Example:

```php
PHPExcel_X
```

## Step 2 — Find the PhpSpreadsheet equivalent

Determine whether PhpSpreadsheet provides:

* a renamed class
* a renamed method
* a new namespace
* a different static API
* a replacement constant
* no direct equivalent

## Step 3 — Add the mapping

Update the appropriate mapping/rule.

## Step 4 — Test independently

Create a small input file containing only the problematic code.

Example:

```php
<?php

$test = PHPExcel_X::someMethod();
```

Run the migrator.

Verify the output.

## Step 5 — Test against a real application file

Once the isolated test works, run the migration against the real report or application file.

## Step 6 — Test the generated Excel file

Compilation success is not enough.

The actual Excel output must also be verified.

---

# Running the Migrator

A typical workflow is:

```bash
php migrate.php path/to/File.php
```

For example:

```bash
php migrate.php Reports/BreakdownReport.php
```

Then inspect:

```text
Reports/BreakdownReport.php
```

or the generated output location configured by the migrator.

---

# Recommended Git Workflow

Before running migration:

```bash
git status
```

Create a dedicated branch:

```bash
git checkout -b migrate-phpexcel-to-phpspreadsheet
```

Run the migration.

Then inspect:

```bash
git diff
```

This is extremely important.

Do not blindly commit the generated files.

Review:

```bash
git diff --check
```

and:

```bash
git diff
```

---

# Backups and Safety

Automated source-code transformation should always be performed with a recoverable version of the original source.

Recommended approach:

```text
Git branch
    +
Git commit
    +
Migration
    +
git diff
```

The migration engine may also provide backup/reporting functionality depending on the configured version.

The safest approach is to always have the original source available through Git.

---

# Migration Reports

A migration report should ideally provide information such as:

```text
File:
    Reports/BreakdownReport.php

Changes:
    PHPExcel class detected
    PHPExcel_IOFactory migrated
    PHPExcel_Worksheet_Drawing migrated
    Coordinate API migrated

Warnings:
    Manual review required for XYZ
```

Warnings are important.

An automated migrator should not pretend that every migration is guaranteed to be correct.

---

# Debugging

When a migrated file does not work, first determine whether the problem is:

1. A migration rule problem
2. A PhpSpreadsheet API difference
3. An application-specific problem
4. An existing PHPExcel behavior that was previously relied upon

---

## Check PHP syntax

Run:

```bash
php -l path/to/file.php
```

Example:

```bash
php -l Reports/BreakdownReport.php
```

Expected result:

```text
No syntax errors detected
```

---

## Check Composer

Run:

```bash
composer dump-autoload
```

Then:

```bash
composer show phpoffice/phpspreadsheet
```

---

# Common Migration Issues

## 1. Class exists but method does not

Example:

```text
Call to undefined method
PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::setCellValueByColumnAndRow()
```

This usually means the class migration succeeded but the method API also changed.

The solution is not to add another class mapping.

Instead, add or update a **method migration rule**.

---

# 2. Writer receives the wrong object

Example:

```text
PhpOffice\PhpSpreadsheet\Writer\Xlsx::__construct():
Argument #1 must be PhpOffice\PhpSpreadsheet\Spreadsheet
```

This usually indicates that some part of the application is still creating a PHPExcel object.

For example:

```php
$spreadsheet = new PHPExcel();
```

must be migrated to:

```php
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

and then passed to the PhpSpreadsheet writer.

---

# 3. Formula appears instead of calculated value

A migration can be syntactically correct while the generated Excel file behaves differently.

For example:

```text
=SUM(H3:H1247)
```

may appear where the application previously expected a calculated value.

This is not necessarily an AST migration problem.

Excel formulas and formula calculation are separate concerns.

PhpSpreadsheet can write formulas, but formula calculation behavior must be tested independently.

---

# 4. Formula pre-calculation

When using the Xlsx writer, the application may use:

```php
$writer->setPreCalculateFormulas(true);
```

This controls formula calculation behavior when writing the workbook.

However, the correct setting depends on how the application uses formulas.

Do not assume that enabling pre-calculation will reproduce every PHPExcel behavior.

---

# 5. Deprecated PHP behavior

Migration may expose PHP 8.x issues that were hidden under PHP 5.x.

Examples include:

```text
Dynamic property creation
Deprecated functions
Removed functions
Changed type behavior
```

These should be treated separately from the PHPExcel → PhpSpreadsheet migration.

---

# Important PhpSpreadsheet Differences

PHPExcel and PhpSpreadsheet are related projects, but they are not drop-in replacements in every situation.

Some APIs have:

* renamed classes
* renamed methods
* different namespaces
* removed methods
* changed constants
* changed behavior
* changed parameter expectations

Therefore:

```text
PHPExcel migration
        ≠
automatic application migration
```

The migration engine automates source transformations.

The application still needs testing.

---

# What This Tool Does Not Do

This project intentionally does not try to automatically solve every possible migration problem.

It does not automatically understand:

* business logic
* database behavior
* report requirements
* application-specific Excel logic
* expected Excel output
* external API behavior
* custom PHPExcel extensions
* custom helper classes
* every removed PHPExcel API
* every behavioral difference between PHPExcel and PhpSpreadsheet

For these cases, manual intervention may be required.

---

# Recommended Migration Strategy

For a large application, do not migrate everything at once.

Use an incremental approach.

## Phase 1 — Install PhpSpreadsheet

Add PhpSpreadsheet to the application.

```bash
composer require phpoffice/phpspreadsheet
```

---

## Phase 2 — Run the migrator on one file

Example:

```bash
php migrate.php Reports/BreakdownReport.php
```

---

## Phase 3 — Review the diff

```bash
git diff -- Reports/BreakdownReport.php
```

---

## Phase 4 — Fix migration gaps

If an unsupported API is found:

```text
Identify API
     ↓
Find PhpSpreadsheet equivalent
     ↓
Add migration rule
     ↓
Run migrator again
     ↓
Test
```

---

## Phase 5 — Test the report

Verify:

* Excel file opens
* Sheet names are correct
* Cell values are correct
* Formulas are correct
* Formatting is correct
* Column widths are correct
* Row heights are correct
* Images/drawings are correct
* Merged cells are correct
* Number formats are correct
* Calculated values are correct
* No PHP warnings/errors are generated

---

## Phase 6 — Move to the next report

Once one report is working correctly, migrate the next report.

This creates a controlled migration rather than one huge change.

---

# Testing After Migration

A successful migration should be tested at multiple levels.

## Level 1 — PHP syntax

```bash
php -l file.php
```

---

## Level 2 — Application execution

Run the report/application normally.

---

## Level 3 — Excel generation

Generate the actual Excel file.

---

## Level 4 — Excel content

Compare:

```text
Old PHPExcel output
        VS
New PhpSpreadsheet output
```

Check important:

* values
* formulas
* formatting
* number formats
* dimensions
* images
* merged cells
* worksheets

---

# Output Comparison

For critical reports, keep a known-good PHPExcel-generated file.

Then generate the same report using PhpSpreadsheet.

Compare:

```text
PHPExcel.xlsx
PhpSpreadsheet.xlsx
```

Do not compare only whether both files open.

The important question is:

> Does the migrated application produce the same business result?

---

# Adding Support for Legacy Code

Legacy applications often contain code that is not part of the standard PHPExcel API.

For example:

```php
$myExcelHelper->somePHPExcelOperation();
```

The migrator cannot know what `somePHPExcelOperation()` means unless it is explicitly programmed.

In such cases:

1. Identify the actual PHPExcel operation.
2. Determine the PhpSpreadsheet equivalent.
3. Add a migration rule if it is generic.
4. Keep application-specific transformations in application code.

---

# Design Philosophy

The project follows several principles.

## 1. AST over text replacement

Use:

```text
PHP source
   ↓
AST
   ↓
AST transformation
   ↓
PHP source
```

rather than:

```text
str_replace()
```

for structural transformations.

---

## 2. Rules should be small

Each rule should solve one migration problem.

For example:

```text
ClassRule
MethodRule
ConstantRule
CoordinateRule
```

This makes the project easier to maintain.

---

## 3. Centralize mappings

Mappings should be kept in a central location whenever possible.

This prevents the same PHPExcel class from being mapped differently by different rules.

---

## 4. Do not hide uncertainty

If the migrator cannot safely transform something, it is better to produce a warning than silently generate incorrect code.

---

## 5. Human verification remains important

The engine automates migration.

It does not replace testing.

---

# Example Migration

Consider this legacy PHPExcel code:

```php
<?php

require_once 'PHPExcel.php';

$objPHPExcel = new PHPExcel();

$worksheet = $objPHPExcel->getActiveSheet();

$worksheet->setCellValueByColumnAndRow(
    0,
    1,
    'Hello World'
);

$column = PHPExcel_Cell::stringFromColumnIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter(
    $objPHPExcel,
    'Excel2007'
);

$objWriter->save('output.xlsx');
```

The migration engine transforms the PHPExcel-specific structures into their PhpSpreadsheet equivalents.

The resulting code will follow the PhpSpreadsheet API, for example:

```php
<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$spreadsheet = new Spreadsheet();

$worksheet = $spreadsheet->getActiveSheet();

$column = Coordinate::stringFromColumnIndex(0);

$writer = IOFactory::createWriter(
    $spreadsheet,
    'Xlsx'
);

$writer->save('output.xlsx');
```

The exact output depends on the migration rules implemented in the version of the engine.

---

# Migration Checklist

Use this checklist when migrating a PHPExcel application.

* [ ] Create a Git branch
* [ ] Commit the original PHPExcel application
* [ ] Install PhpSpreadsheet
* [ ] Install the migration engine
* [ ] Run the migrator on one PHP file
* [ ] Review the generated diff
* [ ] Run `php -l`
* [ ] Run the application
* [ ] Generate the Excel file
* [ ] Verify worksheets
* [ ] Verify cell values
* [ ] Verify formulas
* [ ] Verify formatting
* [ ] Verify number formats
* [ ] Verify column widths
* [ ] Verify row heights
* [ ] Verify drawings/images
* [ ] Verify merged cells
* [ ] Compare with the original PHPExcel output
* [ ] Fix unsupported APIs
* [ ] Add migration rules where appropriate
* [ ] Repeat for the next file
* [ ] Run the complete application test suite
* [ ] Commit the migrated changes

---

# Updating the Mapping

When a new PHPExcel API is discovered, update the central mapping.

For example:

```php
'PHPExcel_SomeClass' => 'PhpOffice\\PhpSpreadsheet\\SomeClass',
```

Then add any required method or constant mappings.

After changing the mapping:

```bash
php migrate.php test/example.php
```

Review the generated result.

---

# Developing the Migrator

Install development dependencies:

```bash
composer install
```

Run the migrator against a test file:

```bash
php migrate.php tests/fixtures/example.php
```

Check the output.

When adding a new rule, always include a small reproducible example.

A good test should demonstrate:

```text
Input
  ↓
Migration
  ↓
Expected Output
```

---

# Debugging the AST

If a migration rule is not triggering, inspect the AST generated by `nikic/php-parser`.

For example, these expressions are structurally different:

```php
PHPExcel::something();
```

and:

```php
$obj->something();
```

The rule must inspect the correct AST node type.

This is one of the main reasons AST-based migration is useful.

---

# PHP Parser

This project uses:

**nikic/php-parser**

Repository:

https://github.com/nikic/PHP-Parser

The parser provides the AST used by the migration engine.

---

# PhpSpreadsheet

The target library is:

**PhpSpreadsheet**

Official repository:

https://github.com/PHPOffice/PhpSpreadsheet

Official documentation:

https://phpspreadsheet.readthedocs.io/

Install with:

```bash
composer require phpoffice/phpspreadsheet
```

---

# PHPExcel

PHPExcel is the legacy library being migrated away from.

PHPExcel is no longer the recommended library for new projects.

Existing PHPExcel applications should consider migrating to PhpSpreadsheet.

---

# Version Compatibility

The migration engine should be run using a modern PHP version supported by the project's dependencies.

For the application being migrated, verify the actual PhpSpreadsheet version and PHP version requirements before deployment.

Do not assume that migrating the PHPExcel API is the same thing as upgrading the entire PHP runtime.

For example:

```text
PHP 5.x
    ↓
PHP 8.x

PHPExcel
    ↓
PhpSpreadsheet

Slim 2
    ↓
Slim 4
```

These are separate migrations and should ideally be debugged independently.

---

# Large Application Migration

For a large application, it is recommended to migrate by logical module.

For example:

```text
Reports
    ├── BreakdownReport.php
    ├── SightReportDTC.php
    ├── PackingList.php
    └── InvoiceReport.php
```

Migrate one report:

```text
BreakdownReport.php
        ↓
Test
        ↓
Production-equivalent output
```

Then move to:

```text
SightReportDTC.php
```

This makes it much easier to identify which migration rule caused a problem.

---

# Known Migration Areas Requiring Manual Review

Some areas should always receive additional attention.

## Formula handling

Check whether formulas should remain formulas or whether the application expects calculated values.

---

## Writers

Check:

```php
PHPExcel_Writer_Excel2007
```

and related writer creation code.

PhpSpreadsheet uses its own writer classes.

---

## Readers

Check all:

```php
PHPExcel_IOFactory::load()
PHPExcel_IOFactory::createReader()
PHPExcel_IOFactory::identify()
```

calls.

---

## Drawings

Check:

```php
PHPExcel_Worksheet_Drawing
```

and related image/drawing functionality.

---

## Number formats

Check custom number formats and formatted values carefully.

---

## Zip handling

PHPExcel projects may contain legacy configuration such as:

```php
PHPExcel_Settings::setZipClass(
    PHPExcel_Settings::PCLZIP
);
```

PhpSpreadsheet does not necessarily provide a direct one-to-one equivalent.

Such code should be manually reviewed rather than automatically assumed to have identical behavior.

---

# Security Considerations

The migrator processes PHP source code.

Only run it against source code that you trust and understand.

Always review generated code before deploying it.

Do not run generated migration output directly in production without testing.

---

# Performance

AST parsing is more expensive than a simple `str_replace()` operation.

This is intentional.

The trade-off is:

```text
Text replacement
    Fast
    Unsafe for complex PHP syntax

AST migration
    More processing
    Much safer for structural PHP transformations
```

For large projects, migrate files in batches rather than loading an entire codebase into memory unnecessarily.

---

# Contributing

Contributions are welcome.

When submitting a new migration rule, please include:

1. The PHPExcel code that is not currently supported.
2. The expected PhpSpreadsheet equivalent.
3. The reason the existing rules cannot handle it.
4. The new rule/mapping.
5. A reproducible test case.
6. Any limitations or manual steps required.

Example:

```text
PHPExcel API:
PHPExcel_X::someMethod()

PhpSpreadsheet API:
PhpOffice\PhpSpreadsheet\X::someMethod()

Required change:
Add class mapping.

Test:
tests/fixtures/some-method.php
```

---

# Pull Requests

A good pull request should contain:

```text
Problem
    ↓
Example PHPExcel code
    ↓
Expected PhpSpreadsheet code
    ↓
Migration rule
    ↓
Test
```

Avoid adding broad text replacements unless there is no safer AST-based alternative.

---

# Roadmap

Possible future improvements include:

* [ ] More PHPExcel class mappings
* [ ] More method mappings
* [ ] More constant mappings
* [ ] More coordinate API transformations
* [ ] Better automatic import management
* [ ] Better migration warnings
* [ ] Migration summary reports
* [ ] Dry-run mode
* [ ] Directory migration
* [ ] Automatic backup support
* [ ] Automated fixture tests
* [ ] Before/after migration reports
* [ ] Improved handling of removed PHPExcel APIs
* [ ] PhpSpreadsheet version-specific rules
* [ ] CI test suite
* [ ] Migration statistics
* [ ] Unsupported API detection

---

# FAQ

## Is this a replacement for PhpSpreadsheet?

No.

This is a **migration tool**.

It helps convert PHPExcel source code into PhpSpreadsheet-compatible source code.

---

## Does it automatically migrate an entire application?

It can automate many repetitive transformations, but every migrated application still needs testing.

---

## Can I use it with PHP 8?

Yes. The migration engine is intended to run on modern PHP versions.

The application being migrated may still contain legacy PHP issues that must be handled separately.

---

## Can I migrate from PHPExcel directly to PhpSpreadsheet without changing PHP?

Technically, the PHPExcel → PhpSpreadsheet migration and PHP runtime upgrade are separate concerns.

It is usually easier to understand failures when each major migration is controlled and tested separately.

---

## Will every PHPExcel API have an automatic replacement?

No.

Some APIs have changed significantly or have been removed.

Those cases require manual migration or a custom migration rule.

---

## Is search-and-replace enough?

For simple class-name replacements, it may work.

For a large application, it is not recommended as the primary migration strategy.

AST-based migration provides much better control over PHP syntax.

---

# Philosophy

This project is not intended to claim:

> "Run this tool and your entire PHPExcel application is magically migrated."

Instead, the goal is:

> **Automate the repetitive work, identify migration gaps, and leave application-specific decisions to the developer.**

A good migration engine should save development time while making it obvious where manual review is required.

---

# Final Migration Flow

The complete workflow can be summarized as:

```text
                    LEGACY APPLICATION
                           |
                           v
                    PHPExcel source
                           |
                           v
                  +-------------------+
                  | PHP Parser / AST  |
                  +-------------------+
                           |
                           v
                  +-------------------+
                  | Migration Engine  |
                  +-------------------+
                           |
             +-------------+-------------+
             |             |             |
             v             v             v
        Class Rules   Method Rules   Constant Rules
             |             |             |
             +-------------+-------------+
                           |
                           v
                  Coordinate Rules
                           |
                           v
                    Import Handling
                           |
                           v
                  +-------------------+
                  | Migrated PHP Code |
                  +-------------------+
                           |
                           v
                  PhpSpreadsheet Code
                           |
                           v
                   Application Tests
                           |
                           v
                  Excel Output Tests
                           |
                           v
                    Production Ready
```

---

# License

Add your project's license here.

For example:

```text
MIT License
```

See the `LICENSE` file for details.

---

# Author

Created to simplify the migration of legacy PHPExcel-based PHP applications to PhpSpreadsheet.

If this project helps you migrate a legacy PHPExcel application, feel free to contribute improvements, migration rules, test cases, and bug fixes.

---

## Quick Start

For someone discovering this repository for the first time:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
cd YOUR_REPOSITORY
composer install
```

Install PhpSpreadsheet in the application being migrated:

```bash
composer require phpoffice/phpspreadsheet
```

Run the migration:

```bash
php migrate.php path/to/YourReport.php
```

Check the generated code:

```bash
php -l path/to/YourReport.php
```

Review:

```bash
git diff
```

Then run the application and verify the generated Excel output.

**Always test the migrated Excel files before deploying to production.**

---

# Summary

PHPExcel → PhpSpreadsheet migration is not simply a library rename.

A real migration can involve:

```text
PHPExcel classes
        +
Namespaces
        +
Methods
        +
Constants
        +
Coordinates
        +
Readers
        +
Writers
        +
Drawings
        +
Formula behavior
        +
Application-specific code
```

This migration engine handles the parts that can be reliably automated using PHP AST transformations and provides an extensible rule system for additional migration cases.

The result is a repeatable migration process that can be used across multiple PHPExcel-based projects.
