<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_code',
        'course_name', 
        'subject_code', 
        'subject_name', 
        'description',
        'units',
        'instructor_id', 
        'semester', 
        'created_at', 
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all courses assigned to a specific instructor
     */
    public function getCoursesByInstructor($instructorId)
    {
        return $this->where('instructor_id', $instructorId)->findAll();
    }

    /**
     * Get course details with enrollment count
     */
    public function getCourseWithStats($courseId)
    {
        $db = \Config\Database::connect();
        
        return $db->table($this->table)
            ->select('courses.*, COUNT(DISTINCT enrollments.id) as enrolled_count')
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->where('courses.id', $courseId)
            ->groupBy('courses.id')
            ->get()
            ->getRowArray();
    }

    /**
     * Get all courses with enrollment statistics for a teacher
     */
    public function getTeacherCoursesWithStats($instructorId)
    {
        $db = \Config\Database::connect();
        
        // Check which column names exist in the table
        $fields = $db->getFieldNames('courses');
        $hasSubjectCode = in_array('subject_code', $fields);
        $hasCourseCode = in_array('course_code', $fields);
        
        // Build select statement based on available columns
        if ($hasSubjectCode) {
            $select = 'courses.id, courses.subject_code, courses.subject_name, courses.description, courses.semester, courses.instructor_id, courses.created_at, courses.updated_at, COUNT(DISTINCT enrollments.id) as enrolled_count';
        } else {
            $select = 'courses.id, courses.course_code as subject_code, courses.course_name as subject_name, courses.description, courses.semester, courses.instructor_id, courses.created_at, courses.updated_at, COUNT(DISTINCT enrollments.id) as enrolled_count';
        }
        
        return $db->table($this->table)
            ->select($select)
            ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
            ->where('courses.instructor_id', $instructorId)
            ->groupBy('courses.id')
            ->orderBy($hasSubjectCode ? 'courses.subject_name' : 'courses.course_name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
