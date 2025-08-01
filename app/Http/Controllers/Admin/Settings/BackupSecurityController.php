<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;

class BackupSecurityController extends Controller
{
    public function edit()
    {
        return view('admin.settings.backup-security');
    }
}
