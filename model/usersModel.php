<?php 
class UsersModel extends Model{

	public $primaryKey = 'user_id';

	public $validates = array(
		'register' => array(
			'login' => array(
				'rule'    => 'notEmpty',
				'message' => 'Vous devez choisir un pseudo'		
				),
			'email' => array(
				'rule' => 'email',
				'message' => "L'adresse email n'est pas valide"
				),
			'password' => array(
				'rule' => 'regex',
				'regex'=> '.{5,20}',
				'message' => "Votre mot de passe doit etre entre 5 et 20 caracteres"
				),
			'confirm' => array(
				'rule' => 'confirmPassword',
				'message' => "Vos mots de passe ne sont pas identiques"
				),
			'prenom' => array(
				'rule'=> 'optional',
				),
			'nom' => array(
				'rule' => 'optional',
				),
			'age' => array(
				'rules'=> array(
							array(

								'rule'=>"regex",
								'regex'=>'19[0-9]{1}[0-9]{1}',
								'message'=> "Between 1900 and 1999..."
							),
							array(
								'rule'=>'optional',								
							)
						)
				)
		),
		'account_info' => array(
			'login' => array(
				'rules'=> array(
							array(
								'rule' => 'notEmpty',
								'message' => 'Your login is empty'
									),
							array('rule' => 'regex',
								'regex'=> '.{5,30}',
								'message' => 'Login between 5 and 20 caracters'
								)
							)
				),
			'email' => array(
				'rule' => 'email',
				'message' => "L'adresse email n'est pas valide"
				)
		),
		'account_profil' => array(

		),
		'recovery_mdp' => array(
			'password' => array(
				'rule' => 'regex',
				'regex' => '.{5,30}',
				'message' => "Votre mot de passe doit etre entre 5 et 20 caracteres"
				),
			'confirm' => array(
				'rule' => 'confirmPassword',
				'message' => "Vos mots de passe ne sont pas identiques"
				)
		),
		'account_password' => array(
			'oldpassword' => array(
				'rule' => 'notEmpty',
				'message' => "Votre mot de passe doit contenir entre 5 et 20 caracteres"
				),
			'password' => array(
				'rule' => 'regex',
				'regex' => '.{5,30}',
				'message' => "Votre mot de passe doit etre entre 5 et 20 caracteres"
				),
			'confirm' => array(
				'rule' => 'confirmPassword',
				'message' => "Vos mots de passe ne sont pas identiques"
				)
		),
		'account_delete' => array(
			'password' => array(
				'rule' => 'notEmpty',
				'message' => "Enter your password"
				)
		),
		'account_avatar'=>array(
			'avatar'=>array(
						'rule'=>'file',
						'params'=>array(
							'destination'=>'media/user/avatar',
							'extentions'=>array('png','gif','jpg','jpeg','JPG','bmp'),
							'extentions_error'=>'Your avatar is not an image file',
							'max_size'=>50000,
							'max_size_error'=>'Your image is too big',
							'ban_php_code'=>true
							),
						)
			),
	);


	public function saveUser($user,$user_id = null){

		//unset action value
		if(isset($user->action)) unset($user->action);
		//set user_id for updating
		if(isset($user_id)) $user->user_id = $user_id;
		//if there is nothing to save
		$tab = (array) $user;
		if( (count($tab)==0 ) || (count($tab)==1 && isset($tab['user_id'])))
			return true;

		//if new user , check unique value
		if(!isset($user->user_id)){

			//check if login already exist
			$check = $this->findFirst(array(
											'fields'=>'user_id',
											'conditions'=>array('login'=>$user->login)
											));
			if(!empty($check)){
				$this->session->setFlash("Sorry this login is in use.","error");
				return false;
			}

			//check if mail already in use
			$checkmail = $this->findFirst(array(
												'fields'=>'mail',
												'conditions'=>array('mail'=>$user->mail)
												));
			if(!empty($checkmail)){
				$this->session->setFlash("This email is already in use. Please try to recovery your password","error");
				return false;
			}
		}		

		if($this->save($user))
			return true;
		else
			return false;

	}

	public function findUsers($req){

		$sql = 'SELECT ';
 		
 		if(isset($req['fields'])){
			if(is_array($req['fields']))
 				$sql .= implode(', ',$req['fields']);
 			else
 				$sql .= $req['fields'];
 		}
 		else $sql .= '* ';


 		$sql .= " FROM users									
 					WHERE ";


 		 $sql .= $this->sqlConditions($req['conditions']);

 		if(isset($req['order'])){
 			if($req['order'] == 'random') $sql .= ' ORDER BY rand()';
 			else $sql .= ' ORDER BY '.$req['order'];
 		}

 		if(isset($req['limit'])){
			$sql .= ' LIMIT '.$req['limit'];
 		}

 		  //debug($sql);
 		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		$results = $pre->fetchAll(PDO::FETCH_OBJ);
 		
 		$users = array();
 		foreach ($results as $user) {

 			//$user = $this->JOIN('groups',array('group_id','logo as avatar','slug'),array('user_id'=>$user->user_id),$user); 			
 			$users[] = new User($user); 
 		}

 		return $users;
	}

	public function __autoload($class){

    	echo $class;
	}

	public function findFirst($req){

		return current($this->findUsers($req));
	}


	public function findParticipations( $fields, $user_ids ){

		$results = array();

		$sql = "SELECT $fields FROM manif_participation as P 
				LEFT JOIN manif_info as M ON M.manif_id = P.manif_id
				LEFT JOIN manif_descr as D ON D.manif_id = P.manif_id AND D.lang='".$this->session->user('lang')."'
				WHERE user_id = :user_id";

		foreach ($user_ids as $user_id) {

			$pre = $this->db->prepare($sql);
			$pre->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$pre->execute();

			return $pre->fetchAll(PDO::FETCH_OBJ);
			# code...
		}


	}

	public function findUserThread($user_id){


		$sql=" SELECT P.id,
	                 'PROTEST' AS TYPE,
	                 P.date,	                 
	                 D.name as relatedName,
	                 D.manif_id as relatedID,
	                 D.slug     as relatedSlug,
	                 I.logo     as relatedLogo

        		FROM manif_participation as P
        		LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".$this->session->user('lang')."'	
        		LEFT JOIN manif_info as I ON I.manif_id=P.manif_id
           		WHERE P.user_id = $user_id
      		UNION
        --   	SELECT `id`,
	       --           'COMMENT' AS TYPE,
	       --           `date`
        --     	FROM `manif_comment`
	       --     	WHERE user_id = $user_id AND reply_to=0
      		-- UNION
       		SELECT C.id,
                	'NEWS' AS TYPE,
                  	C.date,
                  	D.name as relatedName,
                  	D.manif_id as relatedID,
                  	D.slug     as relatedSlug,
                  	I.logo     as relatedLogo
              	FROM manif_comment AS C
              	LEFT JOIN manif_participation AS P ON P.user_id = $user_id
              	LEFT JOIN manif_descr as D ON D.manif_id=P.manif_id AND D.lang='".$this->session->user('lang')."'
              	LEFT JOIN manif_info as I ON I.manif_id=P.manif_id	
             	WHERE C.context_id = P.manif_id AND C.context='manif' AND C.type='news'
			ORDER BY date DESC
			";


		$pre = $this->db->prepare($sql);
 		$pre->execute();
 		return $pre->fetchAll(PDO::FETCH_OBJ);

	}

	
}


class User {

	public function __construct( $fields ){

		foreach ($fields as $field => $value) {
			
			$this->$field = $value;
		}
	}

	public function getLogin(){

		if($this->account=='anonym') return 'anonym_'.$this->user_id;
		else return $this->login;
	}

	public function getAvatar(){

		if(isset($this->avatar)&&!empty($this->avatar)) return $this->avatar;
		else return 'img/logo.png';
	}

	public function getAge(){

		if(isset($this->age)&&!empty($this->age)) return $this->age;
		else return 0;
	}

}
 ?>