<?php
namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Clients\DTOs\ClientPackageData;

class CreateClientPackageAction
{
    public function execute(ClientPackageData $data): ClientPackage
    {
        return ClientPackage::create([
            'client_id' => $data->client_id,
            'package_id' => $data->package_id,
            'starts_at' => $data->starts_at,
            'ends_at' => $data->ends_at,
            'is_trial' => $data->is_trial,
            'is_active' => $data->is_active,
            'status' => $data->is_active
                ? ClientPackage::STATUS_ACTIVE
                : ClientPackage::STATUS_CANCELLED,
        ]);
    }
}