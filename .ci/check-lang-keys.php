<?php

/**
 * Translation completeness: every lang('File.key') used in app/ must exist in app/Language/<locale>/File.php
 * or in CodeIgniter's own system language files, for every locale the app ships.
 *
 *   php .ci/check-lang-keys.php
 */
$root    = dirname(__DIR__);
$appLang = $root . '/app/Language';
$sysLang = $root . '/vendor/codeigniter4/framework/system/Language/en';

$locales = array_values(array_filter(scandir($appLang) ?: [], static fn ($d) => $d[0] !== '.' && is_dir("{$appLang}/{$d}")));
if ($locales === []) {
    $locales = ['en'];
}

$used = [];
$it   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/app', FilesystemIterator::SKIP_DOTS));

foreach ($it as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    preg_match_all("/\\blang\\(\\s*['\"]([A-Za-z0-9_]+)\\.([A-Za-z0-9_.]+)['\"]/", (string) file_get_contents($file->getPathname()), $m, PREG_SET_ORDER);

    foreach ($m as [, $langFile, $key]) {
        $used["{$langFile}.{$key}"] = substr($file->getPathname(), strlen($root) + 1);
    }
}

$lookup = static function (string $path, string $key): bool {
    if (! is_file($path)) {
        return false;
    }
    $data = require $path;

    foreach (explode('.', $key) as $part) {
        if (! is_array($data) || ! array_key_exists($part, $data)) {
            return false;
        }
        $data = $data[$part];
    }

    return true;
};

$missing = 0;

foreach ($locales as $locale) {
    foreach ($used as $full => $where) {
        [$langFile, $key] = explode('.', $full, 2);
        if ($lookup("{$appLang}/{$locale}/{$langFile}.php", $key) || $lookup("{$sysLang}/{$langFile}.php", $key)) {
            continue;
        }
        echo "::error file={$where},title=Missing translation::lang('{$full}') has no entry for locale '{$locale}'\n";
        $missing++;
    }
}

printf("Translations: %d key(s) used, %d locale(s) [%s], %d missing.\n", count($used), count($locales), implode(', ', $locales), $missing);

exit($missing > 0 ? 1 : 0);
