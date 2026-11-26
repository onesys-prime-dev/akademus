<?php

declare(strict_types=1);

use CodeIgniter\CodingStandard\CodeIgniter4;
use Nexus\CsConfig\Factory;
use PhpCsFixer\Finder;

/*
 * Code style: CodeIgniter's own coding standard (codeigniter/coding-standard).
 *
 *   composer cs       check only (what CI runs)
 *   composer cs:fix   apply fixes
 *
 * app/Views is excluded: views mix HTML and PHP, and automatic fixes there risk changing the markup.
 */
$finder = Finder::create()
    ->files()
    ->in([__DIR__ . '/app', __DIR__ . '/tests'])
    ->exclude(['Views'])
    ->append([__DIR__ . '/spark', __DIR__ . '/public/index.php', __FILE__]);

return Factory::create(new CodeIgniter4(), [], [
    'finder'    => $finder,
    'cacheFile' => 'build/.php-cs-fixer.cache',
])->forProjects();
