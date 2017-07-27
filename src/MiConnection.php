<?php 

namespace Vorbind\MysqlInflux;

use Vorbind\InfluxAnalytics\Connection;
use \PDO;
use \Exception;

/**
*  Db connections 
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author sasa.rajkovic
*/
class MiConnection {

  private $idb;
  private $mdb;

  public function __construct($config = null) {
    try {

      if(!isset($config['influx']) || !isset($config['mysql']) ) {
        throw new Exception("Missing influx or db configuration!");
      }

      $influx = $config['influx'];
      $mysql = $config['mysql'];
  
      $iconn = new Connection($influx["username"],$influx["password"],$influx["host"],$influx["port"]); 
      $this->idb = $iconn->getDatabase("news");

      $mhost = $mysql["host"];
      $this->mdb = new PDO("mysql:host=$mhost;", $mysql["username"], $mysql["password"]);
      $this->mdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(Exception $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function getMysqlAdapter() {
    return $this->mdb;
  }

  public function getInfluxAdapter() {
    return $this->idb;
  }

}
