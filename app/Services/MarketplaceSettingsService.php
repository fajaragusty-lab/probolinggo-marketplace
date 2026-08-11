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
        $table = $this->db->table('marketplace_settings');
        $now   = date('Y-m-d H:i:s');
        $exists = $table->where('setting_key', $key)->get()->getRowArray();

        if ($exists) {
            $table->where('setting_key', $key)->update([
                'setting_value' => $value,
                'updated_at'    => $now,
            ]);
            return;
        }

        $table->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
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
