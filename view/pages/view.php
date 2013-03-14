<?php 

//Title for brwoser
$title_for_layout = $page->title;


echo $this->session->flash();

?>

<div class="hero-unit">	
	<?php echo $page->content; ?>
</div>