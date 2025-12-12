<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizQuestionsTable extends Migration
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
            'question' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'question_type' => [
                'type' => 'ENUM',
                'constraint' => ['multiple_choice', 'true_false', 'sentence'],
                'default' => 'multiple_choice',
            ],
            'option_a' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'For multiple choice questions'
            ],
            'option_b' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'option_c' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'option_d' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'correct_answer' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'For MCQ: A/B/C/D, For sentence: expected answer'
            ],
            'points' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 1,
            ],
            'question_order' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
                'comment' => 'Order of question in quiz'
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
        $this->forge->addKey('question_type');
        $this->forge->addKey('question_order');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_questions');
    }
    
    public function down()
    {
        $this->forge->dropTable('quiz_questions');
    }
}
