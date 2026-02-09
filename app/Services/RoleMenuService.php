<?php

namespace App\Services;

use App\Models\RoleMenu;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleMenuService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, ?int $roleId = null, int $perPage = 10, bool $skipLog = false): LengthAwarePaginator
    {
        $query = RoleMenu::query()->with(['role', 'menu']);
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Role Menus');

        if ($search) {
            $query->whereHas('role', function ($q) use ($search) {
                $q->where('role', 'like', '%' . $search . '%');
            })->orWhereHas('menu', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Role Menu', $search);
        }

        if ($roleId) {
            $query->where('role_id', $roleId);
        }

        if (!$skipLog) {
            $this->logActivityService->log($remark);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): RoleMenu
    {
        $roleMenu = RoleMenu::withTrashed()
            ->where('role_id', $data['role_id'])
            ->where('menu_id', $data['menu_id'])
            ->first();

        if ($roleMenu) {
            if ($roleMenu->trashed()) {
                $roleMenu->restore();
            }

            $roleMenu->update([
                'is_active'   => $data['is_active'] ?? 1,
                'updated_by'  => Auth::id(),
            ]);

            $activityType = ActivityType::UPDATE;
        } else {
            $roleMenu = RoleMenu::create([
                'role_id'     => $data['role_id'],
                'menu_id'     => $data['menu_id'],
                'is_active'   => $data['is_active'] ?? 1,
                'created_by'  => Auth::id(),
            ]);

            $activityType = ActivityType::CREATE;
        }

        $roleMenu->load(['role', 'menu']);
        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        $this->logActivityService->log(
            $this->logActivityService->generateRemark($activityType, 'Role Menu', $details)
        );

        return $roleMenu;
    }

    public function find(int $id, bool $skipLog = false): ?RoleMenu
    {
        $roleMenu = RoleMenu::with(['role', 'menu'])->find($id);

        if ($roleMenu && !$skipLog) {
            $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Role Menu', $details)
            );
        }

        return $roleMenu;
    }

    public function update(int $id, array $data): ?RoleMenu
    {
        $roleMenu = RoleMenu::find($id);

        if (!$roleMenu) {
            return null;
        }

        $roleMenu->update([
            'role_id' => $data['role_id'],
            'menu_id' => $data['menu_id'],
            'is_active' => $data['is_active'] ?? $roleMenu->is_active,
            'updated_by' => Auth::id(),
        ]);

        $roleMenu->load(['role', 'menu']);
        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Role Menu', $details)
        );

        return $roleMenu;
    }

    public function delete(int $id): bool
    {
        $roleMenu = RoleMenu::with(['role', 'menu'])->find($id);

        if (!$roleMenu) {
            return false;
        }

        $details = "Role: {$roleMenu->role->role}, Menu: {$roleMenu->menu->name}";
        
        $roleMenu->update(['deleted_by' => Auth::id()]);
        $roleMenu->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Role Menu', $details)
        );

        return true;
    }
}
