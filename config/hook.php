<?php 
if($this->request->prefix == 'admin'){
	$this->layout = 'admin'; 


	//Si l'user n'est pas admin on redirige sur le log in
	if(!$this->session->user() || $this->session->user('role') != 'admin'){
		$this->redirect('users/login');
	}
} ?>