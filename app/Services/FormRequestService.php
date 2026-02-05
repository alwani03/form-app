<?php

namespace App\Services;

use App\Models\FormRequest as FormRequestModel;
use App\Enums\ActivityType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class FormRequestService
{
    public function __construct(
        protected LogActivityService $logActivityService
    ) {}

    public function paginate(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = FormRequestModel::query()->with(['documentType', 'user']);
        $remark = $this->logActivityService->generateRemark(ActivityType::LIST, 'Form Requests');

        if ($search) {
            $query->where('form_name', 'like', '%' . $search . '%')
                  ->orWhere('form_no', 'like', '%' . $search . '%');
            $remark = $this->logActivityService->generateRemark(ActivityType::SEARCH, 'Form Request', $search);
        }

        $this->logActivityService->log($remark);

        return $query->paginate($perPage);
    }

    public function create(array $data): FormRequestModel
    {
        $formRequest = FormRequestModel::create([
            'form_no'            => $data['form_no'],
            'form_name'          => $data['form_name'],
            'document_type_id'   => $data['document_type_id'],
            'type'               => $data['type'] ?? null,
            'status'             => $data['status'] ?? 'pending',
            'user_id'            => Auth::id(),
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::CREATE, 'Form Request', $formRequest->form_no)
        );

        return $formRequest;
    }

    public function find(int $id): ?FormRequestModel
    {
        $formRequest = FormRequestModel::with(['documentType', 'user', 'logs'])->find($id);

        if ($formRequest) {
            $this->logActivityService->log(
                $this->logActivityService->generateRemark(ActivityType::READ, 'Form Request', $formRequest->form_no)
            );
        }

        return $formRequest;
    }

    public function update(int $id, array $data): ?FormRequestModel
    {
        $formRequest = FormRequestModel::find($id);

        if (!$formRequest) {
            return null;
        }

        $formRequest->update([
            'form_no' => $data['form_no'],
            'form_name' => $data['form_name'],
            'document_type_id' => $data['document_type_id'],
            'type' => $data['type'] ?? $formRequest->type,
            'status' => $data['status'] ?? $formRequest->status,
        ]);

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::UPDATE, 'Form Request', $formRequest->form_no)
        );

        return $formRequest;
    }

    public function delete(int $id): bool
    {
        $formRequest = FormRequestModel::find($id);

        if (!$formRequest) {
            return false;
        }

        $formNo = $formRequest->form_no;
        $formRequest->delete();

        $this->logActivityService->log(
            $this->logActivityService->generateRemark(ActivityType::DELETE, 'Form Request', $formNo)
        );

        return true;
    }
}
