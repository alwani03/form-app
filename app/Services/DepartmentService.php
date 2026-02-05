<?php

namespace App\Services;

use App\Models\Department;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;

class DepartmentService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10)
    {
        $query = Department::query();
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Departments');

        if ($search) {
            $query->where('department_name', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Department', $search);
        }

        $this->logActivityService->log($remark);
        return $query->paginate($perPage);
    }

    public function create(string $departmentName): Department
    {
        $department = Department::create([
            'department_name' => $departmentName,
            'created_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Department', $department->department_name)
        );

        return $department;
    }

    public function find(int $id): ?Department
    {
        $department = Department::find($id);
        if ($department) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Department', $department->department_name)
            );
        }
        return $department;
    }

    public function update(int $id, array $data): ?Department
    {
        $department = Department::find($id);
        if (!$department) {
            return null;
        }

        $department->update([
            'department_name' => $data['department_name'],
            'updated_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Department', $department->department_name)
        );

        return $department;
    }

    public function delete(int $id): bool
    {
        $department = Department::find($id);
        if (!$department) {
            return false;
        }

        $name = $department->department_name;
        $department->update(['deleted_by' => Auth::id()]);
        $department->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Department', $name)
        );

        return true;
    }
}
