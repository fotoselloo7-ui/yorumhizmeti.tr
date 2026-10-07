<?php
namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $validated = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = trim($this->data[$field] ?? '');

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'rule' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $error = $this->$method($field, $value, $params);
                    if ($error) {
                        $this->errors[$field][] = $error;
                        break;
                    }
                }
            }

            $this->validated[$field] = $value;
        }
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->validated;
    }

    private function ruleRequired(string $field, string $value): ?string
    {
        return $value === '' ? "{$field} alanı zorunludur." : null;
    }

    private function ruleEmail(string $field, string $value): ?string
    {
        if ($value === '') return null;
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Geçerli bir e-posta adresi giriniz.";
    }

    private function ruleMin(string $field, string $value, array $params): ?string
    {
        if ($value === '') return null;
        $min = (int) $params[0];
        return mb_strlen($value) < $min ? "{$field} en az {$min} karakter olmalıdır." : null;
    }

    private function ruleMax(string $field, string $value, array $params): ?string
    {
        if ($value === '') return null;
        $max = (int) $params[0];
        return mb_strlen($value) > $max ? "{$field} en fazla {$max} karakter olmalıdır." : null;
    }

    private function ruleNumeric(string $field, string $value): ?string
    {
        if ($value === '') return null;
        return is_numeric($value) ? null : "{$field} sayısal bir değer olmalıdır.";
    }

    private function ruleUnique(string $field, string $value, array $params): ?string
    {
        if ($value === '') return null;
        $table = $params[0];
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
        $sqlParams = [$value];

        if ($exceptId) {
            $sql .= " AND id != ?";
            $sqlParams[] = $exceptId;
        }

        $result = $db->fetch($sql, $sqlParams);
        return ($result['cnt'] > 0) ? "Bu {$field} zaten kullanılıyor." : null;
    }

    private function ruleConfirmed(string $field, string $value): ?string
    {
        $confirmation = $this->data[$field . '_confirmation'] ?? '';
        return $value !== $confirmation ? "Onay alanı eşleşmiyor." : null;
    }

    private function ruleSlug(string $field, string $value): ?string
    {
        if ($value === '') return null;
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) ? null : "Geçerli bir slug giriniz (küçük harf, rakam ve tire).";
    }
}
