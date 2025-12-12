<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizQuestionModel extends Model
{
    protected $table = 'quiz_questions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'quiz_id',
        'question',
        'question_type',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'points',
        'question_order'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'quiz_id' => 'required|integer',
        'question' => 'required',
        'question_type' => 'required|in_list[multiple_choice,true_false,sentence]',
        'points' => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'quiz_id' => [
            'required' => 'Quiz ID is required',
            'integer' => 'Quiz ID must be an integer'
        ],
        'question' => [
            'required' => 'Question text is required'
        ],
        'question_type' => [
            'required' => 'Question type is required',
            'in_list' => 'Question type must be either multiple_choice or sentence'
        ],
        'points' => [
            'required' => 'Points are required',
            'integer' => 'Points must be an integer',
            'greater_than' => 'Points must be greater than 0'
        ]
    ];

    /**
     * Get all questions for a quiz
     */
    public function getQuizQuestions($quizId)
    {
        return $this->where('quiz_id', $quizId)
                    ->orderBy('question_order', 'ASC')
                    ->findAll();
    }

    /**
     * Save multiple questions for a quiz
     */
    public function saveQuestions($questions)
    {
        return $this->insertBatch($questions);
    }

    /**
     * Delete all questions for a quiz
     */
    public function deleteQuizQuestions($quizId)
    {
        return $this->where('quiz_id', $quizId)->delete();
    }

    /**
     * Get question count for a quiz
     */
    public function getQuestionCount($quizId)
    {
        return $this->where('quiz_id', $quizId)->countAllResults();
    }

    /**
     * Get total points for a quiz
     */
    public function getTotalPoints($quizId)
    {
        $result = $this->selectSum('points')
                       ->where('quiz_id', $quizId)
                       ->first();
        
        return $result['points'] ?? 0;
    }
}
