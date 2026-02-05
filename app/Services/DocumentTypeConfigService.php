<?php

namespace App\Services;

use App\Models\DocumentTypeConfig;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentTypeConfigService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = DocumentTypeConfig::query();
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Document Type Configs');

        if ($search) {
            $query->where('type', 'like', '%' . $search . '%')
                  ->orWhere('document_name', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Document Type Config', $search);
        }

        $this->logActivityService->log($remark);

        return $query->paginate($perPage);
    }

    public function create(array $data): DocumentTypeConfig
    {
        $config = DocumentTypeConfig::create([
            'type' => $data['type'],
            'document_name' => $data['document_name'],
            'approval' => $data['approval'] ?? false,
            'setting' => $data['setting'] ?? null,
            'is_active' => $data['is_active'] ?? 1,
            'created_by' => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Document Type Config', $config->type)
        );

        return $config;
    }

    public function find(int $id): ?DocumentTypeConfig
    {
        $config = DocumentTypeConfig::find($id);

        if ($config) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Document Type Config', $config->type)
            );
        }

        return $config;
    }

    public function update(int $id, array $data): ?DocumentTypeConfig
    {
        $config = DocumentTypeConfig::find($id);

        if (!$config) {
            return null;
        }

        $config->update([
            'type' => $data['type'],
            'document_name' => $data['document_name'],
            'approval' => $data['approval'] ?? $config->approval,
            'setting' => $data['setting'] ?? $config->setting,
            'is_active' => $data['is_active'] ?? $config->is_active,
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Document Type Config', $config->type)
        );

        return $config;
    }

    public function delete(int $id): bool
    {
        $config = DocumentTypeConfig::find($id);

        if (!$config) {
            return false;
        }

        $typeName = $config->type;
        $config->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Document Type Config', $typeName)
        );

        return true;
    }
}
