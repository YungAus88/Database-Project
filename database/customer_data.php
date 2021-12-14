<?php
$customer_scheme = Scheme::Create("customer_data", function ($table)
{
	$table->VARCHAR("客戶姓名", "12");
	$table->VARCHAR("身分證字號", "10")->AsPrimary();
	$table->VARCHAR("手機號碼", "16");
	$table->VARCHAR("住址", "30");
	$table->VARCHAR("年齡", "4");
	$table->VARCHAR("職業", "12");
	$table->DATE("登載日期");
	$table->IMAGE("照片");
	$table->VARCHAR("消費狀態", "12");
});
?>