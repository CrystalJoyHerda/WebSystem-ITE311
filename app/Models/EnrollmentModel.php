<?php


namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table      = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
    public $timestamps = false;

    // Enroll a user in a course
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    // Get all courses a user is enrolled in
    public function getUserEnrollments($user_id)
    {
        return $this->where('user_id', $user_id)->findAll();
    }

    // Check if a user is already enrolled in a course
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where([
            'user_id' => $user_id,
            'course_id' => $course_id
        ])->countAllResults() > 0;
    }
}