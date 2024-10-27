<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiTest extends TestCase
{

    /**
     * 測試成功驗證訂單
     * - 使用 TWD 幣別，不進行轉換
     */
    public function testSuccessfulOrderValidate()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Order data validated and transformed successfully!',
                 ]);
    }

    /**
     * 測試成功驗證訂單
     * - 將 USD 轉換成 TWD 幣別
     */
    public function testSuccessfulOrderValidateAndTransformByUsdToTwd()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 30,
            'currency' => 'USD',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Order data validated and transformed successfully!',
                 ]);
    }

    /**
     * 測試姓名格式錯誤時的失敗
     * - 名稱單字首字非大寫
     */
    public function testFailOrderValidateDueToInvalidNameFormat()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'melody holiday inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1500,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['name'])
                 ->assertJsonFragment([
                     'name' => ['Name is not capitalized'],
                 ]);
    }

    /**
     * 測試姓名格式錯誤時的失敗
     * - 名稱非全英文
     */
    public function testFailOrderValidateDueToNonEnglishCharacters()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody 假日 Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1500,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['name'])
                 ->assertJsonFragment([
                    'name' => ["Name contains non-English characters"],
                 ]);
    }

    /**
     * 測試訂單金額超過限制時的失敗
     */
    public function testFailOrderValidateDueToPriceExceedingLimit()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 3000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['price'])
                 ->assertJsonFragment([
                     'price' => ['Price is over 2000'],
                 ]);
    }

    /**
     * 測試貨幣格式不正確時的失敗
     */
    public function testFailOrderValidateDueToInvalidCurrencyFormat()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'JPY',
        ]);
    
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['currency'])
                 ->assertJsonFragment([
                     'currency' => ['Currency format is wrong'],
                 ]);
    }

    // 以下為其他在驗證中的測試

    /**
     * 測試姓名為空值時的失敗
     */
    public function testFailOrderValidateDueToEmptyId()
    {
        $response = $this->postJson('/api/orders', [
            'id' => '',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['id'])
                 ->assertJsonFragment([
                     'id' => ['Id is required'],
                 ]);
    }

    /**
     * 測試姓名為空值時的失敗
     */
    public function testFailOrderValidateDueToEmptyName()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => '',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['name'])
                 ->assertJsonFragment([
                     'name' => ['Name is required'],
                 ]);
    }

    /**
     * 測試金額為空時的失敗
     */
    public function testFailOrderValidateDueToEmptyPrice()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => '',
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['price'])
                 ->assertJsonFragment([
                     'price' => ['Price is required'],
                 ]);
    }

    /**
     * 測試地址縣市為空時的失敗
     */
    public function testFailOrderValidateDueToEmptyAddressCity()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => '',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['address.city'])
                 ->assertJsonFragment([
                     'address.city' => ['Address city is required'],
                 ]);
    }

    /**
     * 測試地址區域為空時的失敗
     */
    public function testFailOrderValidateDueToEmptyAddressDistrict()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => '',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['address.street'])
                 ->assertJsonFragment([
                     'address.street' => ['Address street is required'],
                 ]);
    }

    /**
     * 測試地址街道為空時的失敗
     */
    public function testFailOrderValidateDueToEmptyAddressStreet()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => '',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['address.district'])
                 ->assertJsonFragment([
                     'address.district' => ['Address district is required'],
                 ]);
    }

    /**
     * 測試幣別為空值時的失敗
     */
    public function testFailOrderValidateDueToEmptyCurrency()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => 1000,
            'currency' => '',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['currency'])
                 ->assertJsonFragment([
                     'currency' => ['Currency is required'],
                 ]);
    }

    /**
     * 測試價格非數值時的失敗
     */
    public function testFailOrderValidateDueToNonNumericPrice()
    {
        $response = $this->postJson('/api/orders', [
            'id' => 'A0000001',
            'name' => 'Melody Holiday Inn',
            'address' => [
                'city' => 'taipei-city',
                'district' => 'da-an-district',
                'street' => 'fuxing-south-road',
            ],
            'price' => '兩千',
            'currency' => 'TWD',
        ]);

        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['price'])
                 ->assertJsonFragment([
                     'price' => ['Price must be a numeric value'],
                 ]);
    }
}
