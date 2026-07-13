<?php

namespace App\Domain\Clients\DTOs;

class ClientData
{
    public function __construct(
        public string $user_id,
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone = null,
        public ?string $division = null,
        public ?string $district = null,
        public ?string $address = null,
        public ?string $postal_code = null,
        public ?string $nid_number = null,
        public ?string $nid_card_front = null,
        public ?string $nid_card_back = null,
        public ?string $profile_photo = null,
        public ?string $password = null,
        public ?int $package_id = null,
        public ?int $parent_id = null,
        public bool $status = true,
    ) {}
}
