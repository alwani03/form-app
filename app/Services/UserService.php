<?php

namespace App\Services;

use App\Models\User;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10, bool $skipLog = false): LengthAwarePaginator
    {
        $query = User::with(['role', 'department']);
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Users');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'User', "Keyword: {$search}");
        }

        if (!$skipLog) {
            $this->logActivityService->log($remark);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): User
    {
        $user = User::create([
            'username'        => $data['username'],
            'password'        => Hash::make($data['password']),
            'email'           => $data['email'],
            'role_id'         => $data['role_id'],
            'department_id'   => $data['department_id'],
            'is_active'       => $data['is_active'] ?? true,
            'created_by'      => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'User', $user->username)
        );

        return $user;
    }

    public function find(int $id, bool $skipLog = false): ?User
    {
        $user = User::with(['role', 'department','role_menus','menu.masterMenu'])->find($id);

        if ($user && !$skipLog) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'User', $user->username)
            );
        }

        return $user;
    }

    public function update(int $id, array $data): ?User
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'department_id' => $data['department_id'],
            'is_active' => $data['is_active'] ?? $user->is_active,
            'updated_by' => Auth::id(),
        ];

        if (isset($data['password']) && !empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'User', $user->username)
        );

        return $user;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        $username = $user->username;
        $user->update(['deleted_by' => Auth::id()]);
        $user->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'User', $username)
        );

        return true;
    }
}
