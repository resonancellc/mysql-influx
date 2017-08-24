<?php 

namespace Vorbind\MysqlInflux;

use Vorbind\MysqlInflux\Reader\Reader;
use Vorbind\MysqlInflux\MiConnection;
use \PDO;
use Vorbind\InfluxAnalytics\Analytics;
use Vorbind\InfluxAnalytics\Mapper\AnalyticsMapper;

use \Exception;

/**
*  MysqlInflux import
*  Import data from mysql to influx db
* 
*  @author sasa.rajkovic
*/
class Import implements ImportInterface {

    /**
     * Run import data from mysql to influx
     */
    public function run($config) {
      try {

        // read config for metrix and dbs
        $reader = new Reader();
        $reader->read($config);
        $metrics = $reader->getMetrics();
        $db = $reader->getDb();
            
        // make connections for mysql and influxdb
        $conn = new MiConnection($db);        
        $mdb = $conn->getMysqlAdapter();
        $idb = $conn->getInfluxAdapter();
        
        // instantiate analytics
        $analytics = new Analytics(new AnalyticsMapper($idb));
        
        // Do import  
        foreach($metrics as $metric => $metricItem) {   
          if (!isset($metricItem["influx"]) || !isset($metricItem["influx"]["tags"]) 
              || !isset($metricItem["mysql"]) || !isset($metricItem["mysql"]["query"]) ) {
            throw new Exception("Configuration is bad, missing 'influx' or 'influx[tags]' or 'mysql' or 'mysql[query]'!");
          }

          $offset = 0;
          $limit = 100;
        
          while (true) {
            $query = sprintf($metricItem["mysql"]["query"], $limit, $offset);
            $stmt = $mdb->query($query);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($rows) <= 0) {
               break;
            }
            
            foreach($rows as $row) {
              $input = array_merge($metricItem["influx"]["tags"],$row);
              foreach($input as $key => $val) {
                if(!isset($val)) {
                   throw new Exception("Configuration is bad, $key[$val] is not defined!");
                }
              }
              
              $value = $input["value"];
              $utc = $input["utc"];
              
              $tags = array_merge([], $input);
              unset($tags["value"]); 
              unset($tags["utc"]);
              
              $data = $analytics->save($metric, $tags, intval($value), $utc, "years_5");
            }

            $offset += $limit;
            sleep(1);
          }    
        }
      } catch(Exception $e) {
          printf("Error importing data:" . $e->getMessage(), PHP_EOL);
      }
    }   
}