<?php

declare(strict_types=1);

use CodeIgniter\CodingStandard\CodeIgniter4;
use Nexus\CsConfig\Factory;
use PhpCsFixer\Finder;
use PhpCsFixer\FixerFactory;

/*
 * Code style: CodeIgniter's own coding standard (codeigniter/coding-standard), LAYOUT ONLY.
 *
 *   composer cs       check only (what CI runs)
 *   composer cs:fix   apply fixes
 *
 * Every "risky" rule of the ruleset is switched off: risky fixers may change behaviour
 * (e.g. strict_comparison turns == into ===, strict_param adds strict=true to in_array()).
 * Applying those to existing code broke the app once (string vs int IDs from MySQLi), so the
 * formatter must never change semantics. Adopt such rules deliberately, one at a time, with tests.
 *
 * app/Views is excluded: views mix HTML and PHP, and automatic fixes there risk changing the markup.
 */
$ruleset = new CodeIgniter4();

$riskyOff = [];

foreach ((new FixerFactory())->registerBuiltInFixers()->getFixers() as $fixer) {
    if ($fixer->isRisky() && array_key_exists($fixer->getName(), $ruleset->getRules())) {
        $riskyOff[$fixer->getName()] = false;
    }
}

$finder = Finder::create()
    ->files()
    ->in([__DIR__ . '/app', __DIR__ . '/tests'])
    ->exclude(['Views'])
    ->append([__DIR__ . '/spark', __DIR__ . '/public/index.php', __FILE__]);

return Factory::create($ruleset, $riskyOff, [
    'finder'    => $finder,
    'cacheFile' => 'build/.php-cs-fixer.cache',
])->forProjects();
