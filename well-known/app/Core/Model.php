<?php
namespace App\Core;

class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function findBy(string $column, mixed $value): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$column} = ?",
            [$value]
        );
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy}"
        );
    }

    public function where(string $where, array $params = [], string $orderBy = 'id DESC'): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy}",
            $params
        );
    }

    public function create(array $data): int
    {
        if (!isset($data['created_at']) && $this->hasColumn('created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at']) && $this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        if (!isset($data['updated_at']) && $this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        return $this->db->count($this->table, $where, $params);
    }

    public function paginate(int $page = 1, int $perPage = 20, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $total = $this->count($where, $params);
        $totalPages = max(1, ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
        ];
    }

    protected function hasColumn(string $column): bool
    {
        static $cache = [];
        $key = $this->table . '.' . $column;
        if (!isset($cache[$key])) {
            try {
                $this->db->query("SELECT {$column} FROM {$this->table} LIMIT 0");
                $cache[$key] = true;
            } catch (\Exception $e) {
                $cache[$key] = false;
            }
        }
        return $cache[$key];
    }
}
