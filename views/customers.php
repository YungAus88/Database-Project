<?php
ini_set('display_errors', 1);
require_once "../config.php";

require_once DB_CONNECT;
include_once VIEW_HEADER;

$offset = TryGetValue("offset", "0");

$sql = "SELECT * FROM `customer_data` WHERE `name` LIKE ? LIMIT 10 OFFSET $offset";
$stmt = $conn->stmt_init();
$stmt->prepare($sql);

$customer_name = $customer_data[0]->get_post("%");

$stmt->bind_param("s", $customer_name);
$stmt->execute();
$stmt->store_result();
$result = $stmt->get_result();


// $array_of_result = array();
// foreach ($customer_data as $data) {
// 	array_push($array_of_result, $data->stored_value);
// }

// $stmt->bind_result(...$array_of_result);

$stmt->bind_result(
	$customer_data[0]->stored_value, 
	$customer_data[1]->stored_value,
	$customer_data[2]->stored_value,
	$customer_data[3]->stored_value,
	$customer_data[4]->stored_value,
	$customer_data[5]->stored_value,
	$customer_data[6]->stored_value,
	$customer_data[7]->stored_value,
	$customer_data[8]->stored_value,
);

echo "$stmt->num_rows rows found.\n";

?>

<!DOCTYPE html>
<html>
<head>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>customers</title>
</head>
<body>
	<table>
		<form>
			<tr>
				<td></td>
				<?php echo create_headers($customer_data); ?>
			</tr>
			<tr>
				<td><input type="submit" method="post" action="./"></td>
				<?php echo create_inputs($customer_data); ?>
			</tr>
			<tr>
				<td></td>
				<?php 
					while($stmt->fetch())
					{
						echo $customer_data[0]->stored_value;
						echo create_data($customer_data);
					}
				?>
			</tr>
		</form>
	</table>
</body>
</html>