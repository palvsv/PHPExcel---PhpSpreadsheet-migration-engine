# Excel Migrator

A rule-based PHP code migration engine for converting legacy **PHPExcel** code to **PhpSpreadsheet**.

The engine uses **nikic/php-parser** to analyze PHP source code and apply targeted AST-aware migration rules instead of relying on simple text replacements.

It was designed for large legacy PHP applications where hundreds of existing reports and Excel-related classes need to be migrated from PHPExcel to PhpSpreadsheet safely and consistently.

---

## Why Excel Migrator?

PHPExcel is no longer maintained, while PhpSpreadsheet is its modern successor.

Migrating a small PHPExcel project manually is relatively straightforward.

Migrating a large legacy application is not.

Typical legacy applications may contain:

- Hundreds of PHP files
- Thousands of PHPExcel method calls
- PHPExcel static classes
- PHPExcel constants
- PHPExcel readers and writers
- PHPExcel worksheet objects
- Zero-based column indexes
- `getCellByColumnAndRow()`
- `setCellValueByColumnAndRow()`
- PHPExcel-specific styling arrays
- Legacy class names
- Nested method calls
- Dynamic Excel formulas
- Custom report classes

Simple search-and-replace is dangerous because PHPExcel and PhpSpreadsheet do not always have a 1-to-1 API mapping.

Excel Migrator provides a structured rule engine for handling these differences.

---

# Features

## AST-based migration

Uses [`nikic/php-parser`](https://github.com/nikic/PHP-Parser) to parse PHP source code and operate on PHP syntax trees.

This allows the migration engine to understand:

- Classes
- Method calls
- Static calls
- Constants
- Object creation
- Arguments
- Nested expressions
- Imports

instead of blindly replacing strings.

---

## Rule-based architecture

Migration functionality is divided into independent rules.

Examples:

```text
IOFactoryRule
StaticCallRule
ConstantFetchRule
MethodCallRule
NewExpressionRule
SpreadsheetRule