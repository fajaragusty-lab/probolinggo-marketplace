<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressLocationAccuracy extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('location_accuracy', 'addresses')) {
            $this->forge->addColumn('addresses', [
                'location_accuracy' => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'after' => 'longitude'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('location_accuracy', 'addresses')) {
            $this->forge->dropColumn('addresses', 'location_accuracy');
        }
    }
}
