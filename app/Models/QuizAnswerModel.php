<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizAnswerModel extends Model
{
    protected $table = 'quiz_answers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'submission_id',
        'quiz_id',
        'question_index',
        'student_answer',
        'is_correct',
        'points_earned'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'submission_id' => 'required|integer',
        'quiz_id' => 'required|integer',
        'question_index' => 'required|integer'
    ];

    protected $validationMessages = [
        'submission_id' => [
            'required' => 'Submission ID is required',
            'integer' => 'Submission ID must be an integer'
        ],
        'quiz_id' => [
            'required' => 'Quiz ID is required',
            'integer' => 'Quiz ID must be an integer'
        ]
    ];

    /**
     * Get all answers for a submission
     */
    public function getSubmissionAnswers($submissionId)
    {
        return $this->where('submission_id', $submissionId)
                    ->orderBy('question_index', 'ASC')
                    ->findAll();
    }

    /**
     * Save student answers
     */
    public function saveAnswers($answers)
    {
        return $this->insertBatch($answers);
    }

    /**
     * Get answer for a specific question in a submission
     */
    public function getAnswer($submissionId, $questionIndex)
    {
        return $this->where([
            'submission_id' => $submissionId,
            'question_index' => $questionIndex
        ])->first();
    }
}
