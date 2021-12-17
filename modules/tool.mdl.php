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
	function PostToURLAttribute()
	{

	}
?>