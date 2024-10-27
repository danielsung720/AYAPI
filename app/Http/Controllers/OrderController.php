<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\OrderRequest;
use App\DTO\OrderDTO\OrderDTO;
use App\Service\Order\OrderInterface;
use Exception;

class OrderController extends Controller
{

    /**
     * 訂單Service
     * @var OrderInterface
     */
    private OrderInterface $orderService;

    /**
     * 建構子
     * 
     * @param OrderInterface $orderService
     */
    public function __construct(OrderInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * 檢查或轉換訂單格式
     * 
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function validateAndTransform(OrderRequest $request): JsonResponse
    {
        // 驗證資料
        $validatedData = $request->validated();
        try {
            // 將資料轉換成 DTO 物件
            $orderDTO = OrderDTO::fromArray($validatedData);

            // 進行訂單處理
            $processedOrder = $this->orderService->processOrder($orderDTO);
         
            return response()->json([
                'message' => 'Order data validated and transformed successfully!',
                'data' => $processedOrder->toArray(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
