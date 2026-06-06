<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Student;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── OTP 2FA Routes ──
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/broadcast', [Admin\DashboardController::class, 'broadcast'])->name('dashboard.broadcast');
    Route::get('/search', [Admin\SearchController::class, 'index'])->name('search');
    Route::patch('/notifications/mark-all-read', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // ── AJAX API (Live Stats) ──
    Route::get('api/stats', [Admin\ApiController::class, 'stats'])->name('api.stats');
    Route::get('api/room-heatmap', [Admin\ApiController::class, 'roomHeatMap'])->name('api.roomHeatmap');

    // ── Live Chat ──
    Route::get('chat', [Admin\ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{student}/messages', [Admin\ChatController::class, 'messages'])->name('chat.messages');
    Route::post('chat/send', [Admin\ChatController::class, 'send'])->name('chat.send');
    Route::get('chat/unread-count', [Admin\ChatController::class, 'unreadCount'])->name('chat.unread');

    // ── Room Heat Map ──
    Route::get('room-heatmap', fn() => view('admin.room_heatmap'))->name('roomHeatmap.index');

    // ── Receipts ──
    Route::get('receipts/{receipt}', [Admin\ReceiptController::class, 'show'])->name('receipts.show');

    // Students
    Route::get('students', [Admin\StudentController::class, 'index'])->name('students.index');
    Route::post('students', [Admin\StudentController::class, 'store'])->name('students.store');
    Route::put('students/{id}', [Admin\StudentController::class, 'update'])->name('students.update');
    Route::delete('students/{id}', [Admin\StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('students/{id}/assign-room', [Admin\StudentController::class, 'assignRoom'])->name('students.assignRoom');

    // Rooms
    Route::get('rooms', [Admin\RoomController::class, 'index'])->name('rooms.index');
    Route::post('rooms', [Admin\RoomController::class, 'store'])->name('rooms.store');
    Route::put('rooms/{room}', [Admin\RoomController::class, 'update'])->name('rooms.update');
    Route::delete('rooms/{room}', [Admin\RoomController::class, 'destroy'])->name('rooms.destroy');

    // Complaints
    Route::get('complaints', [Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('complaints/{complaint}', [Admin\ComplaintController::class, 'show'])->name('complaints.show');
    Route::patch('complaints/{complaint}/status', [Admin\ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
    Route::post('complaints/{complaint}/reply', [Admin\ComplaintController::class, 'reply'])->name('complaints.reply');

    // Events
    Route::get('events', [Admin\EventController::class, 'index'])->name('events.index');
    Route::post('events', [Admin\EventController::class, 'store'])->name('events.store');
    Route::put('events/{event}', [Admin\EventController::class, 'update'])->name('events.update');
    Route::delete('events/{event}', [Admin\EventController::class, 'destroy'])->name('events.destroy');
    Route::get('events/{event}/registrations', [Admin\EventController::class, 'registrations'])->name('events.registrations');

    // Announcements
    Route::get('announcements', [Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('announcements', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('announcements/{announcement}', [Admin\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('announcements/{announcement}', [Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Leave Requests
    Route::get('leave-requests', [Admin\LeaveRequestController::class, 'index'])->name('leaveRequests.index');
    Route::patch('leave-requests/{id}/approve', [Admin\LeaveRequestController::class, 'approve'])->name('leaveRequests.approve');
    Route::patch('leave-requests/{id}/reject', [Admin\LeaveRequestController::class, 'reject'])->name('leaveRequests.reject');

    // Payments
    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::patch('payments/{id}/approve', [Admin\PaymentController::class, 'approve'])->name('payments.approve');
    Route::patch('payments/{id}/reject', [Admin\PaymentController::class, 'reject'])->name('payments.reject');

    // Mess Menu
    Route::get('mess-menu', [Admin\MessMenuController::class, 'index'])->name('messMenu.index');
    Route::put('mess-menu/{messMenu}', [Admin\MessMenuController::class, 'update'])->name('messMenu.update');

    // Visitor Passes
    Route::get('visitor-passes', [Admin\VisitorPassController::class, 'index'])->name('visitorPasses.index');
    Route::patch('visitor-passes/{id}/approve', [Admin\VisitorPassController::class, 'approve'])->name('visitorPasses.approve');
    Route::patch('visitor-passes/{id}/reject', [Admin\VisitorPassController::class, 'reject'])->name('visitorPasses.reject');

    // Reports & Export
    Route::get('reports', [Admin\ExportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/students', [Admin\ExportController::class, 'studentsCsv'])->name('reports.students');
    Route::get('reports/export/complaints', [Admin\ExportController::class, 'complaintsCsv'])->name('reports.complaints');
    Route::get('reports/export/fees', [Admin\ExportController::class, 'feesCsv'])->name('reports.fees');
    Route::get('reports/print', [Admin\ExportController::class, 'printReport'])->name('reports.print');

    // Mess Feedback
    Route::get('mess-feedback', [Admin\MessFeedbackController::class, 'index'])->name('messFeedback.index');

    // Maintenance
    Route::get('maintenance', [Admin\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('maintenance', [Admin\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::delete('maintenance/{maintenance}', [Admin\MaintenanceController::class, 'destroy'])->name('maintenance.destroy');

    // Notice Board
    Route::get('notice-board', [Admin\NoticeBoardController::class, 'index'])->name('noticeBoard.index');
    Route::patch('notice-board/{notice}/pin', [Admin\NoticeBoardController::class, 'togglePin'])->name('noticeBoard.pin');
    Route::delete('notice-board/{notice}', [Admin\NoticeBoardController::class, 'destroy'])->name('noticeBoard.destroy');

    // Roll Call
    Route::get('roll-call', [Admin\RollCallController::class, 'index'])->name('rollCall.index');
    Route::post('roll-call/start', [Admin\RollCallController::class, 'start'])->name('rollCall.start');
    Route::patch('roll-call/{session}/close', [Admin\RollCallController::class, 'close'])->name('rollCall.close');
    Route::post('roll-call/{session}/mark', [Admin\RollCallController::class, 'manualMark'])->name('rollCall.mark');

    // Hostel Fees
    Route::get('fees', [Admin\HostelFeeController::class, 'index'])->name('fees.index');
    Route::post('fees', [Admin\HostelFeeController::class, 'store'])->name('fees.store');
    Route::patch('fees/{fee}/paid', [Admin\HostelFeeController::class, 'markPaid'])->name('fees.paid');
    Route::delete('fees/{fee}', [Admin\HostelFeeController::class, 'destroy'])->name('fees.destroy');

    // Profile
    Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/photo', [Admin\ProfileController::class, 'updatePhoto'])->name('profile.photo');
});

// Student routes
Route::middleware('auth')->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');

    // ── Live Chat ──
    Route::get('chat', [Student\ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{admin}/messages', [Student\ChatController::class, 'messages'])->name('chat.messages');
    Route::post('chat/send', [Student\ChatController::class, 'send'])->name('chat.send');
    Route::get('chat/unread-count', [Student\ChatController::class, 'unreadCount'])->name('chat.unread');

    // ── Payment Gateway ──
    Route::get('payment/{fee}', [Student\PaymentController::class, 'create'])->name('payment.create');
    Route::post('payment', [Student\PaymentController::class, 'store'])->name('payment.store');
    Route::get('receipt/{receipt}', [Student\PaymentController::class, 'receipt'])->name('receipt.show');

    // Complaints
    Route::get('complaints', [Student\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('complaints/create', [Student\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('complaints', [Student\ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('complaints/{complaint}', [Student\ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('complaints/{complaint}/message', [Student\ComplaintController::class, 'addMessage'])->name('complaints.message');

    // Leave Requests
    Route::get('leave-requests', [Student\LeaveRequestController::class, 'index'])->name('leaveRequests.index');
    Route::get('leave-requests/create', [Student\LeaveRequestController::class, 'create'])->name('leaveRequests.create');
    Route::post('leave-requests', [Student\LeaveRequestController::class, 'store'])->name('leaveRequests.store');

    // Events
    Route::get('events', [Student\EventController::class, 'index'])->name('events.index');
    Route::post('events/{event}/register', [Student\EventController::class, 'register'])->name('events.register');
    Route::delete('events/{event}/cancel', [Student\EventController::class, 'cancel'])->name('events.cancel');

    // Announcements
    Route::get('announcements', [Student\AnnouncementController::class, 'index'])->name('announcements.index');

    // Mess Menu
    Route::get('mess-menu', [Student\MessMenuController::class, 'index'])->name('messMenu.index');

    // Visitor Passes
    Route::get('visitor-passes', [Student\VisitorPassController::class, 'index'])->name('visitorPasses.index');
    Route::get('visitor-passes/create', [Student\VisitorPassController::class, 'create'])->name('visitorPasses.create');
    Route::post('visitor-passes', [Student\VisitorPassController::class, 'store'])->name('visitorPasses.store');

    Route::post('mess-feedback', [Student\MessFeedbackController::class, 'store'])->name('messFeedback.store');
    Route::get('maintenance', [Student\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('notice-board', [Student\NoticeBoardController::class, 'index'])->name('noticeBoard.index');
    Route::post('notice-board', [Student\NoticeBoardController::class, 'store'])->name('noticeBoard.store');
    Route::post('notice-board/{notice}/comment', [Student\NoticeBoardController::class, 'comment'])->name('noticeBoard.comment');

    Route::get('attendance', [Student\RollCallController::class, 'index'])->name('attendance.index');
    Route::post('attendance/check-in', [Student\RollCallController::class, 'checkInActive'])->name('attendance.checkIn');
    Route::get('attendance/scan/{token}', [Student\RollCallController::class, 'checkIn'])->name('attendance.scan');

    Route::get('fees', [Student\HostelFeeController::class, 'index'])->name('fees.index');

    // Notifications
    Route::get('notifications', [Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/read', [Student\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::patch('notifications/mark-all-read', [Student\NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Profile
    Route::get('profile', [Student\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [Student\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/password', [Student\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/photo', [Student\ProfileController::class, 'updatePhoto'])->name('profile.photo');
});
