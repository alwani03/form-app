<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'role_id'       => $this->role_id,
            'role'          => $this->role->role ?? null,
            'department_id' => $this->department_id,
            'department'    => $this->department->department_name ?? null,
            'is_active'     => (bool) $this->is_active,
            'menus'         => $this->menu->map(function ($menu) {
                return [
                    'id'          => $menu->id,
                    'master_menu' => $menu->masterMenu ? [
                        'id'       => $menu->masterMenu->id,
                        'name'     => $menu->masterMenu->name,
                        'icon'     => $menu->masterMenu->icon,
                        'ordering' => $menu->masterMenu->ordering,
                    ] : null,
                    'name'        => $menu->name,
                    'url'         => $menu->url,
                    'icon'        => $menu->icon,
                    'ordering'    => $menu->ordering,
                ];
            }),
            // Placeholder structure for permissions as per recommendation
            // In a real implementation, you would fetch these from role_menus or a permissions table
            'permissions' => [
                'users' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true], // Example default
                'forms' => ['view' => true, 'create' => true, 'approve' => ($this->role->role === 'admin' || $this->role->role === 'manager')],
            ],
        ];
    }
}
