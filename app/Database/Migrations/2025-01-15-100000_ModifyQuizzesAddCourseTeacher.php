<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyQuizzesAddCourseTeacher extends Migration
{
    public function up()
    {
        // Add course_id and teacher_id columns to quizzes table
        $fields = [
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id'
            ],
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'course_id'
            ]
        ];
        
        $this->forge->addColumn('quizzes', $fields);
        
        // Add foreign keys
        $this->db->query('ALTER TABLE quizzes ADD CONSTRAINT fk_quizzes_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE quizzes ADD CONSTRAINT fk_quizzes_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
        
        // Add index for performance
        $this->forge->addKey('course_id');
        $this->forge->addKey('teacher_id');
    }
    
    public function down()
    {
        // Drop foreign keys first
        $this->db->query('ALTER TABLE quizzes DROP FOREIGN KEY fk_quizzes_course');
        $this->db->query('ALTER TABLE quizzes DROP FOREIGN KEY fk_quizzes_teacher');
        
        // Drop columns
        $this->forge->dropColumn('quizzes', ['course_id', 'teacher_id']);
    }
}
