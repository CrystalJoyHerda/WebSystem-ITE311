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
        $session = session();
        $role = $session->get('role');
        $userId = $session->get('userID');

        // Authorization: admin OR teacher assigned to this course
        if ($role !== 'admin') {
            if ($role !== 'teacher') {
                return redirect()->back()->with('error', 'Not authorized.');
            }
            // teacher -> verify ownership
            $db = \Config\Database::connect();
            $owns = (bool)$db->table('courses')
                ->where('id', intval($course_id))
                ->where('instructor_id', intval($userId))
                ->countAllResults();
            if (! $owns) {
                return redirect()->back()->with('error', 'Not authorized to manage this course.');
            }
        }

        // Use fully-qualified name to avoid "class not found" issues
        $model = new \App\Models\MaterialModel();

        if ($this->request->getMethod() !== 'post') {
            // your view file is "uploads.php" — load it by folder/name (without .php)
            return view('materials/uploads', ['course_id' => $course_id]);
        }

        // Validate file
        $rules = [
            'material_file' => [
                'rules' => 'uploaded[material_file]|max_size[material_file,5120]|ext_in[material_file,pdf,doc,docx,ppt,pptx,zip,rar,txt]',
                'errors' => [
                    'uploaded' => 'Please choose a file to upload.',
                    'max_size' => 'File size must be under 5MB.',
                    'ext_in'   => 'Allowed file types: pdf, doc, docx, ppt, pptx, zip, rar, txt.'
                ]
            ]
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', $this->validator->listErrors())->withInput();
        }

        $file = $this->request->getFile('material_file');

        if (! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'Uploaded file is not valid.');
        }

        // Prepare upload directory (per-course)
        $relativeDir = 'uploads/materials/' . intval($course_id) . '/';
        $uploadPath = FCPATH . $relativeDir;

        if (! is_dir($uploadPath)) {
            if (! mkdir($uploadPath, 0755, true) && ! is_dir($uploadPath)) {
                return redirect()->back()->with('error', 'Failed to create upload directory.');
            }
        }

        $newName = $file->getRandomName();

        if (! $file->move($uploadPath, $newName)) {
            return redirect()->back()->with('error', 'Failed to move uploaded file.');
        }

        $data = [
            'course_id' => $course_id,
            'file_name' => $file->getClientName(),
            'file_path' => $relativeDir . $newName, // store relative path
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->insertMaterial($data)) {
            return redirect()->back()->with('success', 'Material uploaded successfully!');
        } else {
            // rollback file on DB failure
            @unlink($uploadPath . $newName);
            return redirect()->back()->with('error', 'Failed to save material to database.');
        }
    }

    // Delete material
    public function delete($id)
    {
        $model = new MaterialModel();
        $material = $model->find($id);

        if ($material) {
            $fullPath = FCPATH . $material['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
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

        if ($material) {
            $fullPath = FCPATH . $material['file_path'];
            if (file_exists($fullPath)) {
                return $this->response->download($fullPath, null)
                                      ->setFileName($material['file_name']);
            }
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}
