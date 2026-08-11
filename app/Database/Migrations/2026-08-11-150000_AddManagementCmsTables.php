<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManagementCmsTables extends Migration
{
    public function up()
    {
        // marketplace_settings
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 120],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('marketplace_settings', true);

        // banners
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 150],
            'subtitle'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'image'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'mobile_image'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cta_label'     => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'cta_url'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order'    => ['type' => 'INT', 'default' => 0],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'starts_at'     => ['type' => 'DATETIME', 'null' => true],
            'ends_at'       => ['type' => 'DATETIME', 'null' => true],
            'created_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('banners', true);

        // featured_products
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id'  => ['type' => 'INT', 'unsigned' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'starts_at'   => ['type' => 'DATETIME', 'null' => true],
            'ends_at'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('product_id');
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('featured_products', true);

        // featured_stores
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'store_id'    => ['type' => 'INT', 'unsigned' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'starts_at'   => ['type' => 'DATETIME', 'null' => true],
            'ends_at'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('store_id');
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('featured_stores', true);

        // payment_methods
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'provider'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'method_code' => ['type' => 'VARCHAR', 'constraint' => 50],
            'method_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'config_json' => ['type' => 'JSON', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['provider', 'method_code']);
        $this->forge->createTable('payment_methods', true);
    }

    public function down()
    {
        $tables = [
            'payment_methods',
            'featured_stores',
            'featured_products',
            'banners',
            'marketplace_settings',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
