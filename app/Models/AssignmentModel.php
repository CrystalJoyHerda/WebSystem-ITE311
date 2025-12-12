<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table      = 'assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'due_date',
        'total_score',
        'file_name',
        'file_path',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Create a new assignment
     */
    public function createAssignment($data)
    {
        $result = $this->insert($data);
        
        if (!$result) {
            log_message('error', 'Assignment insert failed: ' . json_encode($this->errors()));
            log_message('error', 'Data attempted: ' . json_encode($data));
        }
        
        return $result;
    }

    /**
     * Get all assignments for a specific course
     */
    public function getAssignmentsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get a single assignment by ID
     */
    public function getAssignment($id)
    {
        return $this->find($id);
    }

    /**
     * Delete assignment (cascades to submissions via FK)
     */
    public function deleteAssignment($id)
    {
        return $this->delete($id);
    }

    /**
     * Get assignments with submission count for teacher view
     */
    public function getAssignmentsWithSubmissionCount($courseId)
    {
        $db = \Config\Database::connect();
        return $db->table($this->table)
            ->select('assignments.*, COUNT(student_submissions.id) as submission_count')
            ->join('student_submissions', 'student_submissions.assignment_id = assignments.id', 'left')
            ->where('assignments.course_id', $courseId)
            ->groupBy('assignments.id')
            ->orderBy('assignments.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
