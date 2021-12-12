<?php
ini_set('display_errors', 1);
define('DIR_BASE',      dirname( __FILE__ ) . '/');
define('DIR_MDL',    DIR_BASE . 'modules/');
define('DIR_CUSTOMER',    DIR_MDL . 'customer_data/');
define('DIR_PRODUCT',    DIR_MDL . 'product_date/');
define('DIR_TABLE_CREATE',    DIR_MDL . 'data_manipulation.mdl.php');
define('DIR_INC',    DIR_BASE . 'includes/');
define('DB_CONNECT',    DIR_INC . 'db_connect.inc.php');
define('DIR_VIEWS',     DIR_BASE . 'views/');
define('VIEW_HEADER',   DIR_VIEWS . 'header.php');
define('VIEW_NAVIGATION',   DIR_VIEWS . 'navigation.php');
define('VIEW_FOOTER',   DIR_VIEWS . 'footer.php');

require_once(DIR_MDL."tool.mdl.php");
require_once(DIR_TABLE_CREATE);

$customer_data = array(
	new Data("客戶姓名", "text", "12"),
	new Data("身分證字號", "text", "10"),
	new Data("手機號碼", "text", "16"),
	new Data("住址", "text", "30"),
	new Data("年齡", "number", "4"),
	new Data("職業", "text", "12"),
	new Data("登載日期", "text", "12", "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/"),
	new Data("照片", "text", "12"),
	new Data("消費狀態", "text", "12")
);
?>