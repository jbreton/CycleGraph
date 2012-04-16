<?php

namespace CycleGraph\RideLog;

class Parser {
	private $_adaptor;
	private $_em;
	
	public function __construct(\Doctrine\ORM\EntityManager $em, $adaptor='\CycleGraph\RideLog\Adaptor\CyclemeterCSV') {
		$this->_em = $em;
		
		if(is_string($adaptor)) {
			$reflection = new \ReflectionClass($adaptor);
		
			if($reflection->implementsInterface('\CycleGraph\RideLog\Adaptor')) {
				$this->_adaptor = $reflection->newInstance();
			}
			else {
				throw new \Exception($adaptor.' does not implement \CycleGraph\RideLog\Adaptor');
			}
		}
		else if($adaptor instanceof \CycleGraph\RideLog\Adaptor) {
			$this->_adaptor = $adaptor;
		}
		else {
			throw new \Exception('The given adaptor must implement \CycleGraph\RideLog\Adaptor');
		}
	}
	
	public function ParseFile($filename) {
		if(!file_exists($filename)) {
			throw new \Exception('File does not exists : '.$filename);
		}
		
		if($this->_adaptor->ParseFile($filename)) {
			$ride = $this->_adaptor->GetRideEntity();
			$this->_em->persist($ride);

			while($point = $this->_adaptor->GetPointEntity()) {
				$this->_em->persist($point);
			}
			
			$this->_em->flush();
		}
		else {
			throw new Exception($this->_adaptor->GetParseError());
		}
	}
}