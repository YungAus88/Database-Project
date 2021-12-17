<?php
$customer_scheme = Scheme::Create("customer_data", function ($table)
{
	$table->VARCHAR("客戶姓名", "name", "12", "CUSTOMER");
	$table->VARCHAR("身分證字號", "id", "10", "IDENTITY", "/\w[0-9]{0,9}/")->AsPrimary();
	$table->VARCHAR("手機號碼", "phone", "16", "PHONE_NUM", "/^09[0-9]{0,2}-?[0-9]{0,3}-?[0-9]{0,3}$/");
	$table->VARCHAR("住址", "address", "30", "ADDRESS");
	$table->VARCHAR("年齡", "age", "4", "AGE", "/\d*/");
	$table->VARCHAR("職業", "profession", "12", "PRO");
	$table->DATE("登載日期", "created_at", default_value: date('Y-m-d'));
	$table->IMAGE("照片", "photo");
	$table->VARCHAR("消費狀態", "consumption_state", "12", "TRUE");
});

$product_scheme = Scheme::Create("product_data", function ($table)
{
	$table->VARCHAR("供應商編號", "supplier_id", "5", "supplier_id")->AsPrimary();
	$table->VARCHAR("供應商負責人", "supplier_in_charge", "12", "supplier_in_charge");
	$table->VARCHAR("供應商名稱", "supplier_co_name", "16", "supplier_co_name");
	$table->VARCHAR("進貨品名", "product_name", "16", "product_name");
	$table->VARCHAR("庫存位置", "storage_position", "16", "storage_position");
	$table->VARCHAR("規格", "specification", "16", "specification");
	$table->VARCHAR("進貨單位", "product_unit", "6", "product_unit");
	$table->NUMERIC("進貨數量", "product_count", default_value: 0);
	$table->NUMERIC("進貨單價", "product_value_per_unit");
	$table->NUMERIC("小計", "total_value");
	$table->DATE("進貨日期", "import_date", default_value: date('Y-m-d'));
});

$accounts_scheme = Scheme::Create("accounts_receivable", function ($table)
{
	$table->VARCHAR("身分證字號", "id", "10", "id")->AsPrimary();
	$table->NUMERIC("應收金額", "value", "11", "value");
	$table->NUMERIC("待催收金額", "value_awaiting", "11", "value_awaiting");
	$table->DATE("應收日期", "due_to_date", default_value: date('Y-m-d'));
	$table->VARCHAR("客戶姓名", "customer_name", "16", "customer_name");
	$table->VARCHAR("狀態", "active", "5", "1");
});

$order_scheme = Scheme::Create("customer_order", function ($table)
{
	$table->VARCHAR("身分證字號", "customer_id", "10", "customer_id", "/\w[0-9]{0,9}/")->AsPrimary();
	$table->VARCHAR("訂貨品名", "product_name", "16", "product_name");
	$table->VARCHAR("供應商名稱", "supplier_co_name", "16", "supplier_co_name");
	$table->VARCHAR("單位", "product_unit", "6", "UNIT");
	$table->NUMERIC("數量", "order_count", "11", "1", "/\d*/");
	$table->NUMERIC("單價", "product_value_per_unit", "11", "1", "/\d*/");
	$table->NUMERIC("訂貨金額", "order_value", "11", "1", "/\d*/");
});
?>