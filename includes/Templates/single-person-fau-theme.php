<?php

get_header(); 

$currentTheme = wp_get_theme();		
$vers = $currentTheme->get( 'Version' );
  if (version_compare($vers, "2.3", '<')) {  
    // alte Anweisung für den Hero hier....
      get_template_part('template-parts/hero', 'small');
  }
  
?>

<?php while ( have_posts() ) : the_post(); 
    $id = $post->ID;
?>
	<div id="content">
		<div class="content-container">
		    <div class="content-row">
			    <main<?php echo fau_get_page_langcode($post->ID);?>>
                 <?php 
                    if ($id) { ?>
                     <h1 id="maintop" class="screen-reader-text"><?php the_title(); ?></h1>
                        <?php echo FAU_Person\Data::fau_person_page($id);
                    } else { ?>
                        <h1 id="maintop" class="screen-reader-text"><?php _e('Fehler','fau'); ?></h1>
                        <div class="alert">
                            <p>
                                <strong><?php _e('Es tut uns leid.','fau'); ?></strong><br>
                                <?php _e('Für den angegebenen Kontakt können keine Informationen abgerufen werden.','fau-person'); ?>
                            </p>
                        </div>
                    <?php }  ?> 
                </main>
		    </div>
		</div>
	</div>	
<?php endwhile;
get_footer(); 
