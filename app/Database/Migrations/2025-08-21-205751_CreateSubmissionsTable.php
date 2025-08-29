<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissionsTable extends Migration
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
            'answer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_correct' => [
                'type' => 'BOOLEAN',
                'null' => true,
            ],
            'score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'attempt_number' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 1,
            ],
            'submission_date' => [
                'type' => 'DATETIME',
                'null' => true, 
            ],
            'time_taken' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => true,
                'comment' => 'Time taken in seconds',
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
        $this->forge->addKey(['quiz_id', 'student_id']);
        $this->forge->addKey('student_id');
        $this->forge->addKey('submission_date');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('submissions');
    }

    public function down()
    {
        $this->forge->dropTable('submissions');
    }
}
