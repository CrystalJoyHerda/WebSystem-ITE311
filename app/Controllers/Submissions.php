<?php

namespace App\Controllers;

use App\Models\SubmissionModel;
use App\Models\AssignmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Submissions extends Controller
{
    protected $submissionModel;
    protected $assignmentModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->submissionModel = new SubmissionModel();
        $this->assignmentModel = new AssignmentModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Submit assignment (student)
     */
    public function submit($assignmentId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $studentId = session()->get('user_id') ?? session()->get('userID');
        
        // Check if already submitted
        if ($this->submissionModel->hasSubmitted($assignmentId, $studentId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You have already submitted this assignment']);
        }

        $assignment = $this->assignmentModel->find($assignmentId);
        if (!$assignment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Assignment not found']);
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'text_submission' => 'permit_empty|max_length[5000]'
        ];
        
        // Only validate file if one is uploaded
        $file = $this->request->getFile('submission_file');
        if ($file && $file->isValid()) {
            $rules['submission_file'] = 'max_size[submission_file,10240]|ext_in[submission_file,pdf,doc,docx,ppt,pptx,zip,jpg,jpeg,png,txt]';
        }
        
        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['status' => 'error', 'message' => implode(', ', $validation->getErrors())]);
        }

        $textSubmission = $this->request->getPost('text_submission');
        $file = $this->request->getFile('submission_file');

        // Must have either text or file
        if (empty($textSubmission) && (!$file || !$file->isValid())) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please provide a text submission or upload a file']);
        }

        $data = [
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'text_submission' => $textSubmission,
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ];

        // Handle file upload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . '../public/uploads/submissions', $newName);
            
            $data['file_name'] = $file->getClientName();
            $data['file_path'] = 'uploads/submissions/' . $newName;
        }

        if ($this->submissionModel->createSubmission($data)) {
            // Notify teacher
            $this->notifyTeacherAboutSubmission($assignment, session()->get('name'));
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Submission uploaded successfully!']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit assignment']);
    }

    /**
     * Notify teacher about student submission
     */
    private function notifyTeacherAboutSubmission($assignment, $studentName)
    {
        $this->notificationModel->insert([
            'user_id' => $assignment['teacher_id'],
            'type' => 'submission_received',
            'message' => $studentName . ' submitted "' . $assignment['title'] . '"',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Download student submission (teacher)
     */
    public function downloadSubmission($submissionId)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login');
            return redirect()->to(base_url('login'));
        }

        $submission = $this->submissionModel->find($submissionId);
        
        if (!$submission || !$submission['file_path']) {
            session()->setFlashdata('error', 'File not found');
            return redirect()->back();
        }

        $filePath = FCPATH . $submission['file_path'];
        
        if (!file_exists($filePath)) {
            session()->setFlashdata('error', 'File does not exist');
            return redirect()->back();
        }

        return $this->response->download($filePath, null)->setFileName($submission['file_name']);
    }

    /**
     * Grade submission (teacher)
     */
    public function grade($submissionId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $submission = $this->submissionModel->find($submissionId);
        
        if (!$submission) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Submission not found']);
        }

        $assignment = $this->assignmentModel->find($submission['assignment_id']);
        
        $teacherId = session()->get('user_id') ?? session()->get('userID');
        
        // Verify teacher owns this assignment
        if ($assignment['teacher_id'] != $teacherId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied']);
        }

        $score = $this->request->getPost('score');
        
        if (!is_numeric($score) || $score < 0 || $score > $assignment['total_score']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid score']);
        }

        if ($this->submissionModel->gradeSubmission($submissionId, $score)) {
            // Notify student
            $this->notifyStudentAboutGrade($submission['student_id'], $assignment['title'], $score, $assignment['total_score']);
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Submission graded successfully']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to grade submission']);
    }

    /**
     * Notify student about graded submission
     */
    private function notifyStudentAboutGrade($studentId, $assignmentTitle, $score, $totalScore)
    {
        $this->notificationModel->insert([
            'user_id' => $studentId,
            'type' => 'submission_graded',
            'message' => 'Your submission for "' . $assignmentTitle . '" has been graded: ' . $score . '/' . $totalScore,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * View submissions for an assignment (teacher)
     */
    public function viewSubmissions($assignmentId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $submissions = $this->submissionModel->getSubmissionsByAssignment($assignmentId);
        
        return $this->response->setJSON([
            'status' => 'success',
            'submissions' => $submissions
        ]);
    }

    /**
     * Check submission status for a specific assignment (student)
     * Returns submission details including grade, status, timestamps, and file info
     */
    public function checkStatus($assignmentId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $studentId = session()->get('user_id') ?? session()->get('userID');
        $submission = $this->submissionModel->getStudentSubmission($assignmentId, $studentId);
        
        if (!$submission) {
            return $this->response->setJSON([
                'status' => 'success',
                'submitted' => false,
                'submission' => null
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'submitted' => true,
            'submission' => [
                'id' => $submission['id'],
                'status' => $submission['status'],
                'score' => $submission['score'],
                'total_score' => $submission['total_score'],
                'submitted_at' => $submission['submitted_at'],
                'graded_at' => $submission['graded_at'],
                'file_name' => $submission['file_name'],
                'file_path' => $submission['file_path'],
                'text_submission' => $submission['text_submission']
            ]
        ]);
    }
}
