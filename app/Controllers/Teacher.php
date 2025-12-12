<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\LessonModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Teacher extends BaseController
{
    protected $courseModel;
    protected $lessonModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->lessonModel = new LessonModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function dashboard()
    {
        // Check if user is logged in and is a teacher
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to(base_url('login'));
        }

        $teacherId = $session->get('userID');
        
        // Get all courses assigned to this teacher with enrollment stats
        $data['courses'] = $this->courseModel->getTeacherCoursesWithStats($teacherId);
        $data['role'] = 'teacher';
        $data['userName'] = $session->get('name') ?? $session->get('userName') ?? 'Teacher';

        return view('teacher_dashboard', $data);
    }

    /**
     * AJAX endpoint to get subjects (lessons) for a specific course
     */
    public function getCourseSubjects($courseId)
    {
        // Check if user is logged in and is a teacher
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $teacherId = $session->get('userID');
        
        // Verify this course belongs to the teacher
        $course = $this->courseModel->find($courseId);
        if (!$course || $course['instructor_id'] != $teacherId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course not found or access denied'
            ])->setStatusCode(403);
        }

        // Get lessons with quiz count
        $lessons = $this->lessonModel->getLessonsWithQuizCount($courseId);
        
        // Get enrollment count for this course
        $db = \Config\Database::connect();
        $enrollmentCount = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->countAllResults();

        return $this->response->setJSON([
            'status' => 'success',
            'course' => $course,
            'subjects' => $lessons,
            'enrollmentCount' => $enrollmentCount
        ]);
    }

    /**
     * Get students enrolled in a course
     */
    public function getCourseStudents($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $teacherId = $session->get('userID');
        $course = $this->courseModel->find($courseId);
        
        if (!$course || $course['instructor_id'] != $teacherId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        $students = $db->table('enrollments')
            ->select('users.id, users.name, users.email, enrollments.enrollment_date, enrollments.status, enrollments.id as enrollment_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $courseId)
            ->whereIn('enrollments.status', ['enrolled', 'pending'])
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'students' => $students
        ]);
    }

    /**
     * Get quizzes for a course
     */
    public function getCourseQuizzes($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $teacherId = $session->get('userID');
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            log_message('error', 'Course not found: ' . $courseId);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course not found'])->setStatusCode(404);
        }
        
        if (!isset($course['instructor_id']) || $course['instructor_id'] != $teacherId) {
            log_message('error', 'Access denied for teacher ' . $teacherId . ' to course ' . $courseId . '. Instructor ID: ' . ($course['instructor_id'] ?? 'NULL'));
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied - Course instructor_id: ' . ($course['instructor_id'] ?? 'NULL') . ', Your ID: ' . $teacherId])->setStatusCode(403);
        }

        // Get quizzes through lessons table
        $db = \Config\Database::connect();
        
        // Check if quizzes table exists
        if ($db->tableExists('quizzes')) {
            $quizzes = $db->table('quizzes')
                ->select('quizzes.*, lessons.title as lesson_title')
                ->join('lessons', 'lessons.id = quizzes.lesson_id', 'left')
                ->where('lessons.course_id', $courseId)
                ->get()
                ->getResultArray();
        } else {
            // Return empty array if table doesn't exist
            $quizzes = [];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'quizzes' => $quizzes
        ]);
    }

    /**
     * Get assignments for a course
     */
    public function getCourseAssignments($courseId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $teacherId = $session->get('userID');
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            log_message('error', 'Course not found: ' . $courseId);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Course not found'])->setStatusCode(404);
        }
        
        if (!isset($course['instructor_id']) || $course['instructor_id'] != $teacherId) {
            log_message('error', 'Access denied for teacher ' . $teacherId . ' to course ' . $courseId . '. Instructor ID: ' . ($course['instructor_id'] ?? 'NULL'));
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied - Course instructor_id: ' . ($course['instructor_id'] ?? 'NULL') . ', Your ID: ' . $teacherId])->setStatusCode(403);
        }

        // Get assignments
        $db = \Config\Database::connect();
        
        // Check if assignments table exists
        if ($db->tableExists('assignments')) {
            // Check if assignments has course_id or lesson_id
            $fields = $db->getFieldNames('assignments');
            
            if (in_array('course_id', $fields)) {
                // Direct course_id relationship
                $assignments = $db->table('assignments')
                    ->where('course_id', $courseId)
                    ->get()
                    ->getResultArray();
            } elseif (in_array('lesson_id', $fields)) {
                // Join through lessons table
                $assignments = $db->table('assignments')
                    ->select('assignments.*, lessons.title as lesson_title')
                    ->join('lessons', 'lessons.id = assignments.lesson_id', 'left')
                    ->where('lessons.course_id', $courseId)
                    ->get()
                    ->getResultArray();
            } else {
                $assignments = [];
            }
        } else {
            // Return empty array if table doesn't exist
            $assignments = [];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'assignments' => $assignments
        ]);
    }

    /**
     * Approve student enrollment request
     */
    public function approveEnrollment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $enrollmentId = $this->request->getPost('enrollment_id');
        if (!$enrollmentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment ID required']);
        }

        $teacherId = $session->get('userID');
        $db = \Config\Database::connect();

        try {
            // Get enrollment details
            $enrollment = $db->table('enrollments')
                ->select('enrollments.*, courses.instructor_id, courses.subject_name, users.name as student_name')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->join('users', 'users.id = enrollments.user_id')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment request not found']);
            }

            // Verify teacher owns this course
            if ($enrollment['instructor_id'] != $teacherId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'])->setStatusCode(403);
            }

            // Update enrollment status
            $this->enrollmentModel->update($enrollmentId, [
                'status' => 'enrolled',
                'approved_by' => $teacherId,
                'enrollment_date' => date('Y-m-d H:i:s')
            ]);

            // Notify student
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'type' => 'enrollment_approved',
                'message' => 'Your enrollment request for "' . $enrollment['subject_name'] . '" has been approved!',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment approved for ' . $enrollment['student_name']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Approve enrollment failed: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to approve enrollment']);
        }
    }

    /**
     * Reject student enrollment request
     */
    public function rejectEnrollment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $enrollmentId = $this->request->getPost('enrollment_id');
        if (!$enrollmentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment ID required']);
        }

        $teacherId = $session->get('userID');
        $db = \Config\Database::connect();

        try {
            // Get enrollment details
            $enrollment = $db->table('enrollments')
                ->select('enrollments.*, courses.instructor_id, courses.subject_name, users.name as student_name')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->join('users', 'users.id = enrollments.user_id')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Enrollment request not found']);
            }

            // Verify teacher owns this course
            if ($enrollment['instructor_id'] != $teacherId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied'])->setStatusCode(403);
            }

            // Update enrollment status
            $this->enrollmentModel->update($enrollmentId, [
                'status' => 'rejected',
                'approved_by' => $teacherId
            ]);

            // Notify student
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'type' => 'enrollment_rejected',
                'message' => 'Your enrollment request for "' . $enrollment['subject_name'] . '" was not approved.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment rejected for ' . $enrollment['student_name']
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Reject enrollment failed: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to reject enrollment']);
        }
    }
}

