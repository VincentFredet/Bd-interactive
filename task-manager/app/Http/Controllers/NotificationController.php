<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $query = auth()->user()->notifications()->latest();

            // Filter by read/unread
            if ($request->has('unread_only') && $request->unread_only) {
                $query->unread();
            }

            // Filter by type
            if ($request->has('type')) {
                $query->ofType($request->type);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $notifications = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'notifications' => $notifications->items(),
                'unread_count' => auth()->user()->notifications()->unread()->count(),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notifications', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications',
            ], 500);
        }
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        try {
            $count = auth()->user()->notifications()->unread()->count();

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching unread count', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du comptage des notifications',
            ], 500);
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        try {
            // Verify ownership
            if ($notification->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue',
                'notification' => $notification->fresh(),
                'unread_count' => auth()->user()->notifications()->unread()->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification',
            ], 500);
        }
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread(Notification $notification)
    {
        try {
            // Verify ownership
            if ($notification->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            $notification->markAsUnread();

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme non lue',
                'notification' => $notification->fresh(),
                'unread_count' => auth()->user()->notifications()->unread()->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as unread', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification',
            ], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            $updated = auth()->user()
                ->notifications()
                ->unread()
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "Toutes les notifications ont été marquées comme lues",
                'updated_count' => $updated,
                'unread_count' => 0,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking all notifications as read', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage des notifications',
            ], 500);
        }
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        try {
            // Verify ownership
            if ($notification->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé',
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée',
                'unread_count' => auth()->user()->notifications()->unread()->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la notification',
            ], 500);
        }
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead()
    {
        try {
            $deleted = auth()->user()
                ->notifications()
                ->read()
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifications lues supprimées',
                'deleted_count' => $deleted,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting read notifications', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression des notifications',
            ], 500);
        }
    }
}
