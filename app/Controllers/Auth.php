<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\MaterialModel;

class Auth extends Controller
{
    protected $db;
    protected $builder;

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
                'name' => 'required|min_length[3]|max_length[100]',
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
                    session()->setFlashdata('success', 'Registration successful. You can now log in.');
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
            
            $user = $this->builder
                ->where('email', $email)
                ->get()
                ->getRowArray();

            if ($user && password_verify($password, $user['password'])) {
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
        $data['totalUsers']   = $db->table('users')->countAllResults();
        $data['totalCourses'] = $db->table('courses')->countAllResults();
        $data['users'] = $db->table('users')->get()->getResultArray();
        $data['coursesList'] = $db->table('courses')
            ->select('id, course_name AS name, description')
            ->get()
            ->getResultArray();
    } elseif ($role === 'teacher') {
        $userId = session()->get('userID'); // Get the teacher's ID
        
        log_message('debug', 'Teacher ID: ' . $userId);
        
        $data['courses'] = $db->table('courses')
            ->select('id, course_name AS name, description, semester, course_code')
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
        $data['enrolledCourses'] = $db->table('enrollments')
            ->select('courses.id, courses.course_name as name, courses.description, enrollments.enrollment_date')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where("enrollments.{$enrollmentUserCol}", session()->get('userID'))
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
            $data['availableCourses'] = $db->table('courses')
                ->select('id, course_name as name, description')
                ->whereNotIn('id', $enrolledCourseIds)
                ->get()
                ->getResultArray();
        } else {
            $data['courseMaterials'] = [];
            $data['availableCourses'] = $db->table('courses')->select('id, course_name as name, description')->get()->getResultArray();
        }
    }
    $data['coursesList'] = $db->table('courses')->get()->getResultArray();

    return view('auth/dashboard', $data);
}

}