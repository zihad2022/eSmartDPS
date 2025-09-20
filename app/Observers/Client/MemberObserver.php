<?php

namespace App\Observers\Client;

use App\Models\Member;
use App\Services\ActivityLogger;

class MemberObserver
{
    public function created(Member $member)
    {
        ActivityLogger::log("Member '{$member->name}' was created.");
    }

    public function updated(Member $member)
    {
        ActivityLogger::log("Member '{$member->name}' was updated.");
    }

    public function deleted(Member $member)
    {
        ActivityLogger::log("Member '{$member->name}' was deleted.");
    }
}
