<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_id',
        'teacher_id',
        'lesson_id',
        'title',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'course_id' => 'required|integer',
        'teacher_id' => 'required|integer',
        'title' => 'required|max_length[200]'
    ];

    protected $validationMessages = [
        'course_id' => [
            'required' => 'Course ID is required',
            'integer' => 'Course ID must be an integer'
        ],
        'title' => [
            'required' => 'Quiz title is required',
            'max_length' => 'Quiz title cannot exceed 200 characters'
        ]
    ];

    /**
     * Get all quizzes for a specific course
     */
    public function getQuizzesByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get quiz with submission count, pass rate, and question count
     */
    public function getQuizWithStats($courseId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('quizzes')
            ->select('quizzes.*, 
                      COUNT(DISTINCT quiz_submissions.id) as submissions,
                      COUNT(DISTINCT CASE WHEN quiz_submissions.score >= 75 THEN quiz_submissions.id END) as passed,
                      (SELECT COUNT(*) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) as question_count,
                      (SELECT SUM(points) FROM quiz_questions WHERE quiz_questions.quiz_id = quizzes.id) as total_points')
            ->join('quiz_submissions', 'quiz_submissions.quiz_id = quizzes.id', 'left')
            ->where('quizzes.course_id', $courseId)
            ->groupBy('quizzes.id')
            ->orderBy('quizzes.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get quiz details with teacher and course info
     */
    public function getQuizDetails($quizId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('quizzes')
            ->select('quizzes.*, users.name as teacher_name, courses.name as course_name, courses.subject_code')
            ->join('users', 'users.id = quizzes.teacher_id', 'left')
            ->join('courses', 'courses.id = quizzes.course_id', 'left')
            ->where('quizzes.id', $quizId)
            ->get()
            ->getRowArray();
    }

    /**
     * Create a new quiz
     */
    public function createQuiz($data)
    {
        return $this->insert($data);
    }

    /**
     * Delete a quiz
     */
    public function deleteQuiz($quizId)
    {
        return $this->delete($quizId);
    }
}
