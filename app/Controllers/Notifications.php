<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get notifications for the logged-in user (AJAX endpoint)
     * Returns JSON with unread count and list of notifications
     */
    public function get()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('userID');
        
        // Get unread count
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        // Get latest notifications (limit 10)
        $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);

        return $this->response->setJSON([
            'status' => 'success',
            'unreadCount' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read (AJAX endpoint)
     * 
     * @param int $id Notification ID
     */
    public function mark_as_read($id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('userID');
        
        // Verify the notification belongs to the current user
        $notification = $this->notificationModel->find($id);
        
        if (!$notification) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Notification not found'
            ])->setStatusCode(404);
        }

        if ($notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        // Mark as read
        $success = $this->notificationModel->markAsRead($id);

        if ($success) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update notification'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark all notifications as read for the current user
     */
    public function mark_all_as_read()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = session()->get('userID');
        
        // Update all unread notifications for this user
        $success = $this->notificationModel->markAllAsRead($userId);

        if ($success !== false) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'All notifications marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to update notifications'
            ])->setStatusCode(500);
        }
    }
}