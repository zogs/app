<?php

class Request{

	public static $url; //url appelé par l'utilisateur
	public static $page = 1; //Pagination
	public static $prefix =false; //Prefixes
	public static $data = false; //Donnees de formulaire
	public static $get = false;
	public static $controller;
	public static $action;
	public static $params;	

	//Permet de récupérer la requete url demandé
	public static function init(){
		
		self::$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
		//self::$url = str_replace(BASE_URL."/", "", 
		//$_SERVER['REQUEST_URI']); //Recuperation du PATH_INFO 

		//Récuperation des données GET dans un objet
		if(!empty($_GET)){

			self::$get = new stdClass();
			foreach ($_GET as $k => $v) {
				// if(!is_numeric($v)){
				// 	if(is_array($v)){
				// 		$arr = array();
				// 		foreach ($v as $key => $value) {
				// 			$arr[$key] = mysql_real_escape_string($value);
				// 		}
				// 		$v = $arr;
				// 	}
				// 	else
				// 		$v = mysql_real_escape_string($v);
				// }
				self::$get->$k = $v;
			}						

		}
			
		if(!isset(self::$get->page) || self::$get->page <=0 ){
			 self::$page = 1;
		}
		else {
			self::$page = round(self::$get->page);
		}


		//Récuperation des données POST dans un objet
		if(!empty($_POST)){
			self::$data = new stdClass();
			foreach ($_POST as $k => $v) {
				// if(!is_numeric($v)){
				// 	if(is_array($v)){
				// 		$arr = array();
				// 		foreach ($v as $key => $value) {
				// 			$arr[$key] = mysql_real_escape_string($value);
				// 		}
				// 		$v = $arr;
				// 	}
				// 	else
				// 		$v = mysql_real_escape_string($v);
				// }
				self::$data->$k = $v;
			}

		}

		

	}

	//Renvoi le parametre GET demandé ou renvoi false
	public static function get($param = null){	
		
		if($param){	
			if(isset(self::$get->$param)){
				return self::$get->$param;
			}
			else return false;
		}
		else {
			if(!empty($_GET)){
				return self::$get;
			} 
			else {
				return false;
			}
		} 
	}

	public static function post($param = null){

		if($param){
			if(isset(self::$data->$param)){
				return self::$data->$param;
			}
			else return false;
		}
		else {
			if(!empty($_POST)){
				return self::$data;
			}
			else {
				return false;
			}
		}
	}
}