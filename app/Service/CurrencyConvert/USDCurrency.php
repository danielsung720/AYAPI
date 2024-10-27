<?php

namespace App\Service\CurrencyConvert;

use App\Service\CurrencyConvert\CurrencyConvertTemp;
use App\Service\CurrencyConvert\CurrencyConvertFactory;

class USDCurrency extends CurrencyConvertTemp
{

    /**
     * 設置幣別 - 美元
     * 
     * @return void
     */
    protected function setCurrency(): void
    {
        $this->currency = CurrencyConvertFactory::CURRENCY_USD;
    }

    /**
     * 設置匯率對照
     * 
     * @return void
     */
    protected function setExchangeRates(): void
    {
        $this->exchangeRates = [
            CurrencyConvertFactory::CURRENCY_TWD => 31
        ];
    }

}