<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseBuilder;
use App\Models\MaterialModel;
use App\Models\QuizModel;
use App\Models\QuizSubmissionModel;
use App\Models\QuizAnswerModel;
use App\Models\QuizQuestionModel;
use App\Models\NotificationModel;

class Auth extends Controller
{
    protected $db;
    protected $builder;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('users');
    }

    public function register()
    {
        helper(['form']);
        $data = [];

        if ($this->request->is('post')) {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z\s\'-]+$/]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'matches[password]'
            ];

            if ($this->validate($rules)) {
                $newData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role'       => 'student', 
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($this->builder->insert($newData)) {
                    // Use a normal session variable (not flashdata) so the login
                    // page can display the message once and then remove it.
                    session()->set('registration_success', 'User registered successfully!');
                    return redirect()->to(base_url('login'));
                } else {
                    session()->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    public function login()
{
    helper(['form']);
    $data = [];

    if ($this->request->is('post')) {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]|max_length[255]'
        ];

        if ($this->validate($rules)) {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            // Fetch the user by email using the DB builder (returns array or null)
            $user = $this->builder->where('email', $email)->get()->getRowArray();

            if ($user) {
                // Check if user account is inactive
                if (isset($user['status']) && $user['status'] === 'inactive') {
                    session()->setFlashdata('error', 'Your account is inactive. Please contact the administrator.');
                    return view('auth/login', $data);
                }

                // Verify password
                if (password_verify($password, $user['password'])) {
                    session()->set([
                        'userID'     => $user['id'],
                        'name'       => $user['name'],
                        'email'      => $user['email'],
                        'role'       => $user['role'],
                        'isLoggedIn' => true    
                    ]);
                    // after session()->set([...])
log_message('debug', 'Auth session: ' . json_encode(session()->get()));

                    session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');

                    // Redirect all authenticated users to the unified dashboard view
                    $role = $user['role'];
                    if ($role === 'admin') {
                        return redirect()->to(base_url('admin/dashboard'));
                    } elseif ($role === 'teacher') {
                        return redirect()->to(base_url('teacher/dashboard'));
                    } else {
                        return redirect()->to(base_url('student/dashboard'));
                    }
                } else {
                    session()->setFlashdata('error', 'Invalid email or password.');
                }
            } else {
                session()->setFlashdata('error', 'Invalid email or password.');
            }
        } else {
            $data['validation'] = $this->validator;
        }
    }

    return view('auth/login', $data);
}
public function logout()
{
    // Destroy all session data
    session()->destroy();

    // Optional: Add a flash message
    session()->setFlashdata('success', 'You have been logged out successfully.');

    // Redirect to login page
    return redirect()->to(base_url('login'));
}
public function dashboard()
{
    // Authorization check
    if (!session()->get('isLoggedIn')) {
        return redirect()->to(base_url('login'));
    }

    $role = session()->get('role');
    $db = \Config\Database::connect();

    $data = [
        'role' => $role,
        'name' => session()->get('name')
    ];

        if ($role === 'admin') {
        // Use DB table queries for user counts and lists when UserModel is not present
        /** @var BaseBuilder $usersBuilder */
        $usersBuilder = $db->table('users');
        /** @var BaseBuilder $coursesBuilder */
        $coursesBuilder = $db->table('courses');
            // Only count and list active users if column exists
        try {
            $userFields = $db->getFieldNames('users');
            if (in_array('status', $userFields)) {
                $data['totalUsers']   = $usersBuilder->where('status', 'active')->countAllResults();
                $data['users'] = $usersBuilder->where('status', 'active')->get()->getResultArray();
            } else {
                // status column not present yet; treat all users as active
                $data['totalUsers'] = $usersBuilder->countAllResults();
                $data['users'] = $usersBuilder->get()->getResultArray();
                session()->setFlashdata('warning', 'Status column missing; showing all users. Run migrations to enable soft-delete.');
            }

            $data['totalCourses'] = $coursesBuilder->countAllResults();
            $data['coursesList'] = $coursesBuilder
                ->select('id, subject_name AS name, description')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            // On error, fallback to safe defaults
            $data['totalUsers'] = 0;
            $data['users'] = [];
            $data['totalCourses'] = 0;
            $data['coursesList'] = [];
            log_message('error', 'Auth::dashboard failed to load users: ' . $e->getMessage());
        }
    } elseif ($role === 'teacher') {
        $userId = session()->get('userID'); // Get the teacher's ID
        
        log_message('debug', 'Teacher ID: ' . $userId);
        
        /** @var BaseBuilder $teacherCoursesBuilder */
        $teacherCoursesBuilder = $db->table('courses');
        $data['courses'] = $teacherCoursesBuilder
            ->select('id, subject_name AS name, description, semester, subject_code')
            ->where('instructor_id', $userId)
            ->get()
            ->getResultArray();
        
        log_message('debug', 'Teacher courses found: ' . count($data['courses']));
        
        if (empty($data['courses'])) {
            $data['courses'] = [];
        }
    } elseif ($role === 'student') {
        // Detect the enrollments table user/student column dynamically
        $fields = $db->getFieldData('enrollments'); // returns array of field objects
        $fieldNames = array_map(function($f){ return $f->name; }, $fields);

        $candidates = ['user_id', 'student_id', 'userID', 'userid', 'studentid', 'studentID'];
        $enrollmentUserCol = null;
        foreach ($candidates as $cand) {
            // case-insensitive check against real column names
            foreach ($fieldNames as $fn) {
                if (strcasecmp($fn, $cand) === 0) {
                    $enrollmentUserCol = $fn; // use actual column name from DB
                    break 2;
                }
            }
        }

        if (! $enrollmentUserCol) {
            // Fail early with a clear message for development environment
            throw new \RuntimeException("enrollments table is missing a user/student foreign-key column. Checked: " . implode(', ', $candidates));
        }

        // Get enrolled courses with details
        /** @var BaseBuilder $enrollmentsBuilder */
        $enrollmentsBuilder = $db->table('enrollments');
        $data['enrolledCourses'] = $enrollmentsBuilder
            ->select('courses.id, courses.subject_code as code, courses.subject_name as name, courses.description, courses.semester, enrollments.enrollment_date')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where("enrollments.{$enrollmentUserCol}", session()->get('userID'))
            ->where('enrollments.status', 'enrolled')
            ->get()
            ->getResultArray();

        // Get available courses (not enrolled)
        $enrolledCourseIds = array_column($data['enrolledCourses'], 'id');
        log_message('debug', 'Auth::dashboard detected enrollment column: ' . $enrollmentUserCol . ' enrolled_count: ' . count($enrolledCourseIds));

        if (! empty($enrolledCourseIds)) {
            // Fetch materials for enrolled courses
            $materialModel = new MaterialModel();
            $materials = $materialModel->whereIn('course_id', $enrolledCourseIds)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            // Group materials by course_id for easy rendering
            $courseMaterials = [];
            foreach ($materials as $m) {
                $courseMaterials[intval($m['course_id'])][] = $m;
            }
            $data['courseMaterials'] = $courseMaterials;
            
            // Get assigned/pending/rejected courses for this student
            $assignedCourses = $db->table('enrollments')
                ->select('courses.id, courses.subject_code as code, courses.subject_name as name, courses.description, courses.semester, enrollments.status')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where("enrollments.{$enrollmentUserCol}", session()->get('userID'))
                ->whereIn('enrollments.status', ['assigned', 'pending', 'rejected'])
                ->get()
                ->getResultArray();
            $data['availableCourses'] = $assignedCourses;
        } else {
            $data['courseMaterials'] = [];
            
            // Get assigned/pending/rejected courses for this student
            $assignedCourses = $db->table('enrollments')
                ->select('courses.id, courses.subject_code as code, courses.subject_name as name, courses.description, courses.semester, enrollments.status')
                ->join('courses', 'courses.id = enrollments.course_id')
                ->where("enrollments.{$enrollmentUserCol}", session()->get('userID'))
                ->whereIn('enrollments.status', ['assigned', 'pending', 'rejected'])
                ->get()
                ->getResultArray();
            $data['availableCourses'] = $assignedCourses;
        }
    }
    /** @var BaseBuilder $coursesListBuilder */
    $coursesListBuilder = $db->table('courses');
    $data['coursesList'] = $coursesListBuilder->get()->getResultArray();

    return view('auth/dashboard', $data);
}

    /**
     * Get quizzes for a course (student view)
     */
    public function getCourseQuizzes($courseId)
    {
        $session = session();
        $studentId = $session->get('userID');
        
        if (!$studentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        // Check if student is enrolled in this course
        $db = \Config\Database::connect();
        $enrollment = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->where('user_id', $studentId)
            ->where('status', 'enrolled')
            ->get()
            ->getRowArray();

        if (!$enrollment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not enrolled in this course'])->setStatusCode(403);
        }

        $quizModel = new QuizModel();
        $submissionModel = new QuizSubmissionModel();
        $quizQuestionModel = new QuizQuestionModel();
        
        // Get all quizzes for this course with stats
        $quizzes = $quizModel->getQuizWithStats($courseId);

        // Add submission status for each quiz
        foreach ($quizzes as &$quiz) {
            $submission = $submissionModel->getStudentSubmission($quiz['id'], $studentId);
            $quiz['submitted'] = $submission !== null;
            $quiz['score'] = $submission['score'] ?? null;
            $quiz['question_count'] = $quiz['question_count'] ?? 0;
            $quiz['total_points'] = $quiz['total_points'] ?? 0;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'quizzes' => $quizzes
        ]);
    }

    /**
     * Get a specific quiz for taking (student view)
     */
    public function getQuiz($quizId)
    {
        $session = session();
        $studentId = $session->get('userID');
        
        if (!$studentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quizId);

        if (!$quiz) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Quiz not found'])->setStatusCode(404);
        }

        // Check if student is enrolled in the course
        $db = \Config\Database::connect();
        $enrollment = $db->table('enrollments')
            ->where('course_id', $quiz['course_id'])
            ->where('user_id', $studentId)
            ->where('status', 'enrolled')
            ->get()
            ->getRowArray();

        if (!$enrollment) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not enrolled in this course'])->setStatusCode(403);
        }

        // Check if already submitted
        $submissionModel = new QuizSubmissionModel();
        if ($submissionModel->hasSubmitted($quizId, $studentId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Quiz already submitted'])->setStatusCode(400);
        }

        // Get all questions for this quiz
        $quizQuestionModel = new QuizQuestionModel();
        $questions = $quizQuestionModel->getQuizQuestions($quizId);

        return $this->response->setJSON([
            'status' => 'success',
            'quiz' => $quiz,
            'questions' => $questions
        ]);
    }

    /**
     * Submit quiz (student) - handles multiple questions
     */
    public function submitQuiz()
    {
        $session = session();
        $studentId = $session->get('userID');
        
        if (!$studentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $quizId = $this->request->getPost('quiz_id');
        $answers = $this->request->getPost('answers'); // Array of answers

        $quizModel = new QuizModel();
        $quiz = $quizModel->find($quizId);

        if (!$quiz) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Quiz not found'])->setStatusCode(404);
        }

        // Check if already submitted
        $submissionModel = new QuizSubmissionModel();
        if ($submissionModel->hasSubmitted($quizId, $studentId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Quiz already submitted'])->setStatusCode(400);
        }

        // Get all questions
        $quizQuestionModel = new QuizQuestionModel();
        $questions = $quizQuestionModel->getQuizQuestions($quizId);

        $totalPoints = 0;
        $totalEarned = 0;
        $answersToSave = [];
        $hasSentenceType = false;

        foreach ($questions as $index => $question) {
            $totalPoints += $question['points'];
            $studentAnswer = $answers[$question['id']] ?? '';
            
            $pointsEarned = 0;
            $isCorrect = 0;

            // Auto-grade MCQ and True/False, leave sentence type for teacher grading
            if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
                $isCorrect = ($studentAnswer === $question['correct_answer']) ? 1 : 0;
                $pointsEarned = $isCorrect ? $question['points'] : 0;
                $totalEarned += $pointsEarned;
            } else {
                // Sentence type - will be graded manually
                $hasSentenceType = true;
            }

            $answersToSave[] = [
                'quiz_id' => $quizId,
                'question_id' => $question['id'],
                'question_index' => $index,
                'student_answer' => $studentAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned
            ];
        }

        // Calculate score (only from auto-graded questions if there are sentence types)
        $scorePercentage = null;
        if (!$hasSentenceType && $totalPoints > 0) {
            $scorePercentage = ($totalEarned / $totalPoints) * 100;
        }

        try {
            // Create submission
            $submissionId = $submissionModel->createSubmission([
                'quiz_id' => $quizId,
                'student_id' => $studentId,
                'score' => $scorePercentage,
                'total_points' => $totalPoints,
                'submitted_at' => date('Y-m-d H:i:s')
            ]);

            // Add submission_id to all answers
            foreach ($answersToSave as &$answer) {
                $answer['submission_id'] = $submissionId;
            }

            // Save all answers
            $answerModel = new QuizAnswerModel();
            $answerModel->saveAnswers($answersToSave);

            // Notify teacher
            $notificationModel = new NotificationModel();
            $student = $this->db->table('users')->where('id', $studentId)->get()->getRowArray();
            $message = $student['name'] . ' submitted quiz "' . $quiz['title'] . '"';
            if ($scorePercentage !== null) {
                $message .= ' - Score: ' . number_format($scorePercentage, 2) . '%';
            } else {
                $message .= ' - Requires grading';
            }
            
            $notificationModel->createNotification([
                'user_id' => $quiz['teacher_id'],
                'message' => $message,
                'type' => 'quiz_submission'
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $hasSentenceType ? 'Quiz submitted! Your teacher will grade the written answers.' : 'Quiz submitted successfully!',
                'score' => $scorePercentage,
                'total_earned' => $totalEarned,
                'total_points' => $totalPoints,
                'requires_grading' => $hasSentenceType
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Submit quiz failed: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit quiz: ' . $e->getMessage()]);
        }
    }

}
