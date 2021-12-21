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
	    public $scheme_instance;

    	function __construct($name, $db_name, $default_value = null, $prepare_type = "s") {
        	$this->name = $name;
        	$this->db_name = $db_name;
        	$this->default_value = $default_value;
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

    	function input_node($conn, $use_default = "default", $width = 166, $dynamic_width = true, $nullable = true, $other = "")
    	{
    		$error_class = strcmp(TryGetValue("error"), $this->name) == 0 ? "invalid" : "";
			$value = "";
			if($use_default == "default")
			{
				$value = "value='".$this->default_value."'";
			}
			else if($use_default == "post")
			{
				$value = "value='".$this->GetPostValue()."'";
			}
			$style_attr = $dynamic_width ? "" : "style='width:".$width."px'";
			$required_attr = $this->notNull ? "" : ($nullable ? "" : "required");
    		return "<input ".$this->AttrByDBName("id")." $value ".$this->AttrByType()." $style_attr ".$this->AttrByDBName()." ".$this->AttrByName("placeholder")." class='$error_class' $other $required_attr>";
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
		public $soft_pattern = "//";
		function __construct($name, $db_name, $maximumLength = 0, $default_value = null, $pattern = "//", $soft_pattern = "//", $prepare_type = "s") {
			parent::__construct($name, $db_name, default_value: $default_value);
        	$this->name = $name;
        	$this->maximumLength = $maximumLength;
        	$this->pattern = $pattern;
        	$this->soft_pattern = $soft_pattern;
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
    	function input_node($conn, $use_default = "default", $width = 166, $dynamic_width = true, $nullable = true, $other = "")
    	{
    		$max_attr = " maxlength='".$this->maximumLength."' ";
    		$pattern_attr = strlen($this->pattern) > 2 ? " pattern='".substr($this->pattern, 1, strlen($this->pattern) - 2)."' " : "";
    		$attrs = $max_attr.$pattern_attr;
    		return parent::input_node($conn, $use_default, $this->maximumLength == 0 ? $width : $this->maximumLength * 8, $dynamic_width, $nullable, $attrs.$other);
    	}
	}

	class VARCHAR extends PatternData
	{
		function __construct($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $soft_pattern = "//")
		{
			parent::__construct($name, $db_name, $maximumLength, $default_value, $pattern, $soft_pattern, "s");
			$this->type = "text";
    	}
	}
	class NUMERIC extends PatternData
	{
		public $min = null;
		public $max = null;
		function __construct(
			$name,
			$db_name,
			$maximumLength = 0,
			$default_value = 0,
			$pattern = "//",
			$soft_pattern = "//",
			$min = null,
			$max = null
		)
		{
			parent::__construct($name, $db_name, $maximumLength, $default_value, $pattern, $soft_pattern, "i");
			$this->type = "number";
			$this->min = $min;
			$this->max = $max;
    	}
    	function input_node($conn, $use_default = "default", $width = 166, $dynamic_width = true, $nullable = true, $other = "")
    	{
    		$min_attr = $this->min != null ? "min='".$this->min."'" : $this->min;
    		$max_attr = $this->max != null ? "max='".$this->max."'" : $this->min;
    		return parent::input_node($conn, $use_default, $width, $dynamic_width, $nullable, $other.$min_attr.$max_attr);
    	}
	}
	class DATE extends PatternData
	{
		function __construct($name, $db_name, $default_value = "1970-01-01")
		{
			parent::__construct($name, $db_name, 12, $default_value, "/^\d{4}[\-\/\s]?((((0[13578])|(1[02]))[\-\/\s]?(([0-2][0-9])|(3[01])))|(((0[469])|(11))[\-\/\s]?(([0-2][0-9])|(30)))|(02[\-\/\s]?[0-2][0-9]))$/", null, false, "s");
			$this->type = "date";
    	}
    	function input_node($conn, $use_default = "default", $width = 166, $dynamic_width = true, $nullable = true, $other = "")
    	{
    		return parent::input_node($conn, $use_default, width: 200, dynamic_width: $dynamic_width, nullable: $nullable);
    	}
	}

	class SLTVARCHAR extends PatternData
	{
		public $selections = array();
		function __construct($name, $db_name, $maximumLength = 0, $selections)
		{
			parent::__construct($name, $db_name, $maximumLength, "", "", "", "s");
			$this->type = "text";
			$this->selections = $selections;
    	}
    	function input_node($conn, $use_default = "default", $width = 166, $dynamic_width = true, $nullable = true, $other = "")
    	{
    		$current_selection = $other != null ? $other : $this->selections;
    		$head_node = "<select name='".$this->db_name."' id='".$this->db_name."'>";
    		$options = array();
    		foreach ($current_selection as $value) {
    			array_push($options, "<option value='".$value."'>".$value."</option>");
    		}
    		$options = implode("", $options);
			$tail_node = "</select>";
    		return $head_node.$options.$tail_node;
    	}
	}
	class FOREIGNKEY extends SLTVARCHAR
	{
		public $table;
		public $col;
		function __construct($name, $db_name, $maximumLength = 0, $table, $col)
		{
			parent::__construct($name, $db_name, $maximumLength, "", "", "", "s");
			$this->type = "text";
			$this->table = $table;
			$this->col = $col;
    	}
    	function input_node
    	(
    		$conn, 
    		$use_default = "default", 
    		$width = 166, 
    		$dynamic_width = true, 
    		$nullable = true, 
    		$other = ""
    	)
    	{
    		$result = $this->scheme_instance->Select($conn, [$this->col], limit: 0, table_name: $this->table);
    		$selections = array();
    		foreach ($result as $row_index => $row)
    		{
    			foreach ($row as $key => $value)
    			{
    				array_push($selections, $value);
    			}
    		}
    		return parent::input_node($conn, $use_default, $width, $dynamic_width, $nullable, $selections);
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
    		if(isset($_FILES["photo"]))
    		{
    			return basename($_FILES["photo"]["name"]);
    		}
    		return $default == null ? "NULL" : $default;
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

		function GetPostValues($use_key = false, $default = null)
		{
			$output = array();
			foreach ($this as $key => $value) {
				if($post_data = $value->GetPostValue($default == null ? $value->default_value : $default))
				{
					if($use_key)
					{
						$output[$value->db_name] = $post_data;
					}
					else
					{
						array_push($output, $post_data);
					}
				}
				else
				{
					echo $key;
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

		function GenBuffer($cols = null)
		{
			$output = array();
			foreach ($this as $key => $value) {
				if($cols == null || in_array($value->db_name, $cols))
				{
					$output[$key] = null;
				}
			}
			return $output;
		}

		function Average($conn, $col, $where = null)
		{
			if(!empty($conn))
			{
				$stmt = $conn->stmt_init();
				if($where == null)
				{
					$where_statement = "";
				}
				$sql = "SELECT  CAST(AVG($col) AS DECIMAL(10,2)) as count FROM ".$this->table_name." $where_statement;";
				$result=mysqli_query($conn, $sql);
				$data=mysqli_fetch_assoc($result);
				return $data['count'];
			}
		}

		function CountRows($conn, $col, $where = null)
		{
			if(!empty($conn))
			{
				$stmt = $conn->stmt_init();
				if($where == null)
				{
					$where_statement = "";
				}
				$sql = "SELECT COUNT($col) as count FROM ".$this->table_name." $where_statement;";
				$result=mysqli_query($conn, $sql);
				$data=mysqli_fetch_assoc($result);
				return $data['count'];
			}
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
					echo $sql;
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
					echo("Error description: " . $conn->error);
					return false;
				}
			}
			else
			{
				exit("Connection not available");
			}
		}

		function Select($conn, $cols = "*", $where_statements = null, $limit = 10, $offset = 0, $table_name = null)
		{
			$table_name = $table_name == null ? $this->table_name : $table_name;
			return Select($conn, $table_name, $this->GenBuffer($cols == "*" ? null : $cols), $cols, $where_statements, $limit, $offset);
		}

		private function InsertNewVariable($var)
		{
			$this->offsetSet($var->name, $var);
			$var->scheme_instance = $this;
			return $this->offsetGet($var->name);
		}

		function IMAGE($name, $db_name, $size_limit = 0)
		{
			return $this->InsertNewVariable(new Image($name, $db_name), $size_limit);
		}

		function NUMERIC($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false, $min = null, $max = null)
		{
			return $this->InsertNewVariable(new NUMERIC($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary, $min, $max));
		}

		function DATE($name, $db_name, $default_value = "1970-01-01")
		{
			return $this->InsertNewVariable(new DATE($name, $db_name, $default_value));
		}

		function VARCHAR($name, $db_name, $maximumLength = 0, $default_value = "", $pattern = "//", $isPrimary = false)
		{
			return $this->InsertNewVariable(new VARCHAR($name, $db_name, $maximumLength, $default_value, $pattern, $isPrimary));
		}

		function SLTVARCHAR($name, $db_name, $maximumLength = 0, $selections)
		{
			return $this->InsertNewVariable(new SLTVARCHAR($name, $db_name, $maximumLength, $selections));
		}
		function FOREIGNKEY($name, $db_name, $maximumLength = 0, $table, $col)
		{
			return $this->InsertNewVariable(new FOREIGNKEY($name, $db_name, $maximumLength, $table, $col));	
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

		function CreateInputs($conn, $keys = null, $use_default = "default", $dynamic_width = true, $end = "", $nullable = false)
		{
			$output = "";

			foreach ($this as $key => $col) {
				if($keys == null || in_array($col->db_name, $keys))
				{
					$output = $output."<td>".$col->input_node($conn, $use_default, dynamic_width: $dynamic_width, nullable: $nullable)."</td>".$end;
				}
				else
				{

				}
				// echo gettype($scheme);
			}

			return $output;
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

	function Select($conn, $table_name, $buffer, $cols = "*", $where_statements = null, $limit = 10, $offset = 0)
	{
		if(!empty($conn))
		{
			$stmt = $conn->stmt_init();

			$sql_cols = $cols;
			if($cols != "*")
			{
				$sql_cols = implode(", ", $cols);
			}
			$limit = $limit > 0 ? "LIMIT $limit OFFSET $offset" : "";

			$sql_where = array();
			$where_value_buffer = array();
			$sql_params = "";
			if($where_statements != null)
			{
				foreach ($where_statements as $key => $value)
				{
					array_push($sql_where, "$key LIKE ?");
					array_push($where_value_buffer, "%$value%");
					$sql_params = $sql_params."s";
				}

				$sql_where = implode(" AND ", $sql_where);

				$sql = "SELECT $sql_cols FROM `".$table_name."` WHERE $sql_where $limit";
				
				if(!$stmt->prepare($sql))
				{
					echo "Prepare Error: ".$sql;
					return null;
				}
				$stmt->bind_param($sql_params, ...$where_value_buffer);
			}
			else // Select All
			{
				$sql = "SELECT $sql_cols FROM `".$table_name."` $limit";
				$stmt->prepare($sql);
			}
			$stmt->execute();
			$result = $stmt->get_result();


			if($stmt->bind_result(...array_values($buffer)))
			{
				$output = $result->fetch_all(MYSQLI_ASSOC);
				return $output;
			}
			else
			{
				echo("Error description: " . $conn->error);
			}
		}
		else
		{
			exit("Connection not available");
		}
	}

	class Database extends ArrayObject
	{
		function Create($scheme)
		{
			$this[$scheme->table_name] = $scheme;
			return $scheme;
		}
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
	function create_inputs($conn, $scheme, $keys = null, $use_default = "default", $dynamic_width = true, $end = "", $nullable = false)
	{
		$output = "";

		foreach ($scheme as $key => $col) {
			if($keys == null || in_array($col->db_name, $keys))
			{
				$output = $output."<td>".$col->input_node($conn, $use_default, dynamic_width: $dynamic_width, nullable: $nullable)."</td>".$end;
			}
			else
			{

			}
			// echo gettype($scheme);
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