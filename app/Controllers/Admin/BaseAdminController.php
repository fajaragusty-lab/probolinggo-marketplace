<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

abstract class BaseAdminController extends BaseController
{
    protected function guard()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $roles = session()->get('roles') ?? [];
        if (!in_array('super_admin', $roles, true) && !in_array('government_admin', $roles, true)) {
            return redirect()->to('/')->with('error', 'Akses admin ditolak');
        }

        return null;
    }
}
