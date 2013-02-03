<?php 
/**
 * GESTION DES DATES
 */
 class Date
 {
 	private $Session;

 	public function __construct($session)
 	{
 		$this->session = $session;
 	}

 	public static function MysqlNow(){

 		return self::timestamp2MysqlDate(time());
 	}

 	public static function timestamp2MysqlDate($timestamp){

 		return date('Y-m-d H:i:s',$timestamp);
 	}

 	// public function month($num){

 	// 	$array = array('fr' => array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre')
 	// 			);

 	// 	return $array[$this->session->getLang()][$num - 1];

 	// }
 } ?>