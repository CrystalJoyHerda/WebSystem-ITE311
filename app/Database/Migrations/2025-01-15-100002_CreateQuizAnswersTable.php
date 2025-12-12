<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizAnswersTable extends Migration
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
            'submission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Index of the question in the quiz (0-based)'
            ],
            'student_answer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_correct' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if correct, 0 if incorrect'
            ],
            'points_earned' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('submission_id');
        $this->forge->addKey('quiz_id');
        $this->forge->addForeignKey('submission_id', 'quiz_submissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_answers');
    }
    
    public function down()
    {
        $this->forge->dropTable('quiz_answers');
    }
}
