<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizSubmissionModel extends Model
{
    protected $table = 'quiz_submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'quiz_id',
        'student_id',
        'score',
        'total_points',
        'submitted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'quiz_id' => 'required|integer',
        'student_id' => 'required|integer',
        'total_points' => 'required|integer'
    ];

    protected $validationMessages = [
        'quiz_id' => [
            'required' => 'Quiz ID is required',
            'integer' => 'Quiz ID must be an integer'
        ],
        'student_id' => [
            'required' => 'Student ID is required',
            'integer' => 'Student ID must be an integer'
        ]
    ];

    /**
     * Check if student has already submitted this quiz
     */
    public function hasSubmitted($quizId, $studentId)
    {
        return $this->where([
            'quiz_id' => $quizId,
            'student_id' => $studentId
        ])->first() !== null;
    }

    /**
     * Get student's submission for a quiz
     */
    public function getStudentSubmission($quizId, $studentId)
    {
        return $this->where([
            'quiz_id' => $quizId,
            'student_id' => $studentId
        ])->first();
    }

    /**
     * Get all submissions for a quiz
     */
    public function getQuizSubmissions($quizId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('quiz_submissions')
            ->select('quiz_submissions.*, users.name as student_name, users.email as student_email')
            ->join('users', 'users.id = quiz_submissions.student_id')
            ->where('quiz_submissions.quiz_id', $quizId)
            ->orderBy('quiz_submissions.submitted_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all submissions by a student
     */
    public function getStudentSubmissions($studentId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('quiz_submissions')
            ->select('quiz_submissions.*, quizzes.title as quiz_title, courses.name as course_name')
            ->join('quizzes', 'quizzes.id = quiz_submissions.quiz_id')
            ->join('courses', 'courses.id = quizzes.course_id')
            ->where('quiz_submissions.student_id', $studentId)
            ->orderBy('quiz_submissions.submitted_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Create a new submission
     */
    public function createSubmission($data)
    {
        return $this->insert($data);
    }

    /**
     * Update submission score
     */
    public function updateScore($submissionId, $score, $totalPoints)
    {
        return $this->update($submissionId, [
            'score' => $score,
            'total_points' => $totalPoints
        ]);
    }
}
