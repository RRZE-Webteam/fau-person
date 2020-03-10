<?php
 
get_header(); 
get_template_part('template-parts/hero', 'index');
$screenreadertitle = __('Kontaktliste','fau-person');
?>
    <div id="content">
        <div class="container">
	    <div class="row">
		<main class="col-xs-12" id="droppoint">
		    <h1 class="screen-reader-text"><?php echo $screenreadertitle; ?></h1>   

                    <?php while ( have_posts() ) {
			the_post();
			$id = $post->ID;
			if ($id) {
			    echo FAU_Person\Shortcodes\Kontakt::shortcode_kontakt(array("id"=> $post->ID, 'format' => 'kompakt', 'showlink' => 0, 'showlist' => 1 ));
			} else { ?>
			    <p class="hinweis">
				<strong><?php _e('Es tut uns leid.','fau-person'); ?></strong><br>
				<?php _e('Für den angegebenen Kontakt können keine Informationen abgerufen werden.','fau-person'); ?>
			    </p>
			<?php } 
		    } ?>                                  
		    <nav class="navigation">
			<div class="nav-previous"><?php previous_posts_link(__('<span class="meta-nav">&laquo;</span> Zurück', 'fau-person')); ?></div>
			<div class="nav-next"><?php next_posts_link(__('Weiter <span class="meta-nav">&raquo;</span>', 'fau-person'), '' ); ?></div>
		    </nav>
		</main>
	    </div>    
	</div>
    </div>
<?php 
get_template_part('template-parts/footer', 'social'); 
get_footer(); 

