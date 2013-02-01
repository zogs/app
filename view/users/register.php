
<div class="register">
<?php echo $this->session->flash();?>
	<form class="form" id="form_register" autocomplete="on" action="<?php echo Router::url('users/register'); ?>" method="post" <?php echo (isset($Success))? 'class="hide"':''; ?>>
		<h1>Sign in</h1>

		<?php echo $this->Form->input('login','',array('icon'=>'icon-user','required'=>'required','placeholder'=>"Votre pseudo",'data-url'=>Router::url('users/check'))) ?>
		<?php echo $this->Form->input('email',"",array('type'=>'email', 'icon'=>"icon-envelope","required"=>"required","placeholder"=>"Votre email",'data-url'=>Router::url('users/check'))) ?>
		<?php echo $this->Form->input('password','',array('type'=>"password",'icon'=>'icon-lock','required'=>'required','placeholder'=>'Votre mot de passe')) ?>
		<?php echo $this->Form->input('confirm','', array('type'=>'password','icon'=>'icon-lock','required'=>'required','placeholder'=>'Confirmer votre mot de passe')) ?>		
		<?php echo $this->Form->input('prenom',"",array('icon'=>'icon-user','placeholder'=>'Prénom')) ?>
		<?php echo $this->Form->input('nom',"",array('icon'=>'icon-user','placeholder'=>'Nom de famille')) ;?>
		<?php echo $this->Form->input('age','',array('icon'=>'icon-gift','placeholder'=>'Année de naissance')) ;?>

		<div class="control-group">
			<label for="pays" class="control-label"></label>
			<div class="controls">
			
					<?php 
					
					$this->request('world','locate');


					?>	

			</div>
		</div>	
		
		<div class="actions">
			<?php echo $this->Form->input('token','hidden',array('value'=>$this->session->token())) ;?>			
			<?php echo $this->Form->input('accept','hidden',array('value'=>1)); ?>
			<p>En validant l'inscription j'accepte les conditions d'utilisations</p>
			<input class="pull-right btn btn-large btn-inverse" type="submit" value="C'est parti !" />
			
		</div>

	</form>
	
</div>	

<script type="text/javascript">
	

	
</script>