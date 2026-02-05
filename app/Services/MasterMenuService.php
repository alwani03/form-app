<?php

namespace App\Services;

use App\Models\MasterMenu;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class MasterMenuService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = MasterMenu::query();
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Master Menus');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Master Menu', $search);
        }

        $this->logActivityService->log($remark);

        return $query->paginate($perPage);
    }

    public function create(array $data): MasterMenu
    {
        $masterMenu = MasterMenu::create([
            'name' => $data['name'],
            'icon' => $data['icon'] ?? null,
            'ordering' => $data['ordering'] ?? 0,
            'is_active' => $data['is_active'] ?? 1,
            'created_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Master Menu', $masterMenu->name)
        );

        return $masterMenu;
    }

    public function find(int $id): ?MasterMenu
    {
        $masterMenu = MasterMenu::with(['menus'])->find($id);

        if ($masterMenu) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Master Menu', $masterMenu->name)
            );
        }

        return $masterMenu;
    }

    public function update(int $id, array $data): ?MasterMenu
    {
        $masterMenu = MasterMenu::find($id);

        if (!$masterMenu) {
            return null;
        }

        $masterMenu->update([
            'name' => $data['name'],
            'icon' => $data['icon'] ?? $masterMenu->icon,
            'ordering' => $data['ordering'] ?? $masterMenu->ordering,
            'is_active' => $data['is_active'] ?? $masterMenu->is_active,
            'updated_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Master Menu', $masterMenu->name)
        );

        return $masterMenu;
    }

    public function delete(int $id): bool
    {
        $masterMenu = MasterMenu::find($id);

        if (!$masterMenu) {
            return false;
        }

        $name = $masterMenu->name;
        $masterMenu->update(['deleted_by' => Auth::id()]);
        $masterMenu->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Master Menu', $name)
        );

        return true;
    }
}
