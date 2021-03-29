<?php
 
get_header(); 
get_template_part('template-parts/hero', 'index');
$screenreadertitle = __('Standorte','fau-person');
?>
    <div id="content">
        <div class="container">
	    <div class="row">
		<div <?php post_class(); ?>>
		    <main>
			    <h1 id="droppoint" class="mobiletitle"><?php the_title(); ?></h1>
	<?php
	$display = 'title, telefon, email, fax, url, kurzbeschreibung, adresse, bild, permalink';  
	$adisplay = array_map('trim', explode(',', $display));
	$showfields = array();
	foreach ($adisplay as $val) {
	    $showfields[$val] = 1;
	}
	while ( have_posts() ) {
	    the_post();
	    echo FAU_Person\Data::create_fau_standort($post->ID, $showfields, 'h1'); 
	}  ?>
			<nav class="navigation">
			    <div class="nav-previous"><?php previous_posts_link(__('<span class="meta-nav">&laquo;</span> Zurück', 'fau-person')); ?></div>
			    <div class="nav-next"><?php next_posts_link(__('Weiter <span class="meta-nav">&raquo;</span>', 'fau-person'), '' ); ?></div>
			</nav>
		    </main>
		</div>    
	    </div>    
	</div>
    </div>
<?php 
get_template_part('template-parts/footer', 'social'); 
get_footer(); 

