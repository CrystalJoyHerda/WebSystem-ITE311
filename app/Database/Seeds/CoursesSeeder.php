<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'description' => 'Basic concepts of computer science.',
                'instructor_id' => 2, 
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'MATH201',
                'course_name' => 'Calculus I',
                'description' => 'Differential and integral calculus.',
                'instructor_id' => 2,
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'ENG101',
                'course_name' => 'English Communication',
                'description' => 'Fundamentals of English communication.',
                'instructor_id' => 2, 
                'semester' => '2nd Semester',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'PHY111',
                'course_name' => 'General Physics',
                'description' => 'Introduction to the principles of physics.',
                'instructor_id' => 2,
                'semester' => '1st Semester',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_code' => 'HIST101',
                'course_name' => 'World History',
                'description' => 'A survey of world history from ancient times to the present.',
                'instructor_id' => 2,
                'semester' => '2nd Semester',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}