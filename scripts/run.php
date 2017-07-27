<?php

include "../vendor/autoload.php";

use Vorbind\MysqlInflux\Import;

$shortopts = "c:";  // Required value
$longopts  = array(
    "config:"     // Required value
);
$options = getopt($shortopts, $longopts);

if (count($argv) <= 1){
 print("ERROR: Config file is missing ex:  --config '/var/local/config.json' ");
 exit;
}

try {
  $import = new Import();

  $import->run($options["config"]);
  echo "\n\nFinish importing data in influxdb :)";
} catch(Exception $e) {
  printf(">>>>> ERROR:" . $e->getMessage());
}

