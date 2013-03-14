<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<?php $this->loadCSS();?>
	<?php $this->loadJS();?>	
	<title><?php echo isset($title_for_layout)?$title_for_layout : Conf::$website;?></title>
	
</head>
<body data-user_id="<?php echo $this->session->user('user_id'); ?>">


	<header class="navbar navbar-fixed-top">
	  <nav class="navbar-inner">
	    <div class="container">
      		<a class="brand" href="<?php echo Router::url('pages/view/4/homepage');?>">
	      	  	<?php echo Conf::$website;?>
			</a>
			<form class ="navbar-search pull-left" action="#" method="get">
			<input type ="text" class="search-query nav-search" name="rch" placeholder="Search">
			</form>

			<ul class="nav">
				
				<?php
				//Recuperation du Menu
				//Appel de ma methode getMenu du controlleur Pages
				$pagesMenu = $this->call('Pages','getMenu');

				foreach ($pagesMenu as $v) : ?>				
					<li><a href='<?php echo Router::url("pages/view/$v->id/$v->slug");?>' ><?php echo $v->title; ?></a></li>
				<?php 
				endforeach;
				?>

				<li><a href="<?php echo Router::url('posts/index');?>">Blog.</a></li>
				
				<?php
				//Admin section button
				if($this->session->user('role')=='admin'):?>
				<li><a href="<?php echo Router::url('admin/posts/index');?>">Admin.</a></li>
				<?php endif;

				
				
				?>

				
			</ul>
		

			<ul class="nav pull-right">
				<?php if ($this->session->user()): ?>
					<li><a href="<?php echo Router::url('users/thread');?>">
							<img class="nav-avatar" src="<?php echo Router::webroot($this->session->user('obj')->getAvatar()); ?>" />	
							<span class="nav-login"><?php echo $this->session->user('login'); ?></span>
					</a></li>
					<li class="dropdown">	
			
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Router::url('users/logout'); ?>">Déconnexion</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo Router::url('users/account'); ?>">Mon Compte</a></li>						
						</ul>
					</li>
				<?php else: ?>

					<form class="loginForm" action="<?php echo Router::url('users/login'); ?>" method='post'>
						<input type="login" name="login" required="required" placeholder="Login or email" autofocus="autofocus" value="admin"/>
						<input type="password" name="password" required="required" placeholder="Password" value="fatboy" />
						<input type="hidden" name="token" value="<?php echo $this->session->token();?>" />
						<input type="submit" value="OK" />
					</form>
					<li><a href="<?php echo Router::url('users/login');?>">Login</a></li>	
					<li><a href="<?php echo Router::url('users/register');?>" >Inscription</a></li>


				<?php endif ?>

			</ul>
		</div>
	  </nav>
	</header>

	<section class="container mainContainer">	
			
		<?php echo $content_for_layout;?>
	</section>


	<div class="modal fade" id="myModal"></div>

	<footer class="footer">
	</footer>

</body>



 <script type="text/javascript">

 	/*===========================================================
 		Set security token
 	============================================================*/
 	var CSRF_TOKEN = '<?php echo $this->session->token(); ?>';

 	/*===========================================================
 		GOOGLE FONTS
 	============================================================*/
      WebFontConfig = {
        google: { families: [ 'Bangers','Squada One','Oswald:300,400,700' ] },      
        fontinactive: function(fontFamily, fontDescription) { /*alert('Font '+fontFamily+' is currently not available'); */}
      };

      (function() {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
      })();
</script>





</html>