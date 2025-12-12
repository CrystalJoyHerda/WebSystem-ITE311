<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyQuizAnswersForQuestions extends Migration
{
    public function up()
    {
        // Add question_id column to quiz_answers table
        $fields = [
            'question_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'quiz_id'
            ]
        ];
        
        $this->forge->addColumn('quiz_answers', $fields);
        
        // Add foreign key for question_id
        $this->db->query('ALTER TABLE quiz_answers ADD CONSTRAINT fk_quiz_answers_question FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE ON UPDATE CASCADE');
        
        // Add index
        $this->db->query('ALTER TABLE quiz_answers ADD INDEX idx_question_id (question_id)');
    }
    
    public function down()
    {
        // Drop foreign key and column
        $this->db->query('ALTER TABLE quiz_answers DROP FOREIGN KEY fk_quiz_answers_question');
        $this->forge->dropColumn('quiz_answers', 'question_id');
    }
}
