<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Authorization check
        if (session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $db = \Config\Database::connect();
        $totalUsers = $db->table('users')->countAllResults();

        // Prepare data (example: list of admin users)
        $admins = $db->table('users')->where('role', 'admin')->get()->getResultArray();

        return view('admin/dashboard', [
            'admins' => $admins,
            'totalUsers' => $totalUsers,
        ]);
    }
}