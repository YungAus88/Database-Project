<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="stylesheet" type="text/css" href="../CSS/style.css"/>
	<title>Account</title>
</head>
<?php
	ini_set('display_errors', 1);
	require_once "../config.php";

	require_once DB_CONNECT;
	require_once DATA_CUSTOMER;
	include_once VIEW_HEADER;

	$selection = TryGetValue("selection", null);
	if($selection == null)
	{
		$selection = TryGetPost($accounts_scheme->FindPrimary()->db_name, "");
	}

	$modifying = TryGetValue("modifying", null);
	$modifying = $modifying != null;


	$offset = TryGetValue("offset", "0");

	if(!empty($selection))
	{
		echo "Searching $selection in ".$accounts_scheme->FindPrimary()->db_name;
		$results = $accounts_scheme->Select($conn, $selection);
	}
	else
	{
		echo "selection not set";
		$results = $accounts_scheme->Select($conn, offset: $offset);
	}
?>
<script type="text/javascript">

	var primary = <?php echo json_encode($accounts_scheme->FindPrimary()->db_name); ?>;
	var primary_value = null;
	var db_names = <?php echo json_encode($accounts_scheme->GetDBNames()); ?>;
	console.log(primary);

	function openForm() {
	  document.getElementById("insert-form-container").style.display = "block";
	}

	function closeForm() {
	  document.getElementById("insert-form-container").style.display = "none";
	}

	function openUpdateForm(row_id)
	{
		document.getElementById("update-form-container").style.display = "block";

		var form = document.getElementById("update-form-container").children[0];

		var element = document.getElementById(row_id);
		for(var col_node=element.firstChild; col_node!==null; col_node=col_node.nextSibling)
		{
			if(col_node.id == primary)
			{
				primary_value = col_node.innerHTML;
			}
			for(var i=0; i < form.children.length; i++)
			{
				if(form.children[i].id == col_node.id)
				{
					if(db_names.includes(col_node.id))
					{
						form.children[i].value = col_node.innerHTML;
					}
				}
				if(form.children[i].id == "origin")
				{
					form.children[i].value = primary_value;
				}
			}
		}
		console.log(primary_value);
	}

	function closeUpdateForm()
	{
		document.getElementById("update-form-container").style.display = "none";
	}
</script>
<body>
	<button class="open-button" onclick="openForm()">Open Form</button>
	<table>
		<form method="post" action="customers.php" class="form-container">
			<tr>
				<td></td>
				<?php echo create_headers($accounts_scheme); ?>
			</tr>
			<tr>
				<td><input type="submit" ></td>
				<?php echo create_inputs($accounts_scheme); ?>
			</tr>
		</form>
		<?php
			if(!empty($results))
			{
				$row_index = 0;
				foreach ($results as $result)
				{
					$row_index += 1;
					echo "<tr id='$row_index'><td>$row_index</td>";
					foreach ($result as $key => $value)
					{
						echo "<td id='".$key."'>".(string)$value."</td>";
					}
					echo "<td><button onClick='openUpdateForm($row_index)'>Update</button></td>";
					echo '</tr>';
				}
			}
		?>
	</table>
	<br>
	<div class="form-popup" id="update-form-container">
		<form method="post" action="../modules/update.php" class="form-container">
			<h1>修改帳單</h1>

			<?php echo create_inputs($accounts_scheme); ?>

			<!-- Create a hidden primary value for seraching the original primary -->
			<?php echo "<input type='hidden' name='origin' id='origin' value=''>"; ?>
			<input type="hidden" name="table" value="accounts_scheme"/>

			<button type="submit" class="btn">確認修改</button>
			<button type="button" class="btn cancel" onclick="closeUpdateForm()">Close</button>
		</form>
	</div>
	<div class="form-popup" id="insert-form-container">
		<form method="post" action="../modules/insert.php" class="form-container">
			<h1>新增帳單</h1>

			<?php 
				$input_keys = ["id","value","value_awaiting","due_to_date","customer_name","active"];
				echo create_inputs($accounts_scheme, keys: $input_keys); 
			?>

			<input type="hidden" name="table" value="accounts_scheme"/>
			<button type="submit" class="btn">新增</button>
			<button type="button" class="btn cancel" onclick="closeForm()">Close</button>
		</form>
	</div>
</body>
</html>