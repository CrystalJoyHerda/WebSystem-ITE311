<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitsToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'units' => [
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => true,
                'null' => true,
                'after' => 'description'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'units');
    }
}
