<?php

namespace App\Service\CurrencyConvert;

use App\Exceptions\CurrencyConvertException;

abstract class CurrencyConvertTemp
{

    /**
     * 本身幣別
     * @var string
     */
    protected $currency;

    /**
     * 匯率對照表
     * @var array
     */
    protected $exchangeRates;

    /**
     * 建構子
     */
    public function __construct()
    {
        $this->setCurrency();
        $this->setExchangeRates();
    }

    /**
     * 設置本身幣別
     * 
     * @return void
     */
    abstract protected function setCurrency(): void;

    /**
     * 設置匯率對照
     * 
     * @return void
     */
    abstract protected function setExchangeRates(): void;

    /**
     * 取得匯率
     * 
     * @return float
     */
    private function getExchangeRate(string $toCurrency): float
    {
        if (!isset($this->exchangeRates[$toCurrency])) {
            throw new CurrencyConvertException("Unsupported currency conversion from {$this->currency} TO {$toCurrency}.");
        }
        return $this->exchangeRates[$toCurrency];
    }

    /**
     * 兌換
     * 
     * @param float $amount
     * @param string $toCurrency
     * @return float
     */
    public function convert(float $amount, string $toCurrency): float
    {
        return $amount * $this->getExchangeRate($toCurrency);
    }

}