<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

abstract class BaseDTO extends DataTransferObject
{

    /**
     * 創建 DTO
     * 
     * @param array $data
     * @return self
     */
    abstract static function fromArray(array $data): self;

    /**
     * 將 DTO 轉換為陣列
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = get_object_vars($this);
        
        // 不使用 exceptKeys, onlyKeys
        unset($data['exceptKeys'], $data['onlyKeys']);

        return $data;
    }

}
