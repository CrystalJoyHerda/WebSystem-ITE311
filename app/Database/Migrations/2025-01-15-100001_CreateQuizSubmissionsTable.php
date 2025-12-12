<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizSubmissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'default' => null,
            ],
            'total_points' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('quiz_id');
        $this->forge->addKey('student_id');
        $this->forge->addKey('submitted_at');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_submissions');
    }
    
    public function down()
    {
        $this->forge->dropTable('quiz_submissions');
    }
}
