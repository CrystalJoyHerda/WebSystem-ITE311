<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You must be logged in to enroll.'
            ]);
        }

        $user_id = session()->get('userID');
        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course ID is required.'
            ]);
        }

        $enrollmentModel = new EnrollmentModel();

        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Enroll user
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];  

        if ($enrollmentModel->enrollUser($data)) {
            // Get course details
            $db = \Config\Database::connect();
            $course = $db->table('courses')->where('id', $course_id)->get()->getRowArray();
            
            // Create notification for the enrolled user
            if ($course) {
                $notificationModel = new NotificationModel();
                $notificationModel->insert([
                    'user_id' => $user_id,
                    'message' => 'You have been enrolled in ' . $course['subject_name'],
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment successful.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment failed. Please try again.'
            ]);
        }
    }

    /**
     * Student requests enrollment in an assigned course
     */
    public function requestEnrollment()
    {
        // Check if user is logged in as student
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access.'
            ]);
        }

        $studentId = session()->get('userID');
        $courseId = $this->request->getPost('course_id');

        if (!$courseId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course ID is required.'
            ]);
        }

        $enrollmentModel = new EnrollmentModel();
        $notificationModel = new NotificationModel();
        $db = \Config\Database::connect();

        try {
            // Check if course is assigned or rejected for this student
            $enrollment = $enrollmentModel->where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->whereIn('status', ['assigned', 'rejected'])
                ->first();

            if (!$enrollment) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'This course is not available for enrollment request.'
                ]);
            }

            // Update enrollment status to 'pending'
            $enrollmentModel->update($enrollment['id'], [
                'status' => 'pending'
            ]);

            // Get course and teacher details
            $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
            $teacherId = $course['instructor_id'] ?? null;

            if ($teacherId) {
                // Notify teacher
                $student = $db->table('users')->where('id', $studentId)->get()->getRowArray();
                $courseName = $course['subject_name'] ?? $course['course_name'] ?? 'a course';
                $studentName = $student['name'] ?? 'A student';

                $notificationModel->insert([
                    'user_id' => $teacherId,
                    'type' => 'enrollment_request',
                    'message' => $studentName . ' has requested enrollment in: ' . $courseName,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Notify student
            $notificationModel->insert([
                'user_id' => $studentId,
                'type' => 'enrollment_request',
                'message' => 'Your enrollment request has been sent to the teacher for approval.',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment request sent to teacher for approval.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Request enrollment failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to send enrollment request.'
            ]);
        }
    }
}
