<?php

namespace App\Exceptions;

use InvalidArgumentException;

class CurrencyConvertException extends InvalidArgumentException
{

    /**
     * 建構子
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }

}
