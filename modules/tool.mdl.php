<?php
	function TryGetValue($name, $default_set = "")
	{
		if(isset($_GET[$name]))
			return $_GET[$name];
		else
			return $default_set;
	}
	function TryGetPost($name, $default_set = "")
	{
		if(isset($_POST[$name]))
			return $_POST[$name];
		else
			return $default_set;
	}
	function PostToURLAttribute()
	{
		
	}
?>