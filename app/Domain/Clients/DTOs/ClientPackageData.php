<?php

namespace App\Domain\Clients\DTOs;

class ClientPackageData
{
    public function __construct(
        public int $client_id,
        public int $package_id,
        public string $starts_at,
        public string $ends_at,
        public bool $is_trial,
        public bool $is_active,
    ) {}
}

