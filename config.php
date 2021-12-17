<?php
ini_set('display_errors', 1);

define('DIR_BASE',      dirname( __FILE__ ) . '/');

define('DIR_MDL',    DIR_BASE . 'modules/');
define('DIR_INC',    DIR_BASE . 'includes/');
define('DIR_VIEWS',     DIR_BASE . 'views/');
define('DIR_DATA',     DIR_BASE . 'database/');

define('DATA_MANIP',     DIR_DATA . 'data_manipulation.mdl.php');
define('DATA_CUSTOMER',     DIR_DATA . 'customer_data.php');

define('DIR_CUSTOMER',    DIR_MDL . 'customer_data/');
define('DIR_PRODUCT',    DIR_MDL . 'product_date/');

define('DB_CONNECT',    DIR_INC . 'db_connect.inc.php');

define('VIEW_HEADER',   DIR_VIEWS . 'header.php');
define('VIEW_NAVIGATION',   DIR_VIEWS . 'navigation.php');
define('VIEW_FOOTER',   DIR_VIEWS . 'footer.php');


require_once(DIR_MDL."tool.mdl.php");
require_once(DATA_MANIP);
require_once(DATA_CUSTOMER);
?>