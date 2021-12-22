<?php
	function TryGetValue($name, $default_set = "")
	{
		if(isset($_GET[$name]))
			return $_GET[$name];
		else
			return $default_set;
	}
	function TryGetPost($name, $default_set = null)
	{
		if(isset($_POST[$name]) && !empty($_POST[$name])) // Post value found
		{
			return $_POST[$name];
		}
		else // Post value not found 
		{	
			if($default_set == null)
			{
				return false;
			}
			else
			{
				return $default_set;	
			}
		}
		
	}
	function CurrentPage()
	{
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$url = explode("?", $url)[0];
		return $url;
	}
	function PreviousPage()
	{
		$url = $_SERVER['HTTP_REFERER'];
		$url = explode("?", $url)[0];
		header('Location: '.$url);
	}

	function CombineWhereStatements($where_statements)
	{
		$where_array = array();
		$has_value = false;
		foreach ($where_statements as $key => $value)
		{
			$has_value = true;
			array_push($where_array, "$key LIKE '$value'");
		}
		if(!$has_value)
		{
			return "";
		}
		return implode(" AND ", $where_array);
	}
	
?>