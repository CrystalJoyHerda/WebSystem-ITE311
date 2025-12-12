<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table = 'lessons';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id', 
        'title', 
        'content', 
        'duration_minutes', 
        'created_at', 
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all lessons (subjects) for a specific course
     */
    public function getLessonsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get lesson count for a course
     */
    public function getLessonCount($courseId)
    {
        return $this->where('course_id', $courseId)->countAllResults();
    }

    /**
     * Get lessons with quiz count for a course
     */
    public function getLessonsWithQuizCount($courseId)
    {
        $db = \Config\Database::connect();
        
        return $db->table($this->table)
            ->select('lessons.*, COUNT(DISTINCT quizzes.id) as quiz_count')
            ->join('quizzes', 'quizzes.lesson_id = lessons.id', 'left')
            ->where('lessons.course_id', $courseId)
            ->groupBy('lessons.id')
            ->orderBy('lessons.created_at', 'ASC')
            ->get()
            ->getResultArray();
    }
}
