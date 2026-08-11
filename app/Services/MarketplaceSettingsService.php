<?php

namespace App\Services;

class MarketplaceSettingsService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $row = $this->db->table('marketplace_settings')->where('setting_key', $key)->get()->getRowArray();
        return $row['setting_value'] ?? $default;
    }

    public function set(string $key, ?string $value): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->query(
            'INSERT INTO marketplace_settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)',
            [$key, $value, $now, $now]
        );
    }

    public function all(array $defaults = []): array
    {
        $rows = $this->db->table('marketplace_settings')->get()->getResultArray();
        $settings = $defaults;
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
