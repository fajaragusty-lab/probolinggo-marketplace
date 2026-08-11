<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('user_id')) {
            if ($request->isAJAX()) {
                return service('response')->setJSON([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu',
                ])->setStatusCode(401);
            }
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if ($arguments) {
            $roles = is_array($arguments) ? $arguments : [$arguments];
            $userRoles = $session->get('roles') ?? [];
            $has = false;
            foreach ($roles as $r) {
                if (in_array($r, $userRoles, true)) {
                    $has = true;
                    break;
                }
            }
            if (!$has) {
                if ($request->isAJAX()) {
                    return service('response')->setJSON([
                        'success' => false,
                        'message' => 'Akses ditolak',
                    ])->setStatusCode(403);
                }
                return redirect()->to('/')->with('error', 'Akses ditolak');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
