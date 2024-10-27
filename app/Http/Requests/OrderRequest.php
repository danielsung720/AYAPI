<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Service\CurrencyConvert\CurrencyConvertFactory;

class OrderRequest extends FormRequest
{

    /**
     * 訂單金額上限
     * @var integer
     */
    private $priceLimit = 2000;


    /**
     * 驗證權限
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 覆寫驗證失敗時的行為以回應 400 狀態碼
     * 
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'errors' => $validator->errors(),
        ], 400);

        throw new HttpResponseException($response);
    }

    /**
     * 回傳訊息
     * 
     * @return array
     */
    public function messages(): array
    {
        return [
            // ID
            'id.required' => 'Id is required',
            // 姓名
            'name.regex' => 'Name contains non-English characters',
            'name.required' => 'Name is required',
            // 地址
            'address.city.required' => 'Address city is required',
            'address.district.required' => 'Address district is required',
            'address.street.required' => 'Address street is required',
            // 金額
            'price.max' => 'Price is over 2000',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a numeric value',
            // 幣別
            'currency.in' => 'Currency format is wrong',
            'currency.required' => 'Currency is required',
        ];
    }

    /**
     * 檢查訂單格式
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 訂單編號
            'id' => ['required'],
            // 姓名: 只能英文並且每個單字為大寫
            'name' => [
                'required',
                'regex:/^[A-Za-z\s]+$/',
                function ($attribute, $value, $fail) {
                    $this->validateCapitalization($value, $fail);
                }
            ],
            // 地址
            'address.city' => ['required'],
            'address.district' => ['required'],
            'address.street' => ['required'],
            // 金額: 上限2000
            'price' => [
                'required', 
                'numeric', 
                "max:{$this->priceLimit}"
            ],
            // 幣別: TWD, USD
            'currency' => ['required', 'in:TWD,USD'],
        ];
    }

    /**
     * 驗證英文單字的首字是否為大寫
     * 
     * @param string $allWords
     * @param \Closure $fail
     * @return void
     */
    private function validateCapitalization(string $allWords, callable $fail): void
    {
        $wordsAry = explode(' ', $allWords);
        foreach ($wordsAry as $word) {
            if (ucfirst($word) !== $word) {
                $fail('Name is not capitalized');
            }
        }
    }

    /**
     * 驗證訂單金額是否超過台幣2000
     * - 未使用(個人認為可以做的優化，針對不同幣別時進行的驗證)
     * 
     * @param string $currency
     * @param float|int $amount
     * @return void
     */
    private function validateOrderAmount(string $currency, $amount, callable $fail): void
    {
        switch ($currency) {
            case CurrencyConvertFactory::CURRENCY_USD:
                $converAmount = $this->currencyConverter->convert($amount, CurrencyConvertFactory::CURRENCY_TWD);
                if ($converAmount > $this->priceLimit) {
                    $fail("The price in TWD cannot exceed {$this->priceLimit} after conversion from USD.");
                }
                break;
            case CurrencyConvertFactory::CURRENCY_TWD:
                if ($amount > $this->priceLimit) {
                    $fail("The price in TWD cannot exceed {$this->priceLimit}.");
                }
                break;
        }
    }

}
