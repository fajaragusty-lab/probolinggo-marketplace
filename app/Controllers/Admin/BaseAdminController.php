<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

abstract class BaseAdminController extends BaseController
{
    protected function guard(): void
    {
        $roles = session()->get('roles') ?? [];
        if (!in_array('super_admin', $roles, true) && !in_array('government_admin', $roles, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }
}
