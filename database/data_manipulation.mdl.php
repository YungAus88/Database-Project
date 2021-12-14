<?php
	
	// The abstract datatype, storing basic data settings
	class Data
	{
	    public $name;
	    public $type; // e.g. VARCHAR, IMAGE, DATE
	    public $isValid = true;
	    public $notNull = false;
	    public $stored_value = null;
	    public $default_value = null;
	    public $isPrimary = false;

    	function __construct($name, $notNull = false, $default_value = null, $isPrimary = false) {
        	$this->name = $name;
        	$this->notNull = $notNull;
        	$this->default_value = $default_value;
        	$this->isPrimary = $isPrimary;
    	}

    	// === Attribute === //
    	// Generate a HTML attribute by data name with given prefix.
    	function AttrByName($prefix = "name")
    	{
    		return "$prefix='$this->name'";
    	}
    	// Generate a HTML attribute by data type with given prefix.
    	function AttrByType($prefix = "type")
    	{
    		return "$prefix='$this->type'";	
    	}
    	// Generate a HTML attribute by finding value in the URL that having the same name.
    	function value_attr()
    	{
			return "value='".$this->GetValueFromUrl()."'";
    	}
    	// === Attribute === //

    	// === Get Methods === //
    	// Using data's name to find equivalence value.
    	function GetValueFromUrl()
    	{
    		if(isset($_GET[$this->name]))
				return $_GET[$this->name];
			else
				return "";
    	}
    	// Using data's name to find equivalence post value.
    	function GetPostValue($default = "")
    	{
    		$value = TryGetPost($this->name, $default);
    		return $value;
    	}
    	// Using data's name to find equivalence query result.
    	function ParseQueryResult($result, $default = "")
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
    	// === Get Methods === //

    	// Generate a node with given node type with data's name.
    	function ToNode($node = "p")
    	{
    		return "<$node>".$this->name."</$node>";
    	}


    	function Validate($variable = null)
    	{
    		return true;
    	}

    	function input_node()
    	{
    		$error_class = strcmp(TryGetValue("error"), $this->name) == 0 ? "invalid" : "";
    		return "<input ".$this->AttrByType()." ".$this->AttrByName()." ".$this->AttrByName("place_holder")." ".$this->value_attr()." class='$error_class'>";
    	}

    	function AsPrimary()
    	{
    		$this->primary = true;
    	}
	}

	class PatternData extends DATA
	{
		public $maximumLength = 0;
		public $pattern = "//";
		function __construct($name, $maximumLength = 0, $default_value = null, $pattern = "//", $isPrimary = false) {
			parent::__construct($name, default_value: $default_value, isPrimary: $isPrimary);
        	$this->name = $name;
        	$this->maximumLength = $maximumLength;
        	$this->pattern = $pattern;
    	}
    	public function Validate($variable = "")
    	{
    		if(strlen($variable > $this->maximumLength))
    		{
    			return false;
    		}
    		else if(strlen($this->pattern) > 2)
			{
				if(preg_match($this->pattern, (string)$variable))
    			{
    				return true;
    			}
    			else
    			{
    				return false;
    			}
			}
			else
			{
				return true;
			}
    	}
	}

	class VARCHAR extends PatternData
	{
		function __construct($name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			parent::__construct($name, $maximumLength, $default_value, $pattern, $isPrimary);
			$this->type = "text";
    	}
	}
	class NUMERIC extends PatternData
	{
		function __construct($name, $maximumLength = 0, $default_value = 0, $pattern = "//", $isPrimary = false)
		{
			parent::__construct($name, $maximumLength, $default_value, $pattern, $isPrimary);
			$this->type = "number";
    	}
	}
	class DATE extends PatternData
	{
		function __construct($name, $default_value = "1970-01-01")
		{
			parent::__construct($name, 12, $default_value, "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/");
			$this->type = "number";
    	}
	}
	class IMAGE extends DATA
	{
		// File upload path
		public $targetDir = "";
		public $fileName = "";
		public $targetFilePath = "";
		public $fileType = "";
		public $size_limit = 0;
		function __construct($name, $size_limit = 0)
		{
			parent::__construct($name, default_value: null);
			$this->type = "file";
			$this->size_limit = $size_limit;
    	}
		function StorePostValue($default = "")
    	{
    		// File upload path
			$this->targetDir = "uploads/";
			$this->fileName = basename($_FILES["image"]["name"]);
			$this->targetFilePath = $targetDir . $fileName;
			$this->fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
			return file_get_contents($_FILES[$this->name]['tmp_name']);
    	}
    	function Validate($variable = null)
    	{
			// Allow certain file formats
		    $allowTypes = array('jpg','png','jpeg');
		    if(in_array($this->fileType, $allowTypes))
		    {
		        return true;
		    }
		    else
		    {
		    	return false;
		        $statusMsg = 'Sorry, only JPG, JPEG, PNG files are allowed to upload.';
		    }
    	}
	}

	class Scheme extends ArrayObject
	{
		static function Create($table_name, $blueprint_func)
		{
			$newScheme = new Scheme();
			$blueprint_func($newScheme);
			return $newScheme;
		}

		function FindPrimary()
		{
			foreach ($this as $key => $value) {
				if($value->isPrimary)
				{
					return $key;
				}
			}
		}

		function GetNames()
		{
			$output = array();
			foreach ($this as $key => $value) {
				array_push($output, $key);
			}
			return $output;
		}

		function GenBuffer()
		{
			$output = array();
			foreach ($this as $key => $value) {
				$output[$key] = null;
			}
			return $output;
		}

		function Select($conn, $primary_match = null, $other_matches = null, $limit = 10, $offset = 0)
		{
			if(!empty($conn))
			{
				$primary_name = $this->FindPrimary();
				$stmt = $conn->stmt_init();

				if(empty($primary_match)) // Select All
				{
					$sql = "SELECT * FROM `customer_data` LIMIT $limit OFFSET $offset";
					$stmt->prepare($sql);
				}
				else
				{
					$sql = "SELECT * FROM `customer_data` WHERE `$primary_name` LIKE ? LIMIT $limit OFFSET $offset";
					if(!$stmt->prepare($sql))
					{
						return null;
					}
					$stmt->bind_param("s", $primary_match);
				}

				$stmt->execute();
				$result = $stmt->get_result();

				$buffer = $this->GenBuffer();

				$stmt->bind_result(...array_values($buffer));

				$output = $result->fetch_all(MYSQLI_ASSOC);
				// echo var_dump($output);
				return $output;
			}
			else
			{
				exit("Connection not available");
			}
		}

		private function InsertNewVariable($var)
		{
			$this->offsetSet($var->name, $var);
			return $this->offsetGet($var->name);
		}

		function IMAGE($name, $size_limit = 0)
		{
			return $this->InsertNewVariable(new Image($name), $size_limit);
		}

		function NUMERIC($name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			return $this->InsertNewVariable(new NUMERIC($name, $maximumLength, $default_value, $pattern, $isPrimary));
		}

		function DATE($name, $default_value = "1970-01-01")
		{
			return $this->InsertNewVariable(new DATE($name, $default_value));
		}

		function VARCHAR($name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			return $this->InsertNewVariable(new VARCHAR($name, $maximumLength, $default_value, $pattern, $isPrimary));
		}

		function IsAllValid(&$error_array)
		{
			$isValid = 0;
			$errors = array();
			foreach ($this as $key => $variable)
			{
				if($variable->Validate())
				{
					$isValid += 1;
				}
				else
				{
					$isValid += 0;
					array_push($errors, $variable->name);
				}
			}
			$error_array = $errors;
			return $isValid;
		}

		function ValidateAll()
		{
			$isValid = $this->IsAllValid();
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
			$url = $_SERVER['HTTP_REFERER'];
			$url = explode("?", $url)[0];
			header('Location: '.$url."?$posts&error=".$this->name);
		}

		// === Array ===
		public function __construct($input = array(), $flags = 0, $iterator_class = 'ArrayIterator')
		{
	        if (isset($input) && is_array($input)) {
	            $tmpargs = func_get_args();
	            return call_user_func_array(array('parent', __FUNCTION__), $tmpargs);
	        }
        	return call_user_func_array(array('parent', __FUNCTION__), func_get_args());
	    }

	    public function offsetExists($index)
	    {
	        if (is_string($index)) return parent::offsetExists($index);
	        return parent::offsetExists($index);
	    }

	    public function offsetGet($index)
	    {
	        if (is_string($index)) return parent::offsetGet($index);
	        return parent::offsetGet($index);
	    }

	    public function offsetSet($index, $value)
	    {
	        if (is_string($index)) return parent::offsetSet($index, $value);
	        return parent::offsetSet($index, $value);
	    }

	    public function offsetUnset($index)
	    {
	        if (is_string($index)) return parent::offsetUnset($index);
	        return parent::offsetUnset($index);
	    }
	    // === Array ===
	}

	abstract class Table
	{
		public $rows = array();
		function InsertNewRow($row)
		{
			array_push($this->rows, $row);
		}

		abstract function Select();
	}

	// ===== OLD =====

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
	function create_headers($scheme, $end = "")
	{
		$output = "";
		$names = $scheme->GetNames();
		foreach ($names as &$name) {
			$output = $output."<th>".$name."</th>".$end;
		}
		return $output;
	}
	function create_inputs($scheme, $end = "")
	{
		$output = "";

		foreach ($scheme as $key => $col) {
			// echo gettype($scheme);
			$output = $output."<td>".$col->input_node()."</td>".$end;
		}

		return $output;
	}
	function create_data($data)
	{
		$output = "";
		foreach ($data as $key => $col) {
			$output = $output."<td>".$col->stored_value."</td>";
		}
		return $output;
	}
?>