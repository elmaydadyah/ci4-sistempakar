<?php

namespace App\Filters;

use App\Libraries\RoleAccess;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminRoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = $this->normalizeRole((string) (session()->get('role') ?? 'admin1'));
        session()->set('role', $role);

        if ($this->isAllowed($role, trim($request->getUri()->getPath(), '/'))) {
            return null;
        }

        return redirect()->to('/dashboard')->with('error', 'Role admin Anda tidak memiliki akses ke menu atau aksi tersebut.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function isAllowed(string $role, string $path): bool
    {
        return (new RoleAccess())->isPathAllowed($role, $path);
    }

    private function normalizeRole(string $role): string
    {
        return (new RoleAccess())->normalizeRole($role);
    }
}
