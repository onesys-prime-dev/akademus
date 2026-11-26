<?php

/**
 * Schema and data integrity checks for a freshly migrated + seeded database (CI `database` job).
 *
 *   php .ci/schema-integrity.php
 *
 * Connection: DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME environment variables.
 * Exit code 1 when any check fails; every finding is printed as a GitHub Actions error annotation.
 *
 * Checks:
 *  1. every table uses InnoDB and the database's utf8mb4 collation
 *  2. every table has a primary key
 *  3. every foreign key's columns are the leading columns of some index
 *  4. text columns use the table collation (utf8mb4_bin is allowed: JSON columns on MariaDB)
 *  5. no orphan rows: every non-NULL foreign key value exists in the referenced table
 *  6. every view can be queried
 */
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3306);
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$name = getenv('DB_NAME') ?: '';

if ($name === '') {
    fwrite(STDERR, "DB_NAME is not set.\n");

    exit(2);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli($host, $user, $pass, $name, $port);
$db->set_charset('utf8mb4');

$failures = [];
$fail     = static function (string $check, string $message) use (&$failures): void {
    $failures[] = "[{$check}] {$message}";
};
$rows = static fn (string $sql, string ...$params): array => (static function () use ($db, $sql, $params) {
    $st = $db->prepare($sql);
    if ($params !== []) {
        $st->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $st->execute();

    return $st->get_result()->fetch_all(MYSQLI_ASSOC);
})();

$dbCollation = $rows('SELECT DEFAULT_COLLATION_NAME c FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?', $name)[0]['c'];
$tables      = $rows("SELECT TABLE_NAME t, ENGINE e, TABLE_COLLATION c FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'", $name);

// 1 + 2
foreach ($tables as ['t' => $table, 'e' => $engine, 'c' => $collation]) {
    if ($engine !== 'InnoDB') {
        $fail('engine', "{$table} uses {$engine}, expected InnoDB");
    }
    if (! str_starts_with((string) $collation, 'utf8mb4_')) {
        $fail('collation', "{$table} uses {$collation}, expected utf8mb4");
    }
    $pk = $rows("SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = 'PRIMARY KEY'", $name, $table);
    if ($pk === []) {
        $fail('primary-key', "{$table} has no primary key");
    }
}

// 3 + 5
$fks = [];
foreach ($rows('SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, ORDINAL_POSITION
                FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL
                ORDER BY TABLE_NAME, CONSTRAINT_NAME, ORDINAL_POSITION', $name) as $r) {
    $key = $r['TABLE_NAME'] . '.' . $r['CONSTRAINT_NAME'];
    $fks[$key]['table']      = $r['TABLE_NAME'];
    $fks[$key]['refTable']   = $r['REFERENCED_TABLE_NAME'];
    $fks[$key]['cols'][]     = $r['COLUMN_NAME'];
    $fks[$key]['refCols'][]  = $r['REFERENCED_COLUMN_NAME'];
}

foreach ($fks as $key => $fk) {
    $indexes = [];
    foreach ($rows('SELECT INDEX_NAME, COLUMN_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY INDEX_NAME, SEQ_IN_INDEX', $name, $fk['table']) as $s) {
        $indexes[$s['INDEX_NAME']][] = $s['COLUMN_NAME'];
    }
    $covered = false;
    foreach ($indexes as $cols) {
        if (array_slice($cols, 0, count($fk['cols'])) === $fk['cols']) {
            $covered = true;
            break;
        }
    }
    if (! $covered) {
        $fail('fk-index', "{$key} (" . implode(',', $fk['cols']) . ') is not covered by an index');
    }

    $join  = implode(' AND ', array_map(static fn ($c, $rc) => "p.`{$rc}` = c.`{$c}`", $fk['cols'], $fk['refCols']));
    $where = implode(' AND ', array_map(static fn ($c) => "c.`{$c}` IS NOT NULL", $fk['cols']));
    $orphans = (int) $db->query("SELECT COUNT(*) n FROM `{$fk['table']}` c LEFT JOIN `{$fk['refTable']}` p ON {$join} WHERE {$where} AND p.`{$fk['refCols'][0]}` IS NULL")->fetch_assoc()['n'];
    if ($orphans > 0) {
        $fail('orphans', "{$key}: {$orphans} row(s) reference missing {$fk['refTable']} rows");
    }
}

// 4
foreach ($rows('SELECT c.TABLE_NAME, c.COLUMN_NAME, c.COLLATION_NAME, t.TABLE_COLLATION FROM information_schema.COLUMNS c
                JOIN information_schema.TABLES t ON t.TABLE_SCHEMA = c.TABLE_SCHEMA AND t.TABLE_NAME = c.TABLE_NAME
                WHERE c.TABLE_SCHEMA = ? AND t.TABLE_TYPE = \'BASE TABLE\' AND c.COLLATION_NAME IS NOT NULL', $name) as $c) {
    if ($c['COLLATION_NAME'] !== $c['TABLE_COLLATION'] && $c['COLLATION_NAME'] !== 'utf8mb4_bin') {
        $fail('column-collation', "{$c['TABLE_NAME']}.{$c['COLUMN_NAME']} uses {$c['COLLATION_NAME']} (table: {$c['TABLE_COLLATION']})");
    }
}

// 6
foreach ($rows('SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?', $name) as ['TABLE_NAME' => $view]) {
    try {
        $db->query("SELECT * FROM `{$view}` LIMIT 1");
    } catch (mysqli_sql_exception $e) {
        $fail('view', "{$view} cannot be queried: " . $e->getMessage());
    }
}

printf("Schema integrity: %d tables, %d foreign keys, database collation %s\n", count($tables), count($fks), $dbCollation);

foreach ($failures as $f) {
    echo "::error title=Schema integrity::{$f}\n";
}

if ($failures !== []) {
    echo count($failures) . " problem(s) found.\n";

    exit(1);
}

echo "All schema integrity checks passed.\n";
