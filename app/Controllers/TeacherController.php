<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class TeacherController extends Controller
{
    public function dashboard()
    {
        // Authorization check
        if (session()->get('role') !== 'teacher') {
            return redirect()->to(base_url('login'));
        }

        // Prepare data (example: list of courses taught by this teacher)
        $db = \Config\Database::connect();
        $courses = $db->table('courses')->where('instructor_id', session()->get('userID'))->get()->getResultArray();

        return view('teacher/dashboard', ['courses' => $courses]);
    }
}