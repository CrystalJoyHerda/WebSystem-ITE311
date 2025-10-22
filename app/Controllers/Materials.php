<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    protected $helpers = ['form', 'url', 'filesystem'];

    // Upload function
    public function upload($course_id)
    {
        helper(['filesystem', 'form']);
        $materialModel = new \App\Models\MaterialModel();

        if ($this->request->getMethod() !== 'post') {
            return view('materials/uploads', ['course_id' => $course_id]);
        }

        $file = $this->request->getFile('material_file');
        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'Please choose a valid file.');
            return redirect()->back();
        }

        // Validation (size/type)
        $allowed = 'pdf|doc|docx|ppt|pptx|zip|rar|txt';
        if (!$file->isValid() || !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            if (!in_array($ext, explode('|', $allowed))) {
                session()->setFlashdata('error', 'Invalid file type.');
                return redirect()->back();
            }
        }

        $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials' . DIRECTORY_SEPARATOR . $course_id;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();
        if (!$file->move($uploadPath, $newName)) {
            session()->setFlashdata('error', 'Failed to move uploaded file.');
            return redirect()->back();
        }

        $relativePath = 'writable/uploads/materials/' . $course_id . '/' . $newName;
        $insertData = [
            'course_id'  => (int)$course_id,
            'file_name'  => $file->getClientName(),
            'file_path'  => $relativePath,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        log_message('debug', 'Upload input: ' . json_encode($this->request->getPost()));
        log_message('debug', 'File moved to: ' . $relativePath);

        $inserted = $materialModel->insert($insertData);
        if ($inserted) {
            session()->setFlashdata('success', 'Material uploaded.');
        } else {
            // rollback file
            @unlink($uploadPath . DIRECTORY_SEPARATOR . $newName);
            session()->setFlashdata('error', 'Failed to save material record.');
        }

        // Role-aware redirect: admins/teachers -> course page, students -> dashboard
        $session = session();
        $role = $session->get('role');
        if ($role === 'admin' || $role === 'teacher') {
            // Prefer redirecting back to the admin course edit/view page if exists
            return redirect()->to(base_url('admin/course/' . $course_id));
        }

        // default: student or unauthenticated (safe fallback)
        return redirect()->to(base_url('student/dashboard'));
    }

    // Delete material
    public function delete($id)
    {
        $model = new MaterialModel();
        $material = $model->find($id);

        if ($material) {
            $fullPath = $this->resolveMaterialPath($material['file_path']);
            if ($fullPath && file_exists($fullPath)) {
                @unlink($fullPath);
            } else {
                log_message('warning', 'Materials::delete - file not found for material id ' . $id . ' path:' . $material['file_path']);
            }
            $model->delete($id);
            return redirect()->back()->with('success', 'Material deleted successfully!');
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    // Download material
    public function download($id)
    {
        $model = new MaterialModel();
        $material = $model->find($id);

        // Check existence
        if (! $material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Require login
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in to download materials.');
        }

        $userId = $session->get('userID');
        $role = $session->get('role');

        $courseId = intval($material['course_id']);

        $db = \Config\Database::connect();

        $authorized = false;

        // Admins are allowed
        if ($role === 'admin') {
            $authorized = true;
        }

        // Teachers may download if they own the course
        if (! $authorized && $role === 'teacher') {
            $owns = (bool)$db->table('courses')
                ->where('id', $courseId)
                ->where('instructor_id', intval($userId))
                ->countAllResults();
            if ($owns) {
                $authorized = true;
            }
        }

        // Students must be enrolled in the course
        if (! $authorized && $role === 'student') {
            // detect enrollment user/student column dynamically (same strategy as Auth::dashboard)
            $fields = $db->getFieldData('enrollments');
            $fieldNames = array_map(function($f){ return $f->name; }, $fields);
            $candidates = ['user_id', 'student_id', 'userID', 'userid', 'studentid', 'studentID'];
            $enrollmentUserCol = null;
            foreach ($candidates as $cand) {
                foreach ($fieldNames as $fn) {
                    if (strcasecmp($fn, $cand) === 0) {
                        $enrollmentUserCol = $fn;
                        break 2;
                    }
                }
            }

            if ($enrollmentUserCol) {
                $isEnrolled = (bool)$db->table('enrollments')
                    ->where('course_id', $courseId)
                    ->where($enrollmentUserCol, intval($userId))
                    ->countAllResults();
                if ($isEnrolled) {
                    $authorized = true;
                }
            }
        }

        if (! $authorized) {
            return redirect()->back()->with('error', 'You are not authorized to download this material.');
        }

        // Serve the file securely
        $fullPath = $this->resolveMaterialPath($material['file_path']);
        if ($fullPath && file_exists($fullPath) && is_readable($fullPath)) {
            // Use response->download to force download and set original filename
            return $this->response->download($fullPath, null)
                                  ->setFileName($material['file_name']);
        }

        log_message('error', 'Materials::download - file not found or inaccessible for id ' . $id . ' tried path: ' . ($material['file_path'] ?? ''));

        return redirect()->back()->with('error', 'File not found or inaccessible.');
    }

    public function courseMaterials($course_id)
    {
        try {
            $sess = session();
            log_message('debug', 'Materials::courseMaterials called for course_id=' . intval($course_id) . ' session=' . json_encode($sess->get()) );

            if (! $sess->get('isLoggedIn')) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
            }

            $model = new \App\Models\MaterialModel();
            $materials = $model->where('course_id', (int)$course_id)->orderBy('created_at','DESC')->findAll();

            return $this->response->setJSON(['status' => 'success', 'materials' => $materials]);
        } catch (\Throwable $e) {
            log_message('error', 'Materials::courseMaterials exception: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Server error while fetching materials'])->setStatusCode(500);
        }
    }

    /**
     * Resolve stored material path to an existing filesystem path.
     * Tries several common locations (as-stored, FCPATH, WRITEPATH, ROOTPATH)
     * and returns the first existing path or false.
     *
     * @param string|null $storedPath
     * @return string|false
     */
    private function resolveMaterialPath(?string $storedPath)
    {
        if (empty($storedPath)) {
            return false;
        }

        $stored = ltrim($storedPath, '/\\');
        $candidates = [];

        // as given (could be absolute)
        $candidates[] = $storedPath;

        // public root
        $candidates[] = FCPATH . $stored;

        // writable path (may already include 'writable/')
        $candidates[] = WRITEPATH . $stored;
        $candidates[] = WRITEPATH . str_replace('writable' . DIRECTORY_SEPARATOR, '', $stored);
        $candidates[] = WRITEPATH . str_replace('writable/', '', $stored);

        // project root
        $candidates[] = ROOTPATH . $stored;

        // if stored contains 'writable/' prefix, also try ROOTPATH . 'writable/' . rest
        if (stripos($stored, 'writable' . DIRECTORY_SEPARATOR) === 0 || stripos($stored, 'writable/') === 0) {
            $without = preg_replace('#^writable[\\/]{1}#i', '', $stored);
            $candidates[] = WRITEPATH . $without;
            $candidates[] = ROOTPATH . 'writable' . DIRECTORY_SEPARATOR . $without;
        }

        foreach ($candidates as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }
}
