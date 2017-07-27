<?php 

use PHPUnit\Framework\TestCase;

// use Vorbind\MysqlInflux\Import;


class ImportTest extends TestCase {  

  /**
   * @test
   */
  public function import() {
    $data = null;
    try {
      $import = new Import();
      $import->run('/vorbind/composer/mysql-influx/src/config/config.json');
      $data = true;
      $this->assertTrue($data); 
    } catch(Exception $e) {
      printf(">>>>> ERR:" . $e->getMessage());
      $data = null;
      $this->assertNotEmpty($data);
    }
  }
}
