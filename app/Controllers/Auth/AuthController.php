<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model(UserModel::class);
        $user = $userModel->findByEmail($this->request->getPost('email'));

        if (!$user || !(int) $user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }
        if (!password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        $roles = array_column($userModel->getRoles($user['id']), 'slug');
        session()->regenerate();
        session()->set([
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'roles'      => $roles,
        ]);
        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        $redirect = '/';
        if (in_array('umkm', $roles, true)) {
            $redirect = '/umkm/dashboard';
        } elseif (in_array('courier', $roles, true)) {
            $redirect = '/courier/dashboard';
        } elseif (in_array('government_admin', $roles, true) || in_array('super_admin', $roles, true)) {
            $redirect = '/admin/dashboard';
        }

        return redirect()->to($redirect)->with('success', 'Login berhasil');
    }

    public function registerForm()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }
        return view('auth/register');
    }

    public function register()
    {
        $rules = [
            'name'                  => 'required|min_length[3]|max_length[150]',
            'email'                 => 'required|valid_email|is_unique[users.email]',
            'phone'                 => 'permit_empty|min_length[10]|max_length[20]',
            'password'              => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = model(UserModel::class);
        $userId = $userModel->insert([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'phone'     => $this->request->getPost('phone'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftar');
        }

        $userModel->assignRole($userId, 'customer');
        session()->regenerate();
        session()->set([
            'user_id'    => $userId,
            'user_name'  => $this->request->getPost('name'),
            'user_email' => $this->request->getPost('email'),
            'roles'      => ['customer'],
        ]);

        return redirect()->to('/')->with('success', 'Registrasi berhasil');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout');
    }
}
