<?php
ini_set('display_errors', 1);
require_once "../../config.php";
require_once DB_CONNECT;

store_posts($customer_data, "%");

$sql = "INSERT INTO `customer_data`(`name`, `id`, `phone`, `address`, `age`, `profession`, `created_at`, `photo`, `consumption_state`) VALUES (?,?,?,?,?,?,?,?,?)";
$stmt = $conn->stmt_init();
$stmt->prepare($sql);

$customer_name = $customer_data[0]->get_post("%");

echo get_stored_values($customer_data)[0];

$stmt->bind_param("ssssdssbs", ...get_stored_values($customer_data));
if(!$stmt->execute()) {
    echo "Error: " . mysqli_error($conn);
}else{
    echo "Success";
}
?>