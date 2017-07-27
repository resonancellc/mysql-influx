<?php 

namespace Vorbind\MysqlInflux\Reader;

use \Exception;
/**
*  MysqlInflux reader
*  Read data from config
* 
*  @author sasa.rajkovic
*/
class Reader implements ReaderInterface {
   
  /**
   * Metrics
   * @var Array
   */
	protected $metrics;

  /**
   * Db
   * @var Array
   */
  protected $db;

    /**
     * Read reader
     */
    public function read($config = null) {
   		try {
        if (null == $config) {
          throw new Exception("Config metrics.json is missing.");
        }  	
	      $configJson = file_get_contents($config);
        $configArray = json_decode($configJson, true);
       
        if(!is_array($configArray) || !$configArray['metrics']) {
          throw new Exception("Metrix is not configured!");
        }
        
        if(!$configArray['db']) {
          throw new Exception("Db is not configured!");
        }

        $this->metrics = $configArray['metrics'];
	      $this->db = $configArray['db'];

	    } catch(Exception $e) {
	    	throw new Exception("Can't read metrics json.");
	    }  
    }

    public function getMetrics() {
      return $this->metrics;
    }

    public function getDb() {
      return $this->db;
    }
}