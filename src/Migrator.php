<?php

declare(strict_types=1);

namespace ExcelMigrator;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use ExcelMigrator\Writers\ImportWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Migrator
{
    /**
     * All imports required for the current file.
     */
    private array $imports = [];
    /**
     * @var Rule[]
     */
    private array $rules = [];

    private Parser $parser;

    private bool $dryRun;

    public function __construct(bool $dryRun = false) {
        $this->dryRun = $dryRun;

       $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
    }

    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    public function migrate(string $target)
    {
        if (is_file($target)) {
            $this->migrateFile($target);
            return;
        }

        if (is_dir($target)) {
            $this->migrateDirectory($target);
            return;
        }

        throw new \RuntimeException(
            "Target not found: {$target}"
        );
    }

    private function migrateDirectory(string $directory)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {

            if (!$file->isFile()) {
                continue;
            }

            if (strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $this->migrateFile(
                $file->getPathname()
            );
        }
    }

    private function migrateFile(string $file)
    {
        echo PHP_EOL;
        echo "Migrating : {$file}" . PHP_EOL;
        $this->imports = [];
        $code = file_get_contents($file);

        if ($code === false) {
            throw new \RuntimeException(
                "Unable to read {$file}"
            );
        }

        $ast = $this->parse($code);

        $patches = $this->collectPatches($ast);
        if ($this->dryRun) {
            $this->printPatches($code, $patches);
        }
        if (count($patches) === 0) {
            echo "No changes." . PHP_EOL;
            return;
        }

        foreach ($patches as $i => $a) {
            foreach (array_slice($patches, $i + 1) as $b) {

                if ($a->start <= $b->end && $b->start <= $a->end) {

                    $aText = substr(
                        $code,
                        $a->start,
                        $a->length()
                    );

                    $bText = substr(
                        $code,
                        $b->start,
                        $b->length()
                    );

                    throw new \RuntimeException(
                        "Overlapping patches:\n\n" .

                        "RULE A: " .
                        basename(str_replace('\\', '/', $a->rule)) .
                        PHP_EOL .

                        "Range A: {$a->start}-{$a->end}" .
                        PHP_EOL .

                        "Text A: [{$aText}]" .
                        PHP_EOL . PHP_EOL .

                        "RULE B: " .
                        basename(str_replace('\\', '/', $b->rule)) .
                        PHP_EOL .

                        "Range B: {$b->start}-{$b->end}" .
                        PHP_EOL .

                        "Text B: [{$bText}]" .
                        PHP_EOL
                    );
                }
            }
        }
        $newCode = $this->applyPatches(
            $code,
            $patches
        );
        $newCode = ImportWriter::insert(
            $newCode,
            $this->imports
        );

        $this->validate($newCode);

       if ($this->dryRun) {
            echo PHP_EOL;
            echo "========== DRY RUN ==========" . PHP_EOL;
            echo "No files modified." . PHP_EOL;
        } else {
            copy($file, $file.'.bak');
            file_put_contents($file, $newCode);

        }

        echo PHP_EOL;

        foreach ($this->rules as $rule) {

            printf(
                "%-35s %5d\n",
                (new \ReflectionClass($rule))->getShortName(),
                count($rule->getPatches())
            );

        }

        echo str_repeat('-', 45) . PHP_EOL;

        printf(
            "%-35s %5d\n",
            "Total Replacements",
            count($patches)
        );

        printf(
            "%-35s %5d\n",
            "Imports Added",
            count($this->imports)
        );

        echo PHP_EOL;
    }

    private function parse(string $code)
    {
        try {
            return $this->parser->parse($code);
        } catch (Error $e) {
            file_put_contents(__DIR__ . '/../Logs/failed.php', $code);
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @param array $ast
     * @return Patch[]
     */
    private function collectPatches(array $ast)
    {
        $patches = [];

        foreach ($this->rules as $rule) {

            $rule->reset();

            $traverser = new NodeTraverser();
            $traverser->addVisitor($rule);
            $traverser->traverse($ast);

            $patches = array_merge(
                $patches,
                $rule->getPatches()
            );

            foreach ($rule->getImports() as $import) {
                $this->imports[$import] = true;
            }
        }

        usort(
            $patches,
            fn (Patch $a, Patch $b)
                => $b->start <=> $a->start
        );

        return $patches;
    }

    /**
     * @param Patch[] $patches
     */
    private function applyPatches(
        string $code,
        array $patches
    ): string {

        foreach ($patches as $patch) {
             echo $patch->rule .
            " | Line {$patch->line}" .
            " | {$patch->start}-{$patch->end}" .
            PHP_EOL;
            

            $old = substr($code, $patch->start, $patch->length());

            echo "Old  : [" . $old . "]" . PHP_EOL;
            echo "New  : [" . $patch->replacement . "]" . PHP_EOL;

            $patch->original = $old;

            $code = substr_replace(
                $code,
                $patch->replacement,
                $patch->start,
                $patch->length()
            );
        }

        return $code;
    }

    private function validate(string $code)
    {
       try {

            $this->parse($code);

        } catch (\Throwable $e) {

            file_put_contents(
                __DIR__ . '/../Logs/failed.php',
                $code
            );

            echo PHP_EOL;
            echo "Validation FAILED" . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;

            throw $e;
        }
    }

    private function printPatches(string $code, array $patches): void
    {
        foreach ($patches as $patch) {

            $old = substr(
                $code,
                $patch->start,
                $patch->length()
            );

            echo PHP_EOL;

            echo '[' . basename($patch->rule) . ']' . PHP_EOL;

            echo "OLD : " . $old . PHP_EOL;

            echo "NEW : " . $patch->replacement . PHP_EOL;
        }
    }
}