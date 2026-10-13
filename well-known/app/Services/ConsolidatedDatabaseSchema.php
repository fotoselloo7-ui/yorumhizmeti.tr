<?php
namespace App\Services;

use PDO;
use RuntimeException;

/**
 * Reads table definitions from the single canonical database/database.sql file.
 * This keeps staging/import installers compatible without re-introducing
 * separate migration SQL files.
 */
final class ConsolidatedDatabaseSchema
{
    private static ?string $source = null;

    private static function source(): string
    {
        if (self::$source !== null) return self::$source;
        $file = BASE_PATH . '/database/database.sql';
        $sql = @file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('Canonical database.sql could not be read.');
        }
        return self::$source = $sql;
    }

    public static function tableExists(PDO $pdo, string $table): bool
    {
        self::assertTableName($table);
        $stmt = $pdo->prepare(
            'SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=? LIMIT 1'
        );
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    }

    public static function tableCreateSql(string $table): string
    {
        self::assertTableName($table);
        $quoted = preg_quote($table, '~');
        if (!preg_match(
            '~CREATE\\s+TABLE\\s+\`' . $quoted . '\`\\s*\\([\\s\\S]*?\\)\\s*ENGINE=.*?;~i',
            self::source(),
            $match
        )) {
            throw new RuntimeException('Table definition missing from canonical database.sql: ' . $table);
        }

        $sql = preg_replace(
            '~^CREATE\\s+TABLE\\s+\`~i',
            'CREATE TABLE IF NOT EXISTS \`',
            trim($match[0]),
            1
        );
        if (!is_string($sql) || $sql === '') {
            throw new RuntimeException('Could not prepare canonical table definition: ' . $table);
        }
        return str_replace('utf8mb4_0900_ai_ci', 'utf8mb4_unicode_ci', $sql);
    }

    /** @param list<string> $tables */
    public static function ensureTables(PDO $pdo, array $tables): void
    {
        foreach ($tables as $table) {
            self::assertTableName($table);
            if (self::tableExists($pdo, $table)) continue;
            $pdo->exec(self::tableCreateSql($table));
            if (!self::tableExists($pdo, $table)) {
                throw new RuntimeException('Table could not be installed from database.sql: ' . $table);
            }
        }
    }

    private static function assertTableName(string $table): void
    {
        if (!preg_match('/^[a-z0-9_]+$/D', $table)) {
            throw new RuntimeException('Unsafe database table name.');
        }
    }
}
