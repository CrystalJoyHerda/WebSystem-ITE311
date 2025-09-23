<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class StudentController extends Controller
{
    public function dashboard()
    {
        // Authorization check
        if (session()->get('role') !== 'student') {
            return redirect()->to(base_url('login'));
        }

        // Prepare data (example: list of courses for this student)
        $db = \Config\Database::connect();
        $courses = $db->table('enrollments')
            ->where('student_id', session()->get('userID'))
            ->get()->getResultArray();

        return view('student/dashboard', ['courses' => $courses]);
    }
}