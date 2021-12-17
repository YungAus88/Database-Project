<?php
$customer_scheme = Scheme::Create("customer_data", function ($table)
{
	$table->VARCHAR("客戶姓名", "name", "12", "CUSTOMER");
	$table->VARCHAR("身分證字號", "id", "10", "IDENTITY")->AsPrimary();
	$table->VARCHAR("手機號碼", "phone", "16", "PHONE_NUM");
	$table->VARCHAR("住址", "address", "30", "ADDRESS");
	$table->VARCHAR("年齡", "age", "4", "AGE");
	$table->VARCHAR("職業", "profession", "12", "PRO");
	$table->DATE("登載日期", "created_at");
	$table->IMAGE("照片", "photo");
	$table->VARCHAR("消費狀態", "consumption_state", "12", "TRUE");
});
?>