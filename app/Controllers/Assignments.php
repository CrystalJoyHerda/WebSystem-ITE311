<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use App\Models\NotificationModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Assignments extends Controller
{
    protected $assignmentModel;
    protected $notificationModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->notificationModel = new NotificationModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Create assignment (GET - show form, POST - process)
     */
    public function create($courseId)
    {
        // Check if user is logged in and is a teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized access']);
        }

        $teacherId = session()->get('user_id') ?? session()->get('userID');
        
        if (!$teacherId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User ID not found in session']);
        }
        
        // Verify teacher owns this course
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->where('instructor_id', $teacherId)->get()->getRowArray();
        
        if (!$course) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied or course not found']);
        }

        if ($this->request->is('post')) {
            return $this->processCreate($courseId, $teacherId, $course);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request method']);
    }

    /**
     * Process assignment creation
     */
    private function processCreate($courseId, $teacherId, $course)
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'due_date' => 'required',
            'total_score' => 'required|numeric|greater_than[0]'
        ];
        
        // Only validate file if one is uploaded
        $file = $this->request->getFile('assignment_file');
        if ($file && $file->isValid()) {
            $rules['assignment_file'] = 'max_size[assignment_file,10240]|ext_in[assignment_file,pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png]';
        }
        
        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Assignment validation failed: ' . json_encode($validation->getErrors()));
            return $this->response->setJSON([
                'status' => 'error',
                'message' => implode(', ', $validation->getErrors())
            ]);
        }
        
        // Convert datetime-local format to MySQL datetime format
        $dueDate = $this->request->getPost('due_date');
        if ($dueDate) {
            // Input format: "2025-12-12T23:59"
            // MySQL format: "2025-12-12 23:59:00"
            $dueDate = str_replace('T', ' ', $dueDate) . ':00';
        }

        $data = [
            'course_id' => $courseId,
            'teacher_id' => $teacherId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date' => $dueDate,
            'total_score' => $this->request->getPost('total_score')
        ];

        // Handle file upload
        $file = $this->request->getFile('assignment_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . '../public/uploads/assignments', $newName);
            
            $data['file_name'] = $file->getClientName();
            $data['file_path'] = 'uploads/assignments/' . $newName;
        }

        $assignmentId = $this->assignmentModel->createAssignment($data);

        if ($assignmentId) {
            // Notify all enrolled students in this course
            $this->notifyStudentsAboutNewAssignment($courseId, $data['title'], $course['subject_name'] ?? 'this course');
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Assignment created successfully!',
                'assignment_id' => $assignmentId
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create assignment. Please try again.'
            ]);
        }
    }

    /**
     * Notify enrolled students about new assignment
     */
    private function notifyStudentsAboutNewAssignment($courseId, $assignmentTitle, $courseName)
    {
        $db = \Config\Database::connect();
        
        // Get all enrolled students
        $enrollments = $db->table('enrollments')
            ->select('user_id')
            ->where('course_id', $courseId)
            ->where('status', 'enrolled')
            ->get()
            ->getResultArray();

        foreach ($enrollments as $enrollment) {
            $this->notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'type' => 'assignment_created',
                'message' => 'New assignment "' . $assignmentTitle . '" has been created for ' . $courseName,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * View assignments for a course (AJAX)
     */
    public function view($courseId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $assignments = $this->assignmentModel->getAssignmentsWithSubmissionCount($courseId);
        
        return $this->response->setJSON([
            'status' => 'success',
            'assignments' => $assignments
        ]);
    }

    /**
     * Delete assignment
     */
    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $teacherId = session()->get('user_id') ?? session()->get('userID');
        $assignment = $this->assignmentModel->find($id);
        
        if (!$assignment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Assignment not found']);
        }

        // Verify teacher owns the course
        if ($assignment['teacher_id'] != $teacherId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied']);
        }

        // Delete file if exists
        if ($assignment['file_path'] && file_exists(FCPATH . $assignment['file_path'])) {
            unlink(FCPATH . $assignment['file_path']);
        }

        if ($this->assignmentModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Assignment deleted']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete assignment']);
    }

    /**
     * Download assignment file
     */
    public function download($id)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to download files');
            return redirect()->to(base_url('login'));
        }

        $assignment = $this->assignmentModel->find($id);
        
        if (!$assignment || !$assignment['file_path']) {
            session()->setFlashdata('error', 'File not found');
            return redirect()->back();
        }

        $filePath = FCPATH . $assignment['file_path'];
        
        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File does not exist');
            return redirect()->back();
        }

        return $this->response->download($filePath, null)->setFileName($assignment['file_name']);
    }
}
