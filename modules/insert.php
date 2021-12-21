<?php
	ini_set('display_errors', 1);
	require_once "../config.php";

	require_once DB_CONNECT;
	require_once DATA_CUSTOMER;
	include_once VIEW_HEADER;

	$table = TryGetPost("table", null);
	if($table == null)
	{
		exit("No Table Selected");
	}
	else
	{
		
	}
	
	$scheme = $$table;
	if($scheme == null)
	{
		exit("Table Not Valid");
	}
	else
	{
		if($scheme->Insert($conn) == true)
		{
			PreviousPage();
		}
		else
		{
			echo "Something went wrong";
		}
	}
?>