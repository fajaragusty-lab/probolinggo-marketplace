<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'name', 'email', 'phone', 'password', 'avatar', 'is_active',
        'email_verified_at', 'last_login_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function getRoles(int $userId): array
    {
        return $this->db->table('user_roles ur')
            ->select('r.name, r.slug')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->get()->getResultArray();
    }

    public function hasRole(int $userId, string $roleSlug): bool
    {
        return $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.slug', $roleSlug)
            ->countAllResults() > 0;
    }

    public function assignRole(int $userId, string $roleSlug): bool
    {
        $role = $this->db->table('roles')->where('slug', $roleSlug)->get()->getRowArray();
        if (!$role) {
            return false;
        }
        $exists = $this->db->table('user_roles')
            ->where(['user_id' => $userId, 'role_id' => $role['id']])
            ->countAllResults();
        if ($exists) {
            return true;
        }
        return (bool) $this->db->table('user_roles')->insert([
            'user_id'    => $userId,
            'role_id'    => $role['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
