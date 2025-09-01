<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReportJobsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 50],
            'parameters' => ['type' => 'TEXT'],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'processing', 'completed'], 'default' => 'pending'],
            'result_file' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('report_jobs');
    }

    public function down()
    {
        //
    }
}
