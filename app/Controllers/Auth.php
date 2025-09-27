<?php

namespace App\Controllers;
use CodeIgniter\Controller;

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

                session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');

                // ✅ Role-based redirection
                if ($user['role'] === 'admin') {
                    return redirect()->to(base_url('auth/dashboard'));
                } elseif ($user['role'] === 'teacher') {
                    return redirect()->to(base_url('auth/dashboard'));
                } elseif ($user['role'] === 'student') {
                    return redirect()->to(base_url('auth/dashboard'));
                } else {
                    // fallback in case of unknown role
                    return redirect()->to(base_url('dashboard'));
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
    if (!session()->get('isLoggedIn')) {
        return redirect()->to(base_url('login'));
    }

    $role = session()->get('role');
    $db = \Config\Database::connect();

    if ($role === 'admin') {
        $totalUsers = $db->table('users')->countAllResults();
        $totalCourses = $db->table('courses')->countAllResults();

        return view('auth/dashboard', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses
        ]);
    } elseif ($role === 'teacher') {
        // Example: get courses for teacher
        $courses = $db->table('courses')->where('teacher_id', session()->get('userID'))->get()->getResultArray();

        return view('auth/dashboard', [
            'courses' => $courses
        ]);
    } elseif ($role === 'student') {
        // Example: get enrolled courses for student
        $courses = $db->table('enrollments')
            ->select('courses.name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.student_id', session()->get('userID'))
            ->get()->getResultArray();

        return view('auth/dashboard', [
            'courses' => $courses
        ]);
    } else {
        return redirect()->to(base_url('login'));
    }
}
}