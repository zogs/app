<?php

class Dispatcher{

	var $request;

	function __construct() {

		//Intanciation d'un objet requete
		//$this->request = new Request();
		Request::init();

		//Appel de la class Router pour decortiquer la requete url
		Router::parse(Request::$url);

		//Appel de la methode loadController
		$controller = $this->loadController();
		$action = Request::$action;

		//Si il ya un prefix on ajoute le prefixe a l'action
		if(Request::$prefix){
			$action = Request::$prefix.'_'.$action;
		}
		
		if(!in_array($action,array_diff(get_class_methods($controller),get_class_methods('Controller')))){ //Si la methode demandé n'est pas une methode du controlleur on renvoi sur error()
			$this->error("Le controller ".Request::$controller." n'a pas de méthode ".$action);
		}
		
		//Appel de la methode demandé sur le controller demandé
		call_user_func_array(array($controller,$action),Request::$params);

		//Appel le rendu du controlleur Auto rendering
		$controller->render($action);

	}

 
	// Permet d'inclure le bon controlleur
	function loadController() {


		//nom du controller
		$name = ucfirst(Request::$controller).'Controller'; //On recupere le nom du controller ( en lui mettant une majuscule ^^)

		//autoload du controller
		$controller =  new $name(); //retourne une instance du bon controleur ( representé par le $name ! )

		return $controller;
	}

	//Renvoi un controlleur error
	function error($message){

		$controller = new Controller();
		$controller->e404($message);

	}
}