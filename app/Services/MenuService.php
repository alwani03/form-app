<?php

namespace App\Services;

use App\Models\Menu;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class MenuService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query  = Menu::with('masterMenu');
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Menus');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('url', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Menu', $search);
        }

        $this->logActivityService->log($remark);

        return $query->paginate($perPage);
    }

    public function create(array $data): Menu
    {
        $menu = Menu::create([
            'master_menu_id'   => $data['master_menu_id'],
            'name'             => $data['name'],
            'icon'             => $data['icon'] ?? null,
            'ordering'         => $data['ordering'] ?? 0,
            'url'              => $data['url'],
            'description'      => $data['description'] ?? null,
            'is_active'        => $data['is_active'] ?? 1,
            'created_by'       => Auth::id(),
        ]);

        $menu->load('masterMenu');

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Menu', $menu->name)
        );

        return $menu;
    }

    public function find(int $id): ?Menu
    {
        $menu = Menu::with('masterMenu')->find($id);

        if ($menu) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Menu', $menu->name)
            );
        }

        return $menu;
    }

    public function update(int $id, array $data): ?Menu
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return null;
        }

        $menu->update([
            'master_menu_id' => $data['master_menu_id'] ?? $menu->master_menu_id,
            'name' => $data['name'],
            'icon' => $data['icon'] ?? $menu->icon,
            'ordering' => $data['ordering'] ?? $menu->ordering,
            'url' => $data['url'],
            'description' => $data['description'] ?? $menu->description,
            'is_active' => $data['is_active'] ?? $menu->is_active,
            'updated_by' => Auth::id(),
        ]);

        $menu->load('masterMenu');

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Menu', $menu->name)
        );

        return $menu;
    }

    public function delete(int $id): bool
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return false;
        }

        $menuName = $menu->name;
        
        $menu->update(['deleted_by' => Auth::id()]);
        $menu->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Menu', $menuName)
        );

        return true;
    }
}
