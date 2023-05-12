<?php
 
get_header(); 
$currentTheme = wp_get_theme();		
$vers = $currentTheme->get( 'Version' );
  if (version_compare($vers, "2.3", '<')) {  
    // alte Anweisung für den Hero hier....
      get_template_part('template-parts/hero', 'small');
  }
$screenreadertitle = __('Standorte','fau-person');
?>
	<div id="content">
		<div class="content-container">
		    <div class="content-row">
			    <main<?php echo fau_get_page_langcode($post->ID);?>>
                    <h1 id="maintop" class="screen-reader-text"><?php echo $screenreadertitle; ?></h1>
     
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

