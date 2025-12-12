<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollments extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['enrolled', 'pending', 'assigned', 'rejected'],
                'default' => 'enrolled',
                'after' => 'course_id',
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'status',
            ],
        ];
        
        $this->forge->addColumn('enrollments', $fields);
        
        // Add foreign key for approved_by
        $this->db->query('ALTER TABLE enrollments ADD CONSTRAINT fk_enrollments_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE enrollments DROP FOREIGN KEY fk_enrollments_approved_by');
        
        $this->forge->dropColumn('enrollments', ['status', 'approved_by']);
    }
}
