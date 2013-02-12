public function validates($data, $rules = null, $field = null){


			$errors = array();

			//On recupere les regles de validation
			if(!$rules){
				$validates = $this->validates;
			}
			else {

				if($field==null)
					$validates = $this->validates[$rules];
				else
					$validates = array($field=>$this->validates[$rules][$field]);
			}
 			
 			//Vérifie les regles de validation pour chaque champs
			foreach ($validates  as $field => $model) { 
					
				//Si la donnée correspondant est manquante -> erreur				
				if(empty($data->$field)){

					//Si il y a plusiers regles
					if(isset($model['rules'])){
						$rules = $model['rules']; 						
						if(in_array('optional',$rules)) $optional=true;
						//if not optional
						if(!isset($optional)){
							$rule = $rules[0];
							$errors[$field] = $rule['message'];
						}
						
					}
					//Si il y a qu'une regle (et que ce nest pas un upload de fichier)
					if(isset($model['rule']) && $model['rule']!='file'){
						
						//Si le champ est optionnel, sauter au prochain champ
						if($model['rule']=='optional') continue;
						
						$errors[$field] = $model['message'];
					}
					
				}
				else{
				
					//Si il y a plusiers regles
					if(isset($model['rules'])){
						$rules = $model['rules']; 						
					} 
					//Si il y a qu'une regle
					if(isset($model['rule'])){
						
						 $rules = array($model);
					}

					//Pour toutes les regles correspondante
					foreach ($rules as $rule) {

						if($rule['rule']=='notEmpty'){
							if(empty($data->$field)) $errors[$field] = $rule['message'];				
						}
						elseif($rule['rule']=='notNull'){
	 						if($data->$field==0) $errors[$field] = $rule['message'];				
	 					}
	 					elseif($rule['rule']=='email'){

	 						$email_regex = '[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,4})';
	 						if(!preg_match('/^'.$email_regex.'$/',$data->$field)) $errors[$field] = $rule['message'];
	 						
	 					}
	 					elseif(strpos($rule['rule'],'confirm')===0){

	 						$fieldtoconfirm = str_replace('confirm', '', $field);

	 						if($data->$fieldtoconfirm!=$data->$field) $errors[$field] = $rule['message'];
	 					}
	 					elseif($rule['rule']=='checkbox' || $rule['rule']=='radio'){
	 						
	 						if(!is_array($data->$field)) $checkboxs = array( $data->$field);
	 						else $checkboxs = $data->$field;
	 							
 							foreach ($rule['mustbetrue'] as $betrue) {
 								
 								if(!in_array($betrue,$checkboxs)) $errors[$field] = $rule['messages'][$betrue];
 							} 								 							
	 						foreach ($checkboxs as $checkbox) {
	 								
	 								$data->$checkbox = 1;	 
	 							}	
	 						unset($data->$field);	 						
	 					
	 					}	
						elseif($rule['rule']=='regex'){

							if(!preg_match('/^'.$rule['regex'].'$/',$data->$field)) $errors[$field] = $rule['message'];
						}
						elseif($rule['rule']=='file'){
							continue;
						}
					}				
				}
				//reset optionnal
				$optional=false;
			}

 			//Vérifie les fichiers uploadé
 			if(isset($_FILES)){ 
				
				//Pour chaque fichier uploader
 				foreach ($_FILES as $key => $file) { 					

 					$input = str_replace('input','',$key);

 					//Si le ficher est bien attendu par les regles
 					if(isset($validates[$key]) && $validates[$key]['rule']=='file'){

	 					//Si le fichier n'est pas vide et quil n'y pas d'erreur d'envoi
	 					if($file['error'] == 'UPLOAD_ERR_OK'){
	 						
		 					//Si il y a des regles définies
		 					if($validates[$key]['params']){

			 					//Si il y a une limite de poids 
			 					if($validates[$key]['params']['max_size']) {

			 						//Si le fichier est trop gros
			 						if($file['size']>$validates[$key]['params']['max_size']){

			 							$errors[$input] = $validates[$key]['params']['max_size_error'];
			 						}
			 					}
			 					//Si il y a des extentions spécifiquement authorisées
	 							if($validates[$key]['params']['extentions']){
	 								
	 								$extention = substr(strrchr($file['name'], '.'),1);
		 							$extentions = $validates[$key]['params']['extentions'];

		 							if(!in_array($extention,$extentions)){
		 								$errors[$input] = $validates[$key]['params']['extentions_error'];	 					
	 								} 
	 							}

	 							//If Prevent hidden php code
	 							if($validates[$key]['params']['ban_php_code'] && $validates[$key]['params']['ban_php_code'] == true){
		 							//Vérifie qu'il n'y a pas de code php caché dans l'image				 							
		 							if(strpos(file_get_contents($file['tmp_name']),'<?php')){

		 								throw new zException("Malicious php code detected in uploaded file", 1);
		 								
		 							}	
	 							}
			 				}
		 				}
		 				else {

		 					//if the upload is optional , continue
		 					if(isset($validates[$key]['optional'])) continue;

		 					//if the upload is required
		 					if(isset($validates[$key]['required']))
		 						$errors[$input] =$validates[$key]['message'];
		 				}
		 			}
		 			else {
		 				throw new zException("Uploading a file non-expected", 1);
		 				
		 			}
 				}
 			}

 			$this->errors = $errors;

 			//Si une class Form est lié a ce model
 			if(isset($this->Form)){
 				$this->Form->setErrors($errors); //On lui envoi les erreurs
 			}

 			//Si pas d'erreur validates renvoi true
 			if(empty($errors)){
 				return $data;
 			}
 				
 			return false;
 			 			 		
 	}