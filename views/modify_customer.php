<?php
require_once "../config.php";
require_once DB_CONNECT;
include_once VIEW_HEADER;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<form method="post" action="../modules/customer_data/create_customer.php">
		<?php echo create_inputs($customer_data, "<br>"); ?>
		<input type="submit">
	</form>
</body>
</html>