<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmissionModel extends Model
{
    protected $table      = 'student_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'assignment_id',
        'student_id',
        'text_submission',
        'file_name',
        'file_path',
        'score',
        'status',
        'submitted_at',
        'graded_at'
    ];

    /**
     * Create a new submission
     */
    public function createSubmission($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all submissions for a specific assignment
     */
    public function getSubmissionsByAssignment($assignmentId)
    {
        $db = \Config\Database::connect();
        return $db->table($this->table)
            ->select('student_submissions.*, users.name as student_name, users.email as student_email')
            ->join('users', 'users.id = student_submissions.student_id')
            ->where('student_submissions.assignment_id', $assignmentId)
            ->orderBy('student_submissions.submitted_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get a student's submission for a specific assignment
     * Returns complete submission details including status, score, timestamps, and file info
     */
    public function getStudentSubmission($assignmentId, $studentId)
    {
        $db = \Config\Database::connect();
        return $db->table($this->table)
            ->select('student_submissions.*, assignments.total_score, assignments.title as assignment_title')
            ->join('assignments', 'assignments.id = student_submissions.assignment_id', 'left')
            ->where('student_submissions.assignment_id', $assignmentId)
            ->where('student_submissions.student_id', $studentId)
            ->get()
            ->getRowArray();
    }

    /**
     * Update submission score and status
     */
    public function gradeSubmission($submissionId, $score)
    {
        return $this->update($submissionId, [
            'score' => $score,
            'status' => 'graded',
            'graded_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check if student has submitted for an assignment
     */
    public function hasSubmitted($assignmentId, $studentId)
    {
        return $this->where([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId
        ])->countAllResults() > 0;
    }
}
