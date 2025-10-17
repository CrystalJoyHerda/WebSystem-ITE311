<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = session()->get('role') ?? null;
        $path = '/' . ltrim($request->getUri()->getPath(), '/');

        // admin only
        if (str_starts_with($path, '/admin') && $role !== 'admin') {
            return redirect()->to(site_url('announcements'))->with('error', 'Access Denied: Insufficient Permissions');
        }

        // teacher only
        if (str_starts_with($path, '/teacher') && $role !== 'teacher') {
            return redirect()->to(site_url('announcements'))->with('error', 'Access Denied: Insufficient Permissions');
        }

        // announcements only for students
        if (($path === '/announcement' || $path === '/announcements') && $role !== 'student') {
            return redirect()->to(site_url('announcements'))->with('error', 'Access Denied: Insufficient Permissions');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}