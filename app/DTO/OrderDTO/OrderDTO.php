<?php

namespace App\DTO\OrderDTO;

use App\DTO\BaseDTO;
use App\DTO\OrderDTO\OrderDTOInterface;

class OrderDTO extends BaseDTO implements OrderDTOInterface
{
    public string $id;
    public string $name;
    public AddressDTO $address;
    public float $price;
    public string $currency;

    /**
     * 從陣列創建 OrderDTO
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self([
            'id' => $data['id'],
            'name' => $data['name'],
            'address' => AddressDTO::fromArray($data['address']),
            'price' => (float) $data['price'],
            'currency' => $data['currency'],
        ]);
    }
    
}
