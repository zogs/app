<?php

class PagesController extends Controller {


		public function __construct( $request = null ) {

		parent::__construct($request);

		$this->CookieLocation = new Cookie('Location',60*60*24*30,true);
		$this->CookieSport = new Cookie('Sport',60*60*24*30,true);
		}



		public function home(){


			$this->loadModel('Worlds');
			$this->loadJS = 'js/jquery/jquery.autocomplete.js';

			if($this->request->post()){

				$this->CookieLocation->write(array(
													'CC1'=>$this->request->post('CC1'),
													'ADM1'=>$this->request->post('ADM1'),
													'ADM2'=>$this->request->post('ADM2'),
													'ADM3'=>$this->request->post('ADM3'),
													'ADM4'=>$this->request->post('ADM4'),
													'city'=>$this->request->post('city')
												)
											);
				$this->CookieSport->write($this->request->post('sports'));
			}

			
			$d = '';
			$this->set($d);

		}

		//===================
		// Permet de rentre une page
		// $param $id id du post dans la bdd
		public function view($id){

				//On charge le model
				$this->loadModel('Posts');
				//On utlise la methode findFirst du model
				$page = $this->Posts->findFirst(array(
					'conditions'=> array('id'=>$id,'online'=>1,'type'=>'page') //En envoyant les parametres
					));
				//Si le resultat est vide on dirige sur la page 404
				if(empty($page)){
					$this->e404('Page introuvable');
				}
				//Atttribution de l'objet $page a une variable page
				$this->set('page',$page);

				
		}

		//Permet de recuperer les pages pour le menu
		public function getMenu(){

			$this->loadModel('Posts');
			return $this->Posts->find(array(
				'conditions'=> array('online'=>1,'type'=>'page')

				));
		}
}

?>