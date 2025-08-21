<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminData = [
            [
                'email' => 'admin@lms.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Foox',
                'last_name' => 'Ben',
                'role' => 'admin',
                'student_id' => null,
                'emp_id' => 'EMP-001',
                'phone' => '09111111111',
                'department' => 'IT Department',
                'program' => null,
                'year_level' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $instructorData = [
            [
                'email' => 'instructor1@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Jim',
                'last_name' => 'Jamero',
                'role' => 'instructor',
                'student_id' => null,
                'emp_id' => 'EMP-002',
                'phone' => '09123456789',
                'department' => 'CET',
                'program' => null,
                'year_level' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $studentData = [
            [
                'email' => 'herda@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Crystal Joy',
                'last_name' => 'Herda',
                'role' => 'student',
                'student_id' => '2311600113',
                'emp_id' => null,
                'phone' => '09456789012',
                'department' => 'CET',
                'program' => 'Bachelor of Science in Information Technology',
                'year_level' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'email' => 'eslera@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Aman',
                'last_name' => 'Eslera',
                'role' => 'student',
                'student_id' => '2311600016',
                'emp_id' => null,
                'phone' => '09567890123',
                'department' => 'CET',
                'program' => 'Bachelor of Science in Information Technology',
                'year_level' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($adminData);
        $this->db->table('users')->insertBatch($instructorData);
        $this->db->table('users')->insertBatch($studentData);
    }
}
