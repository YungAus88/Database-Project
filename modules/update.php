<?php
	ini_set('display_errors', 1);
	require_once "../config.php";

	require_once DB_CONNECT;
	require_once DATA_CUSTOMER;
	include_once VIEW_HEADER;

	foreach ($_POST as $key => $value) {
		echo "key: $key, value: $value"."<br>";
	}

	$table = TryGetPost("table", null);
	if($table == null)
	{
		exit("No Table Selected");
	}
	else
	{
		echo $table;
	}
	
	$scheme = $$table;
	if($scheme == null)
	{
		exit("Table Not Valid");
	}
	else
	{
		$scheme->Update($conn);
	}
	PreviousPage();
?>