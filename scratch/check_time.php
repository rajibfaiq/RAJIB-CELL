<?php
define('FCPATH', 'c:\\Users\\Rajib\\SIMRS\\public\\');
require 'c:\\Users\\Rajib\\SIMRS\\app\\Config\\Paths.php';
$paths = new Config\Paths();
require 'c:\\Users\\Rajib\\SIMRS\\vendor\\codeigniter4\\framework\\system\\Test\\bootstrap.php';

$db = \Config\Database::connect();
$mysqlTime = $db->query("SELECT NOW() as now, CURDATE() as curdate").getRow();
echo "PHP Time: " . date('Y-m-d H:i:s') . " (Timezone: " . date_default_timezone_get() . ")\n";
echo "MySQL Time: " . $mysqlTime->now . " (CURDATE: " . $mysqlTime->curdate . ")\n";
