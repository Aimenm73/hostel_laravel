<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Log an activity to the activity_logs table.
     *
     * @param string      $action      Short action name (e.g. 'student_created', 'complaint_resolved')
     * @param string|null $description Human-readable description of what happened
     * @param string|null $icon        FontAwesome icon class (e.g. 'fa-user-plus')
     * @param string|null $color       CSS color or class name (e.g. '#4f46e5', 'success')
     * @param int|null    $userId      User who performed the action; defaults to the authenticated user
     * @return ActivityLog
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?string $icon = null,
        ?string $color = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => $action,
            'description' => $description,
            'icon'        => $icon,
            'color'       => $color,
        ]);
    }
}
