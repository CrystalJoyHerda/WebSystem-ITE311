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

    } elseif ($role === 'teacher') {
        // Make sure the column name matches your DB (e.g., 'instructor_id' or 'teacher_id')
        $data['courses'] = $db->table('courses')
            ->where('instructor_id', session()->get('userID'))
            ->get()
            ->getResultArray();

    } elseif ($role === 'student') {
        // Make sure the join and select match your DB structure
        $data['courses'] = $db->table('enrollments')
            ->select('course_name')
            ->join('courses', 'course_id = enrollments.course_id')
            ->where('enrollments.student_id', session()->get('userID'))
            ->get()
            ->getResultArray();
    }
    $data['coursesList'] = $db->table('courses')->get()->getResultArray();

    return view('auth/dashboard', $data);
}

}