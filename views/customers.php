<?php
ini_set('display_errors', 1);
require_once "../config.php";

require_once DB_CONNECT;
require_once DATA_CUSTOMER;
include_once VIEW_HEADER;

$selection = TryGetValue("selection", "");
$offset = TryGetValue("offset", "0");

if(!empty($selection))
{
	echo "using selection";
	$results = $customer_scheme->Select($conn, $selection);
}
else
{
	echo "selection not set";
	$results = $customer_scheme->Select($conn, $offset = $offset);
}

$offset = TryGetValue("offset", "0");
?>

<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>customers</title>
	<style type="text/css">
		table, th, td {
		  /*border: 1px solid black;*/
		  border-radius: 10px;
		}
		th, td {
		  background-color: #96D4D4;
		  border-style: inset;
		}
	</style>
</head>
<body>
	<table>
		<form method="post" action="customers.php">
			<tr>
				<td></td>
				<?php echo create_headers($customer_scheme); ?>
			</tr>
			<tr>
				<td><input type="submit" ></td>
				<?php echo create_inputs($customer_scheme); ?>
			</tr>
			<?php
				if(!empty($results))
				{
					foreach ($results as $result)
					{
						echo '<tr><td></td>';
						foreach ($result as $key => $value) {
							echo "<td>".(string)$value."</td>";
						}
						// echo $value;
						// echo create_data($customer_data);
						echo '</tr>';
					}
				}
			?>
		</form>
	</table>
</body>
</html>