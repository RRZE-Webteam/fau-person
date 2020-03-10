<?php

get_header(); 
?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part('template-parts/hero', 'small'); ?>

	<div id="content">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
				    <main id="droppoint">
				    <?php 
				    $id = $post->ID;
				    if ($id) {
					echo FAU_Person\Data::fau_person_page($id);
				    } else { ?>
					<p class="hinweis">
					    <strong><?php _e('Es tut uns leid.','fau-person'); ?></strong><br>
					    <?php _e('Für den angegebenen Kontakt können keine Informationen abgerufen werden.','fau-person'); ?>
					</p>
				    <?php }  ?>
				    </main>
			    </div>
				
			</div>
		</div>
	</div>
	
	
<?php endwhile;
get_template_part('template-parts/footer', 'social'); 
get_footer(); 