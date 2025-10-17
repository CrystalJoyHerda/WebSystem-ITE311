<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;
use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now()->toDateTimeString();

        $data = [
            [
                'title'      => 'Welcome to the Portal',
                'content'    => 'Welcome students — please check your courses and announcements regularly.',
                'created_at' => $now,
            ],
            [
                'title'      => 'Schedule Update',
                'content'    => 'The midterm schedule has been posted. Visit the schedules page for details.',
                'created_at' => $now,
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
