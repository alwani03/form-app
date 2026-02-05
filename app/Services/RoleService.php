<?php

namespace App\Services;

use App\Models\Role;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Role::query();
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Roles');

        if ($search) {
            $query->where('role', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Role', $search);
        }

        $this->logActivityService->log($remark);

        return $query->paginate($perPage);
    }

    public function create(array $data): Role
    {
        $role = Role::create([
            'role' => $data['role'],
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'created_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Role', $role->role)
        );

        return $role;
    }

    public function find(int $id): ?Role
    {
        $role = Role::find($id);

        if ($role) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Role', $role->role)
            );
        }

        return $role;
    }

    public function update(int $id, array $data): ?Role
    {
        $role = Role::find($id);

        if (!$role) {
            return null;
        }

        $role->update([
            'role' => $data['role'],
            'description' => $data['description'] ?? $role->description,
            'is_active' => $data['is_active'] ?? $role->is_active,
            'updated_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Role', $role->role)
        );

        return $role;
    }

    public function delete(int $id): bool
    {
        $role = Role::find($id);

        if (!$role) {
            return false;
        }

        $roleName = $role->role;
        
        $role->update(['deleted_by' => Auth::id()]);
        $role->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Role', $roleName)
        );

        return true;
    }
}
