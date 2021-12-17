<?php
	
	// The abstract datatype, storing basic data settings
	class Data
	{
	    public $name;
	    public $db_name;
	    public $type; // e.g. VARCHAR, IMAGE, DATE
	    public $prepare_type;
	    public $isValid = true;
	    public $notNull = false;
	    public $stored_value = null;
	    public $default_value = null;
	    public $isPrimary = false;

    	function __construct($name, $db_name, $notNull = false, $default_value = null, $isPrimary = false, $prepare_type = "s") {
        	$this->name = $name;
        	$this->db_name = $db_name;
        	$this->notNull = $notNull;
        	$this->default_value = $default_value;
        	$this->isPrimary = $isPrimary;
        	$this->prepare_type = $prepare_type;
    	}

    	// === Attribute === //
    	// Generate a HTML attribute by database name with given prefix.
    	function AttrByDBName($prefix = "name")
    	{
    		return "$prefix='$this->db_name'";
    	}
    	// Generate a HTML attribute by display name with given prefix.
    	function AttrByName($prefix = "name")
    	{
    		return "$prefix='".$this->name."'";
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

    	// Generates somthing like this: "`name`=?"
    	function GetUpdateSlot()
    	{
    		return "`".$this->db_name."`=?";
    	}
    	// Using data's name to find equivalence value.
    	function GetValueFromUrl()
    	{
    		if(isset($_GET[$this->name]))
				return $_GET[$this->name];
			else
				return "";
    	}
    	// Using data's name to find equivalence post value.
    	function GetPostValue($default = null)
    	{
    		if($value = TryGetPost($this->db_name, $default))
    		{
    			return $value;
    		}
    		else
    		{
    			return false;
    		}
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
    		return "<input ".$this->AttrByDBName("id")." ".$this->AttrByType()." ".$this->AttrByDBName()." ".$this->AttrByName("placeholder")." ".$this->value_attr()." class='$error_class'>";
    	}

    	function AsPrimary()
    	{
    		$this->isPrimary = true;
    	}
	}

	class PatternData extends DATA
	{
		public $maximumLength = 0;
		public $pattern = "//";
		function __construct($name, $db_name, $maximumLength = 0, $default_value = null, $pattern = "//", $isPrimary = false, $prepare_type = "s") {
			parent::__construct($name, $db_name, default_value: $default_value, isPrimary: $isPrimary);
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
		function __construct($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			parent::__construct($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary, "s");
			$this->type = "text";
    	}
	}
	class NUMERIC extends PatternData
	{
		function __construct($name, $db_name, $maximumLength = 0, $default_value = 0, $pattern = "//", $isPrimary = false)
		{
			parent::__construct($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary, "i");
			$this->type = "number";
    	}
	}
	class DATE extends PatternData
	{
		function __construct($name, $db_name, $default_value = "1970-01-01")
		{
			parent::__construct($name, $db_name, 12, $default_value, "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/", false, "s");
			$this->type = "date";
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
		function __construct($name, $db_name, $size_limit = 0)
		{
			parent::__construct($name, $db_name, default_value: "NULL", prepare_type: "b");
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
    	function GetPostValue($default = null)
    	{
    		return "NULL";
    	}
	}

	class Scheme extends ArrayObject
	{
		public $table_name;

		static function Create($table_name, $blueprint_func)
		{
			$newScheme = new Scheme();
			$newScheme->table_name = $table_name;
			$blueprint_func($newScheme);
			return $newScheme;
		}

		// Returns primary colume name
		function FindPrimary()
		{
			foreach ($this as $key => $value) {
				if($value->isPrimary)
				{
					return $value;
				}
			}
			echo "Primary not found";
		}

		//Generates a array like [`name_1`=?, `name_2`=?...]
		function GetUpdateSlots()
		{
			$output = array();
			foreach ($this as $key => $value) {
				array_push($output, $value->GetUpdateSlot());
			}
			return $output;
		}

		function GetPostValues()
		{
			$output = array();
			foreach ($this as $key => $value) {
				if($post_data = $value->GetPostValue($value->default_value))
				{
					array_push($output, $post_data);
					// $output[$value->db_name] = $post_data;
				}
				else
				{
					return false;
				}
			}
			return $output;
		}

		function GetPrepareTypes()
		{
			$output = array();
			foreach ($this as $key => $value) {
				array_push($output, $value->prepare_type);
			}
			return $output;
		}

		function GetDBNames()
		{
			$output = array();
			foreach ($this as $key => $value) {
				array_push($output, $value->db_name);
			}
			return $output;
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

		function Update($conn)
		{
			if(!empty($conn))
			{
				$stmt = $conn->stmt_init();

				$primary_name = $this->FindPrimary()->db_name;

				$placeholders = $this->GetUpdateSlots();
				$placeholders = implode(", ", $placeholders);

				if(!$origin = TryGetPost("origin"))
				{
					exit("origin not set");
				}

				$sql = "UPDATE `".$this->table_name."` SET ".$placeholders." WHERE `".$primary_name."` = ?";
				echo $sql;
				if($stmt->prepare($sql))
				{
					echo "Prepare successfully";
				}
				else
				{
					echo "Prepare Failed";	
				}

				$prepare_types = $this->GetPrepareTypes();
				array_push($prepare_types, $this->FindPrimary()->prepare_type);
				$prepare_types = implode("", $prepare_types);


				if($post_values = $this->GetPostValues())
				{
					array_push($post_values, $origin);
					$stmt->bind_param($prepare_types, ...$post_values);
				}
				else
				{
					exit("not enough arguments");
				}
				
				if($stmt->execute())
				{
					echo "Update success";
					return true;
				}
				else
				{
					echo "Update failed";
					return false;
				}
			}
			else
			{
				exit("Connection not available");
			}
		}

		function Insert($conn)
		{
			if(!empty($conn))
			{
				$stmt = $conn->stmt_init();
				$names_arr = $this->GetDBNames();
				$names = implode("`, `", $names_arr);

				$placeholders = array();
				foreach ($names_arr as $name) {
					array_push($placeholders, "?");
				}
				$placeholders = implode(", ", $placeholders);

				$sql = "INSERT INTO ".$this->table_name." (`".$names."`) VALUES (".$placeholders.")";
				if($stmt->prepare($sql))
				{
					echo "Prepare successfully";
				}
				else
				{
					echo "Prepare Failed";	
				}

				$prepare_types = implode("", $this->GetPrepareTypes());

				if($post_values = $this->GetPostValues())
				{
					$stmt->bind_param($prepare_types, ...$post_values);
				}
				else
				{
					exit("not enough arguments");
				}
				
				if($stmt->execute())
				{
					echo "Insert success";
					return true;
				}
				else
				{
					echo "Insert failed";
					echo("Error description: " . $mysqli -> error);
					return false;
				}
			}
			else
			{
				exit("Connection not available");
			}
		}

		function Select($conn, $primary_match = null, $other_matches = null, $limit = 10, $offset = 0)
		{
			if(!empty($conn))
			{
				$primary_name = $this->FindPrimary()->db_name;
				$stmt = $conn->stmt_init();

				if(empty($primary_match)) // Select All
				{
					$sql = "SELECT * FROM `".$this->table_name."` LIMIT $limit OFFSET $offset";
					$stmt->prepare($sql);
				}
				else
				{
					$sql = "SELECT * FROM `".$this->table_name."` WHERE `$primary_name` LIKE ? LIMIT $limit OFFSET $offset";
					echo $sql;
					if(!$stmt->prepare($sql))
					{
						echo "Prepare Error: ".$sql;
						return null;
					}
					$primary_match = "%$primary_match%";
					$stmt->bind_param("s", $primary_match);
				}

				$stmt->execute();
				$result = $stmt->get_result();

				$buffer = $this->GenBuffer();

				$stmt->bind_result(...array_values($buffer));

				$output = $result->fetch_all(MYSQLI_ASSOC);
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

		function IMAGE($name, $db_name, $size_limit = 0)
		{
			return $this->InsertNewVariable(new Image($name, $db_name), $size_limit);
		}

		function NUMERIC($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			return $this->InsertNewVariable(new NUMERIC($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary));
		}

		function DATE($name, $db_name, $default_value = "1970-01-01")
		{
			return $this->InsertNewVariable(new DATE($name, $db_name, $default_value));
		}

		function VARCHAR($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			return $this->InsertNewVariable(new VARCHAR($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary));
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