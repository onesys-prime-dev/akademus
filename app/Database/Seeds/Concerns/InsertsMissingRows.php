<?php

namespace App\Database\Seeds\Concerns;

/**
 * Idempotent insert helpers for seeders: a row is inserted only when no row matches its natural key,
 * and an existing row is never overwritten (so admin edits on a running system survive a re-seed).
 */
trait InsertsMissingRows
{
    /**
     * Inserts $data (merged with $where) when no row matches $where. Returns the id of the matching
     * or inserted row ($idColumn), or 0 when the table has no such column.
     *
     * @param array<string, mixed> $where natural key
     * @param array<string, mixed> $data  remaining columns
     */
    protected function insertIfMissing(string $table, array $where, array $data = [], string $idColumn = 'id'): int
    {
        $row = $this->db->table($table)->where($where)->get()->getRowArray();
        if ($row !== null) {
            return (int) ($row[$idColumn] ?? 0);
        }

        $this->db->table($table)->insert($where + $data);

        return (int) $this->db->insertID();
    }
}
