<div id="account">
	
	<?php echo $this->session->flash();?>
	
	<div class="dashboard">		
		<div class="module mini-profile">		
			<a class="" href="/">
				<div class="fleft"><img src="<?php echo Router::webroot($this->session->user('obj')->getAvatar());?>" class="avatar size32" alt="<?php echo $user->login;?>"></div>
				<div class="fleft"><b><?php echo $user->getLogin();?></b></div>
		</div>

		<div class="module">
			<ul class="dashboard-links">
				<?php
					$links = array(
									'profil'=>'Profil',
									'account'=>'Account',
									'avatar'=>'Avatar',									
									'password'=>'Password',
									'delete'=>'Delete'
								);

					foreach ($links as $key => $value) {
						
						if($key==$action)
							echo '<li class="active">';
						else
							echo '<li class="">';
						echo '<a class="" href="'.Router::url('users/account/'.$key).'">'.$value.'</a>';
						echo '</li>';
					}
				?>
			</ul>
		</div>
	</div>


	<div class="module account-form">

		<form class="form" id="account-form" autocomplete="off" action="<?php echo Router::url('users/account/'.$action); ?>" method="post" enctype="multipart/form-data">
			<?php echo $this->Form->_input('action','hidden',array('value'=>$action)); ?>
			<?php echo $this->Form->_input('token','hidden',array('value'=>$this->session->token())) ;?>
			<?php echo $this->Form->_input('user_id','hidden',array('value'=>$this->session->user('user_id'))) ;?>

			<?php //=========PROFIL================ ?>
			<?php if($action=='profil'||$action==''): ?>

				<div class="module-header">
					<h2>Profil</h2>
					<p class="subheader">Ces informations n'apparaissent pas blabla...</p>
				</div>
								
				
				<?php echo $this->Form->input('prenom','Prenom',array('icon'=>'icon-user','placeholder'=>'Prenom')); ?>
				<?php echo $this->Form->input('nom','Nom',array('icon'=>'icon-user','placeholder'=>'Nom')); ?>					
				<?php echo $this->Form->SelectNumber('age','Birth year',2006,1950,array('default'=>$user->getAge(),'icon'=>'icon-gift','placeholder'=>"( Your birth year )")) ;?>	

				<div class="control-group">	
					<label for="CC1" class="control-label">Localisation</label>			
					<div class="controls">
						<i class='icon-form icon-home'></i>
						<?php $options = $this->request('world','locate',array($user)); ?>					
					</div>
				</div>	
				
				<?php echo $this->Form->Checkbox('anonym','Anonymat',array('1'=>'Rendre mon profil anonyme'),array('default'=>$user->anonym,'helper'=>"Toutes ces informations ne seront pas visible des autres utilisateurs")) ;?>			

				<input type="submit" class="btn btn-large btn-inverse" value="Save profil" />			
			<?php endif ;?>




			<?php //=========ACCOUNT================= ?>
			<?php if($action=='account'): ?>

				<div class="module-header">
					<h2>Account</h2>
					<p class="subheader">User name and contact email</p>
				</div>

				<?php echo $this->Form->input('login','Login',array('icon'=>'icon-user','required'=>'required','data-url'=>Router::url('users/check'))); ?>
				<?php echo $this->Form->input('email','Email',array('icon'=>'icon-envelope','required'=>'required','type'=>'email','data-url'=>Router::url('users/check'))); ?>
				<?php echo $this->Form->Select('lang','Language',Conf::$languageAvailable,array('default'=>$user->lang,'placeholder'=>'( your language )','icon'=>'icon-book')) ;?>
				<?php echo $this->Form->Radio('account','Compte',array('private'=>'Privé','public'=>'Public'),array('default'=>$user->account,'helper'=>"Un compte public peut être vu et recevoir des messages d'autres utilisateurs")) ;?>
				<?php echo $this->Form->input('Save account','submit',array('class'=>'btn btn-large btn-inverse')) ;?>


			<?php endif ;?>



			
			<?php //=========AVATAR=================== ?>
			<?php if($action=='avatar'): ?>

				<div class="module-header">
					<h2>Avatar</h2>
					<p class="subheader">Votre image bla bla</p>
				</div>

				<div class='avatar'>
					<img src="<?php echo Router::webroot($user->getAvatar()); ?>" />
				</div>
				
					
				<?php echo $this->Form->input('avatar','Avatar',array('icon'=>'icon-bullhorn','type'=>'file')); ?>
					
				<input type="submit" class="btn btn-large btn-inverse" value="Save avatar" />		
		
			<?php endif ;?>


			<?php //=========PASSWORD============== ?>			
			<?php if($action=='password'):?>

				<div class="module-header">
					<h2>Mot de passe</h2>
					<p class="subheader">Changer votre mot de passe</p>
				</div>


				<?php echo $this->Form->input('oldpassword','Old password',array('type'=>'password','icon'=>'icon-lock','placeholder'=>'Old password')); ?>
				<?php echo $this->Form->input('password','New password',array('type'=>'password','icon'=>'icon-lock','placeholder'=>'New password')); ?>
				<?php echo $this->Form->input('confirm','Confirm password',array('type'=>'password','icon'=>'icon-lock','placeholder'=>'Confirm password')); ?>
				<input class="btn btn-large btn-inverse" type="submit" value="Change Password" />	
				
			<?php endif ;?>


			<?php //=======DELETE================ ?>
			<?php if($action=='delete'): ?>

				<div class="module-header">
					<h2>Delete</h2>
					<p class="subheader">Do you want to delete your account ?</p>
				</div>

		
				<?php echo $this->Form->input('password','Yes i do', array('type'=>'password','icon'=>'icon-lock','placeholder'=>'Type your password and delete')); ?>
				<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>
				<input class="btn btn-large btn-inverse" type="submit" value="Delete" />

			<?php endif ;?>



		</form>
	</div>
</div>




