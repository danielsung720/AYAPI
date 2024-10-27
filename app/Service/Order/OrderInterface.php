<?php

namespace App\Service\Order;

use App\DTO\OrderDTO\OrderDTOInterface;

interface OrderInterface
{

    /**
     * 處理訂單
     *
     * @return mixed
     */
    public function processOrder(OrderDTOInterface $orderDTO): mixed;

}