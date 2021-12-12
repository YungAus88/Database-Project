<?php
ini_set('display_errors', 1);
require_once "../../config.php";
require_once DB_CONNECT;

store_posts($customer_data, "%");

$sql = "UPDATE `customer_data` SET `name`=?,`id`=?,`phone`=?,`address`=?,`age`=?,`profession`=?,`created_at`=?,`photo`=?,`consumption_state`=? WHERE `id` = ?";
$stmt = $conn->stmt_init();
$stmt->prepare($sql);

if(isset($_POST['original_id']))
{
	$original_id = $_POST['original_id'];
}
else
{
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}

$stmt->bind_param("ssssdssbss", ...get_stored_values($customer_data), TryGetPost("original_id"));
if(!$stmt->execute()) {
    echo "Error: " . mysqli_error($conn);
}else{
    echo "Success";
}
?>
