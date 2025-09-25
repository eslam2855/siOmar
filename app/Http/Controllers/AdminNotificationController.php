<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of notifications
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = Notification::withCount('userNotifications');

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'scheduled') {
                $query->whereNotNull('scheduled_at')->where('scheduled_at', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:reservation,payment,system,legal,maintenance,security',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'category' => 'required|string|in:general,reservation,payment,system,legal,maintenance',
            'is_global' => 'boolean',
            'target_roles' => 'nullable|array',
            'target_users' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Convert target arrays to JSON
        if ($request->has('target_roles')) {
            $data['target_roles'] = array_filter($request->target_roles);
        }
        
        if ($request->has('target_users')) {
            $data['target_users'] = array_filter($request->target_users);
        }

        $notification = $this->notificationService->createAndSend($data);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created and sent successfully.');
    }

    /**
     * Display the specified notification
     */
    public function show(Request $request, Notification $notification)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $notification->load(['userNotifications.user']);

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified notification
     */
    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
    }

    /**
     * Update the specified notification
     */
    public function update(Request $request, Notification $notification)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'category' => 'required|string|in:general,reservation,payment,system,legal,maintenance',
            'is_active' => 'boolean',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification->update($request->only([
            'title',
            'message',
            'priority',
            'category',
            'is_active',
            'scheduled_at',
            'expires_at',
        ]));

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Toggle notification active status
     */
    public function toggle(Request $request, Notification $notification)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $notification->update(['is_active' => !$notification->is_active]);

        $status = $notification->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Notification {$status} successfully.");
    }

    /**
     * Get notification statistics
     */
    public function statistics(Request $request)
    {
        // Check if user is admin
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $stats = [
            'total_notifications' => Notification::count(),
            'active_notifications' => Notification::active()->count(),
            'scheduled_notifications' => Notification::active()->scheduled()->count(),
            'expired_notifications' => Notification::where('expires_at', '<', now())->count(),
            'total_user_notifications' => UserNotification::count(),
            'unread_user_notifications' => UserNotification::unread()->count(),
            'sent_notifications' => UserNotification::sent()->count(),
            'delivered_notifications' => UserNotification::delivered()->count(),
            'notifications_by_type' => Notification::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'notifications_by_priority' => Notification::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->get(),
        ];

        return view('admin.notifications.statistics', compact('stats'));
    }
}