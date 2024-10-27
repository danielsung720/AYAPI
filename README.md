# AsiaYo測驗題目
姓名: 宋紹寧

# 資料庫測驗
## 題目一 
Q: 請寫出一條查詢語句 (SQL)，列出在 2023 年 5 月下訂的訂單，使用台幣付款且5月總金額最多的前 10 筆的旅宿 ID (bnb_id), 旅宿名稱 (bnb_name), 5 月總金額 (may_amount)

A: 
```
SELECT bnbs.id AS bnb_id, bnbs.name AS bnb_name, orders.may_amount
FROM bnbs
INNER JOIN (
  ## 用子查詢先統計出台幣總額前10名的旅宿
  SELECT bnb_id, SUM(amount) AS may_amount 
  FROM orders
  WHERE currency = 'TWD'
  AND created_at BETWEEN 1682870400 AND 1685548799
  GROUP BY bnb_id
  ORDER BY may_amount DESC
  LIMIT 10
) AS orders ON orders.bnb_id = bnbs.id;
```

## 題目二
Q: 在題目一的執行下，我們發現 SQL 執行速度很慢，您會怎麼去優化?請闡述您怎麼判斷與優化的方式

A: 
- 先透過 EXPLAIN 語句判斷查詢狀況，檢視是否進行全表掃描/索引使用狀況
- 對未添加索引欄位建立索引，尤其是經查查詢使用的 orders.bnb_id, orders.currency, orders.created_at
- 資料表過大的話可以按時間來做 Partitioning
- 若這是經常使用的查詢並且變動性不大的話，可設置排程將查詢結果儲存至快取

# API實作測驗
## 題目一
Q: 說明您所使用的 SOLID 與設計模式分別為何

A: 以下為使用到的架構和說明
### ProviderService
路徑: /asiayo-api/app/Providers/AppServiceProvider.php
說明: 依賴注入 結合 工廠模式 (Laravel本身的設計理念)，並將 OrderServiceInterface 抽出，並由此處依賴注入Service，但現在未有訂單類別，用意為後續擴充時的開口
- OCP: 新增 訂單類型 只要以 OrderServiceInterface 實現，並不會調整到舊有的 訂單類別
- LSP: 所有 訂單類別 都遵照介面只須完成 處理訂單 方法，彼此間可替換

## OrderController
路徑: /asiayo-api/app/Http/Controllers/OrderController.php
- SPR: controller 處理 http 請求，業務邏輯交由 OrderService 處理
- OCP/LSP: 透過抽象處理訂單，便於擴充訂單類型/替換
- DIP: 依賴於抽象 orderServiceInterface

### DTO (Data Transfer Object)
路徑: /asiayo-api/app/DTO/
說明: 將 訂單 資料結構化，並針對 訂單 類型的DTO使用 Interface 便於未來訂單類型的擴展
- SRP: 每一種 DTO 只負責傳輸數據，不涉及其他業務邏輯操作
- OCP: 繼承於抽象類別 BaseDTO，便於擴充

### OrderService
目的: 訂單處理
路徑: /asiayo-api/app/Service/Order/
說明: 目前只先將 interface 拆分出來便於擴充未來有可能新增的訂單類型
- SPR: 只處理訂單業務邏輯

### CurrencyConvertService
目的: 匯兌轉換
路徑: /asiayo-api/app/Service/CurrencyConvert/
設計模式: 樣板 + 工廠
說明: 由 樣板(Temp) 規範每個幣別所需完成方法，接著由 各種幣別(EX: USD) 繼承樣板，最後由 工廠(Factory) 產生實例
- SPR: 每個 class 皆有自己的職責，Temp規範/Factory生產/幣別類 實作產生匯率
- OCP: 只需要在工廠中新增 幣別常數 以及與之相應的 幣別類 即可擴充
- LSP: 幣別類使用可任意替換
- DIP: 幣別類皆依賴抽象
