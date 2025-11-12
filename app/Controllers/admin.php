<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Keep existing admin dashboard routing if used elsewhere
        return view('admin_dashboard');
    }

    /**
     * Add user (used by Add User form). Simple server-side create and redirect.
     */
    public function addUser()
    {
        if (! $this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can add users
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Unauthorized');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');
        $password = $this->request->getPost('password');

        // Basic validation
        if (! $name || ! $email || ! $role || ! $password) {
            session()->setFlashdata('error', 'Please fill all required fields.');
            return redirect()->back();
        }

        // Server-side character validation to match client rules
        // Name: letters, numbers and spaces only
        if (! preg_match('/^[A-Za-z0-9 ]+$/', $name)) {
            session()->setFlashdata('error', 'Name may only contain letters, numbers and spaces.');
            return redirect()->back();
        }

        // Email: only letters, numbers, @ and . allowed (and must be a valid email format)
        if (! preg_match('/^[A-Za-z0-9@.]+$/', $email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('error', 'Invalid email format: only letters, numbers, @, and . are allowed.');
            return redirect()->back();
        }

        // Password length
        if (strlen($password) < 6) {
            session()->setFlashdata('error', 'Password must be at least 6 characters.');
            return redirect()->back();
        }

        // prevent duplicate email
        $exists = $builder->where('email', $email)->get()->getRowArray();
        if ($exists) {
            session()->setFlashdata('error', 'Email already exists.');
            return redirect()->back();
        }

        $insert = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($builder->insert($insert)) {
            session()->setFlashdata('success', 'User added successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to add user.');
        }

        return redirect()->back();
    }

    /**
     * Update user via AJAX (returns JSON)
     */
    public function updateUser()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        // Only admins may update user data
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $id = intval($this->request->getPost('id'));
    $name = $this->request->getPost('name');
    $email = $this->request->getPost('email');
    $role = $this->request->getPost('role');
    $password = $this->request->getPost('password'); // optional

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('id', $id)->get()->getRowArray();
        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setHeader(csrf_header(), csrf_hash());
        }

        // update
        $updateData = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // If password supplied, hash and include it
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // protect admin email uniqueness
        $other = $builder->where('email', $email)->where('id !=', $id)->get()->getRowArray();
        if ($other) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email already in use by another account.']);
        }

        $builder->where('id', $id)->update($updateData);

        // return updated user data
        $updated = $builder->where('id', $id)->get()->getRowArray();

        return $this->response->setJSON(['status' => 'success', 'message' => 'User updated', 'user' => $updated])->setHeader(csrf_header(), csrf_hash());
    }

    /**
     * Delete user via AJAX - only allow delete if user's role is teacher or student.
     */
    public function deleteUser()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        // Only admins may delete users
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $id = intval($this->request->getPost('id'));
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('id', $id)->get()->getRowArray();
        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User not found'])->setHeader(csrf_header(), csrf_hash());
        }

        if (isset($user['role']) && $user['role'] === 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You cannot delete another admin account.'])->setHeader(csrf_header(), csrf_hash());
        }

        // TODO: if foreign keys exist (courses.instructor_id), deletion may fail; handle accordingly
        try {
            $builder->where('id', $id)->delete();
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Delete failed: ' . $e->getMessage()])->setHeader(csrf_header(), csrf_hash());
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted'])->setHeader(csrf_header(), csrf_hash());
    }
}
