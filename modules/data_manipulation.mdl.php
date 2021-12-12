<?php

	class Data
	{
	    public $name;
	    public $type;
	    public $maxlength;
	    public $stored_value = "";
	    public $regex;

	    function __construct($name, $type, $maxlength, $regex = "//") {
        	$this->name = $name;
        	$this->type = $type;
        	$this->maxlength = $maxlength;
        	$this->regex = $regex;
    	}

    	function name_attr()
    	{
    		return "name='$this->name'";
    	}
    	function placeholder_attr()
    	{
    		return "placeholder='$this->name'";
    	}
    	function type_attr()
    	{
    		return "type='$this->type'";
    	}
    	function maxlength_attr()
    	{
    		if($this->maxlength != 0)
    		{
    			return "maxlength='$this->maxlength'";
    		}
    		return "";
    	}
    	function get_value_from_url()
    	{
    		if(isset($_GET[$this->name]))
				return $_GET[$this->name];
			else
				return "";
    	}
    	function value_attr()
    	{
			return "value='".$this->get_value_from_url()."'";
    	}
    	function input_node()
    	{
    		$error_class = strcmp(TryGetValue("error"), $this->name) == 0 ? "invalid" : "";
    		return "<input ".$this->type_attr()." ".$this->name_attr()." ".$this->maxlength_attr()." ".$this->placeholder_attr()." ".$this->value_attr()." class='$error_class'>";
    	}
    	function get_post($default = "")
    	{
    		$value = TryGetPost($this->name, $default);
    		if(strlen($this->regex) > 2)
			{
				if(preg_match($this->regex, $value))
    			{
    				return $value;
    			}
    			else
    			{
    				$posts = "";
    				$i = 0;
    				foreach ($_POST as $key => $value) {
    					if($i != 0)
    					{
    						$posts = $posts."&";
    					}
    					$posts = $posts."$key=$value";
    					$i++;
    				}
    				header('Location: ' . $_SERVER['HTTP_REFERER']."?$posts&error=".$this->name);
    			}
			}
    		return $value;
    	}
    	function parse_query_result($result, $default = "")
    	{
    		if(isset($result[$this->name]))
    		{
				return $result[$this->name];
			}
    		else
    		{
    			return $default;
    		}
    	}
    	function store_post($default = "")
    	{
    		$this->stored_value = $this->get_post($default);
    	}
	}
	function get_stored_values($data)
	{
		$output = [];
		foreach ($data as &$col) {
			array_push($output, $col->stored_value);
		}
		return $output;
	}
	function store_posts($data, $default = "")
	{
		foreach ($data as &$col) {
			$col->store_post($default);
		}
	}
	function parse_query_result($result, $data)
	{
		foreach ($data as &$col) {
			$col->parse_query_result($result);
		}
		return $data;
	}
	function create_headers($datas, $end = "")
	{
		$output = "";
		foreach ($datas as &$data) {
			$output = $output."<th>$data->name</th>".$end;
		}
		return $output;
	}
	function create_inputs($data, $end = "")
	{
		$output = "";

		foreach ($data as $col) {
			$output = $output."<td>".$col->input_node()."</td>".$end;
		}

		return $output;
	}
	function create_data($data)
	{
		$output = "";
		foreach ($data as &$col) {
			$output = $output."<td>$col->stored_value</td>";
		}
		return $output;
	}
?>