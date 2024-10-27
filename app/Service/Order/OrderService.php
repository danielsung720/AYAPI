<?php

namespace App\Service\Order;

use App\Service\Order\OrderInterface;
use App\DTO\OrderDTO\OrderDTO;
use App\DTO\OrderDTO\OrderDTOInterface;
use App\Service\CurrencyConvert\CurrencyConvertFactory;

class OrderService implements OrderInterface
{

    /**
     * 訂單處理流程
     *
     * @param OrderDTO $orderDTO
     * @return OrderDTO
     */
    public function processOrder(OrderDTOInterface $orderDTO): OrderDTO
    {
        // 當幣別為 USD 時進行轉換
        if ($orderDTO->currency == CurrencyConvertFactory::CURRENCY_USD) {
            $orderDTO = $this->convertCurrency($orderDTO, CurrencyConvertFactory::CURRENCY_USD, CurrencyConvertFactory::CURRENCY_TWD);
        }

        return $orderDTO;
    }

    /**
     * 幣別轉換
     *
     * @param OrderDTO $orderDTO
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return OrderDTO
     */
    private function convertCurrency(OrderDTO $orderDTO, string $fromCurrency, string $toCurrency): OrderDTO
    {
        $orderDTO->price = (CurrencyConvertFactory::create($fromCurrency))->convert($orderDTO->price, $toCurrency);
        $orderDTO->currency = $toCurrency;

        return $orderDTO;
    }

}