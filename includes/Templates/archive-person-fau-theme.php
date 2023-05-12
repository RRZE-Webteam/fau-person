<?php

get_header();

$currentTheme = wp_get_theme();		
$vers = $currentTheme->get( 'Version' );
  if (version_compare($vers, "2.3", '<')) {  
      get_template_part('template-parts/hero', 'small');
  }
$screenreadertitle = __('Kontaktliste', 'fau-person');
?>
	<div id="content">
		<div class="content-container">
		    <div class="content-row">
			    <main<?php echo fau_get_page_langcode($post->ID);?>>
                    <h1 id="maintop" class="screen-reader-text"><?php echo $screenreadertitle; ?></h1>

                        <?php while (have_posts()) {
                            the_post();
                            $id = $post->ID;
                            if ($id) {
                                echo FAU_Person\Shortcodes\Kontakt::shortcode_kontakt(array("id" => $post->ID, 'format' => 'kompakt', 'showlink' => 0, 'showlist' => 1));
                            } else { ?>
                                <div class="alert">
                                    <p>
                                    <strong><?php _e('Es tut uns leid.', 'fau-person'); ?></strong><br>
                                    <?php _e('Für den angegebenen Kontakt können keine Informationen abgerufen werden.', 'fau-person'); ?>
                                    </p>
                                </div>
                                <?php
                            }
                        } ?>                                  
                    <nav class="navigation" aria-label="<?php echo __('Navigation', 'fau-person'). ' '.$screenreadertitle; ?>">
                        <div class="nav-links">
                            <div class="nav-previous"><?php previous_posts_link(__('Zurück', 'fau-person')); ?></div>
                            <div class="nav-next"><?php next_posts_link(__('Weiter', 'fau-person'), ''); ?></div>
                        </div>
                    </nav>
		      </main>
		    </div>
		</div>
	</div>
	
<?php
get_footer();
