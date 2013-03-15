<?php

class ContentsController extends Controller {
		
		
		public function admin_edit($id = null){

			$this->loadModel('Contents');
			$d['id'] = $id;

			$lang = $this->getLang();		

			if(Request::$data){

				$new = Request::$data;
				$lang = Request::post('lang');	

				if(!empty($id))
					$old = $this->Contents->findFirst(array('conditions'=>array('id'=>$id)));

				if($this->Contents->validates($new)){
					
					if($this->Contents->saveContent($new)){

						if(empty($old)) $id= $this->Contents->id;
						Session::setFlash("Contenu modifié","success");
					}
					else
						Session::setFlash("Error saving content","error");
				}

			}
							
			if($id){
				$d['content'] = $this->Contents->findFirst(array('conditions'=>array('id'=>$id)));
				$d['content'] = $this->Contents->i18n($d['content'],array('lang'=>$lang));
					
				$d['id'] = $id;
				Request::$data = $d['content'];
			}

			

			$this->set($d);
		}

		public function admin_delete($id){

			$this->loadModel('Contents');				
			
			if($this->Contents->deleteContent($id)){

				Session::setFlash("Page supprimé","success");

				$i18ns = $this->Contents->findi18nContents($id);
				if($this->Contents->deletei18nContents($i18ns)){
					Session::setFlash("Traductions supprimés","success");
				}
				else {
					Session::setFlash("Error lors de la suppression des traductions","error");
				}
				
			}
			else {
				Session::setFlash("Error lors de la suppression","error");
			}

			$this->redirect('admin/pages/index');
		}
}

?>