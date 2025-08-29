<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzesTable extends Migration
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
            'lesson_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'question' => [
                'type' => 'TEXT',
            ],
            'question_type' => [  // Added this missing field
                'type' => 'ENUM',
                'constraint' => ['multiple_choice', 'true_false', 'essay', 'fill_blank'],
                'default' => 'multiple_choice',
            ],
            'option_a' => [  // Added quiz options
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'points' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 1,
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
        $this->forge->addKey('lesson_id');
        $this->forge->addKey('question_type');  // This field now exists
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quizzes');
    }
    
    public function down()
    {
        $this->forge->dropTable('quizzes');
    }
}
