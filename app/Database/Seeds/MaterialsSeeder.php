<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('materials');

        // Sample entries. Adjust course_id values to match your local data.
        $samples = [
            [
                'course_id'  => 1,
                'file_name'  => 'Sample Lecture Notes.pdf',
                'file_path'  => 'writable/uploads/materials/1/sample-lecture-notes.pdf',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id'  => 2,
                'file_name'  => 'Example Assignment.docx',
                'file_path'  => 'writable/uploads/materials/2/example-assignment.docx',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($samples as $row) {
            // avoid duplicate by checking for same course_id + file_name
            $exists = (bool) $builder->where('course_id', $row['course_id'])
                ->where('file_name', $row['file_name'])
                ->countAllResults();

            if (! $exists) {
                $builder->insert($row);
            }
        }
    }
}
