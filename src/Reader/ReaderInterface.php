<?php 

namespace Vorbind\MysqlInflux\Reader;


interface ReaderInterface {

	public function read();

	public function getMetrics();	
	
	public function getDb();	

} 