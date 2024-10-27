<?php

namespace App\Service\CurrencyConvert;

use App\Service\CurrencyConvert\CurrencyConvertTemp;
use App\Exceptions\CurrencyConvertException;

class CurrencyConvertFactory
{

    /**
     * 幣別
     * @var string
     */
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_TWD = 'TWD';

    /**
     * 根據幣別代碼生成相應的幣別轉換策略實例
     *
     * @param string $currencyCode
     * @return CurrencyConvertTemp
     * @throws CurrencyConvertException
     */
    public static function create(string $currencyCode): CurrencyConvertTemp
    {
        $className = 'App\\Service\\CurrencyConvert\\' . $currencyCode . 'Currency';
        if (!class_exists($className)) {
            throw new CurrencyConvertException("Currency format is wrong(Factory)");
        }
        return new $className;
    }

}
