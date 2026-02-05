<?php

namespace App\Services;

use App\Models\LogActivity;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;

class LogActivityService
{
    public function generateRemark(ActivityType $type, string $module, ?string $details = null): string
    {
        $remark = "{$type->value} {$module}";
        if ($details) {
            $remark .= ": {$details}";
        }
        return $remark;
    }

    /**
     * Create a new activity log.
     *
     * @param string $remark
     * @param int|null $userId
     * @return \App\Models\LogActivity
     */
    public function log(string $remark, ?int $userId = null)
    {
        return LogActivity::create([
            'user_id' => $userId ?? Auth::id(),
            'remark' => $remark,
        ]);
    }
}
