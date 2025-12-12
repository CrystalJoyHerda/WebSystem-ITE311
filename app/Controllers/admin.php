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
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->createStatusColumnIfMissing();
        } catch (\Exception $e) {
            // ignore: will surface on insert if critical
        }

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

        // return updated user data (ensure status returned)
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

        // Soft-delete: set status = 'inactive' instead of removing row
        try {
            // Ensure status column exists (attempt to create if missing)
            $this->ensureStatusColumnExists();
            $builder->where('id', $id)->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            // Try once more to create the column and retry
            try {
                $this->createStatusColumnIfMissing();
                $builder->where('id', $id)->update(['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')]);
            } catch (\Exception $e2) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Deactivate failed: ' . $e2->getMessage()])->setHeader(csrf_header(), csrf_hash());
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'User deactivated'])->setHeader(csrf_header(), csrf_hash());
    }

    /**
     * Activate a previously deactivated user (used by About Users page)
     */
    public function activateUser()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

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

        try {
            $this->ensureStatusColumnExists();
            $builder->where('id', $id)->update(['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            try {
                $this->createStatusColumnIfMissing();
                $builder->where('id', $id)->update(['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')]);
            } catch (\Exception $e2) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Activate failed: ' . $e2->getMessage()])->setHeader(csrf_header(), csrf_hash());
            }
        }

        $updated = $builder->where('id', $id)->get()->getRowArray();
        return $this->response->setJSON(['status' => 'success', 'message' => 'User activated', 'user' => $updated])->setHeader(csrf_header(), csrf_hash());
    }

    /**
     * Show inactive users (About Users page)
     */
    public function aboutUsers()
    {
        // Only admins
        if (! session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to(base_url('login'));
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // If the status column is missing, there are no inactive users yet.
        try {
            $this->ensureStatusColumnExists();
            $inactive = $builder->where('status', 'inactive')->get()->getResultArray();
        } catch (\Exception $e) {
            // If we can't ensure the column, return empty list and inform via flashdata
            $inactive = [];
            session()->setFlashdata('error', 'Status column missing. Please run migrations.');
        }

        return view('auth/about_users_clean', ['inactiveUsers' => $inactive]);
    }

    /**
     * Ensure the `status` column exists on `users` table. Throws on error.
     */
    private function ensureStatusColumnExists()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('users');
        if (! in_array('status', $fields)) {
            throw new \RuntimeException('status column missing');
        }
        return true;
    }

    /**
     * Attempt to create the `status` column if it's missing. Uses Forge.
     */
    private function createStatusColumnIfMissing()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('users');
        if (in_array('status', $fields)) return true;

        $forge = \Config\Database::forge();
        $col = [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'active'
            ]
        ];
        $forge->addColumn('users', $col);
        return true;
    }

    /**
     * Create a new course
     */
    public function createCourse()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can create courses
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();

        // Check if units column exists
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('courses');
        $hasUnits = in_array('units', $fields);

        $data = [
            'subject_code' => $this->request->getPost('course_code'),
            'subject_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'semester' => $this->request->getPost('semester'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Add units only if column exists
        if ($hasUnits) {
            $data['units'] = $this->request->getPost('units');
        }

        // Validation
        if (empty($data['subject_code']) || empty($data['subject_name'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course code and course name are required.'
            ]);
        }

        try {
            $courseId = $courseModel->insert($data);
            if ($courseId) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Course created successfully.',
                    'course_id' => $courseId
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to create course: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create course: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to create course.']);
    }

    /**
     * Assign teacher to a course
     */
    public function assignTeacher()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can assign teachers
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        $courseId = $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');

        if (empty($courseId) || empty($teacherId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course ID and Teacher ID are required.'
            ]);
        }

        try {
            // Update course with teacher assignment
            $updated = $courseModel->update($courseId, [
                'instructor_id' => $teacherId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updated) {
                // Get course details for notification
                $course = $courseModel->find($courseId);
                $courseName = $course['subject_name'] ?? $course['course_name'] ?? 'a course';

                // Create notification for teacher
                $notificationModel->insert([
                    'user_id' => $teacherId,
                    'type' => 'course_assignment',
                    'message' => 'You have been assigned to teach: ' . $courseName,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Teacher assigned successfully and notified.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to assign teacher: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to assign teacher.']);
    }

    /**
     * Update course details (AJAX)
     */
    public function updateCourse()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can update courses
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new \App\Models\CourseModel();

        $courseId = $this->request->getPost('course_id');
        $courseCode = $this->request->getPost('course_code');
        $courseName = $this->request->getPost('course_name');
        $semester = $this->request->getPost('semester');
        $units = $this->request->getPost('units');
        $description = $this->request->getPost('description');
        $instructorId = $this->request->getPost('instructor_id');

        // Validation
        if (empty($courseId) || empty($courseCode) || empty($courseName) || empty($semester)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course ID, code, name, and semester are required.'
            ]);
        }

        try {
            // Check if units column exists
            $db = \Config\Database::connect();
            $fields = $db->getFieldNames('courses');
            $hasUnits = in_array('units', $fields);

            $data = [
                'subject_code' => $courseCode,
                'subject_name' => $courseName,
                'semester' => $semester,
                'description' => $description,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Add units only if column exists
            if ($hasUnits) {
                $data['units'] = $units;
            }

            // Add instructor_id if provided
            if (!empty($instructorId)) {
                $data['instructor_id'] = $instructorId;
            } else {
                $data['instructor_id'] = null;
            }

            $updated = $courseModel->update($courseId, $data);

            if ($updated) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Course updated successfully.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to update course: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update course: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update course.']);
    }

    /**
     * Get all courses (AJAX)
     */
    public function getCourses()
    {
        // Only admins can view all courses
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if units column exists
            $fields = $db->getFieldNames('courses');
            $hasUnits = in_array('units', $fields);
            
            // Build query based on column availability
            if ($hasUnits) {
                $courses = $db->table('courses')
                    ->select('courses.id, courses.subject_code as course_code, courses.subject_name as course_name, courses.description, courses.units, courses.semester, courses.instructor_id, users.name as teacher_name, courses.created_at')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->orderBy('courses.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
            } else {
                $courses = $db->table('courses')
                    ->select('courses.id, courses.subject_code as course_code, courses.subject_name as course_name, courses.description, courses.semester, courses.instructor_id, users.name as teacher_name, courses.created_at')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->orderBy('courses.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get courses: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load courses: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all teachers (for dropdown)
     */
    public function getTeachers()
    {
        // Only admins can view teachers list
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $teachers = $db->table('users')
            ->select('id, name, email')
            ->where('role', 'teacher')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'teachers' => $teachers
        ]);
    }

    /**
     * Get all students (AJAX)
     */
    public function getStudents()
    {
        // Only admins can view students list
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $students = $db->table('users')
            ->select('id, name, email')
            ->where('role', 'student')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'students' => $students
        ]);
    }

    /**
     * Auto-enroll student into course (immediate enrollment)
     * Creates student if email doesn't exist
     */
    public function autoEnroll()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can enroll students
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $notificationModel = new \App\Models\NotificationModel();
        $courseModel = new \App\Models\CourseModel();
        $db = \Config\Database::connect();

        $studentName = $this->request->getPost('student_name');
        $studentEmail = $this->request->getPost('student_email');
        $studentPassword = $this->request->getPost('student_password');
        $courseId = $this->request->getPost('course_id');

        // Server-side validation
        if (empty($studentName) || empty($studentEmail) || empty($studentPassword) || empty($courseId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }

        // Validate name (letters and spaces only)
        if (!preg_match('/^[A-Za-z\s]+$/', $studentName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Name can only contain letters and spaces.'
            ]);
        }

        // Validate email format
        if (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid email format.'
            ]);
        }

        // Validate password length
        if (strlen($studentPassword) < 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password must be at least 6 characters.'
            ]);
        }

        try {
            // Check if user exists
            $existingUser = $db->table('users')
                ->where('email', $studentEmail)
                ->get()
                ->getRowArray();

            if ($existingUser) {
                $studentId = $existingUser['id'];
                $userCreated = false;
            } else {
                // Create new student user
                $userData = [
                    'name' => $studentName,
                    'email' => $studentEmail,
                    'password' => password_hash($studentPassword, PASSWORD_DEFAULT),
                    'role' => 'student',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $studentId = $db->table('users')->insert($userData);
                if (!$studentId) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Failed to create student account.'
                    ]);
                }
                $studentId = $db->insertID();
                $userCreated = true;
            }

            // Check if already enrolled or assigned
            $existing = $enrollmentModel->where([
                'user_id' => $studentId,
                'course_id' => $courseId
            ])->first();

            if ($existing) {
                $statusMsg = $existing['status'] === 'enrolled' ? 'enrolled in' : 'assigned to';
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Student is already ' . $statusMsg . ' this course.'
                ]);
            }

            // Auto-enroll with status 'enrolled'
            $enrollmentModel->insert([
                'user_id' => $studentId,
                'course_id' => $courseId,
                'status' => 'enrolled',
                'enrollment_date' => date('Y-m-d H:i:s'),
                'approved_by' => session()->get('id') // Admin who enrolled
            ]);

            // Get course name for notification
            $course = $courseModel->find($courseId);
            $courseName = $course['subject_name'] ?? $course['course_name'] ?? 'a course';

            // Notify student
            $notificationModel->insert([
                'user_id' => $studentId,
                'type' => 'enrollment',
                'message' => 'You have been enrolled in: ' . $courseName,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $message = $userCreated 
                ? 'Student account created and auto-enrolled successfully.' 
                : 'Existing student auto-enrolled successfully.';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Failed to auto-enroll: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to enroll student: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Assign course to student (student must request enrollment)
     * Creates student if email doesn't exist
     */
    public function assignCourse()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        // Only admins can assign courses
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $notificationModel = new \App\Models\NotificationModel();
        $courseModel = new \App\Models\CourseModel();
        $db = \Config\Database::connect();

        $studentName = $this->request->getPost('student_name');
        $studentEmail = $this->request->getPost('student_email');
        $studentPassword = $this->request->getPost('student_password');
        $courseId = $this->request->getPost('course_id');

        // Server-side validation
        if (empty($studentName) || empty($studentEmail) || empty($studentPassword) || empty($courseId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'All fields are required.'
            ]);
        }

        // Validate name (letters and spaces only)
        if (!preg_match('/^[A-Za-z\s]+$/', $studentName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Name can only contain letters and spaces.'
            ]);
        }

        // Validate email format
        if (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid email format.'
            ]);
        }

        // Validate password length
        if (strlen($studentPassword) < 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Password must be at least 6 characters.'
            ]);
        }

        try {
            // Check if user exists
            $existingUser = $db->table('users')
                ->where('email', $studentEmail)
                ->get()
                ->getRowArray();

            if ($existingUser) {
                $studentId = $existingUser['id'];
                $userCreated = false;
            } else {
                // Create new student user
                $userData = [
                    'name' => $studentName,
                    'email' => $studentEmail,
                    'password' => password_hash($studentPassword, PASSWORD_DEFAULT),
                    'role' => 'student',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $studentId = $db->table('users')->insert($userData);
                if (!$studentId) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Failed to create student account.'
                    ]);
                }
                $studentId = $db->insertID();
                $userCreated = true;
            }

            // Check if already enrolled or assigned
            $existing = $enrollmentModel->where([
                'user_id' => $studentId,
                'course_id' => $courseId
            ])->first();

            if ($existing) {
                $statusMsg = $existing['status'] === 'enrolled' ? 'enrolled in' : 'assigned to';
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Student is already ' . $statusMsg . ' this course.'
                ]);
            }

            // Assign with status 'assigned'
            $enrollmentModel->insert([
                'user_id' => $studentId,
                'course_id' => $courseId,
                'status' => 'assigned',
                'enrollment_date' => null // Not enrolled yet
            ]);

            // Get course name for notification
            $course = $courseModel->find($courseId);
            $courseName = $course['subject_name'] ?? $course['course_name'] ?? 'a course';

            // Notify student
            $notificationModel->insert([
                'user_id' => $studentId,
                'type' => 'course_assignment',
                'message' => 'A new course is available for enrollment: ' . $courseName,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $message = $userCreated 
                ? 'Student account created and course assigned successfully.' 
                : 'Course assigned to existing student successfully.';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Failed to assign course: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to assign course: ' . $e->getMessage()
            ]);
        }
    }
}
