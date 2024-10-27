<?php

namespace App\DTO\OrderDTO;

use App\DTO\BaseDTO;

class AddressDTO extends BaseDTO
{

    public string $city;
    public string $district;
    public string $street;

    public static function fromArray(array $data): self
    {
        return new self([
            'city' => $data['city'],
            'district' => $data['district'],
            'street' => $data['street'],
        ]);
    }

}
