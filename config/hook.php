<?php 
if(Request::$prefix == 'admin'){
	$this->layout = 'admin'; 


	//Si l'user n'est pas admin on redirige sur le log in
	if(!Session::user() || Session::user('role') != 'admin'){
		$this->redirect('users/login');
	}
} ?>