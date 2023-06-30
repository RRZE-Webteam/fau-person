<?php

$display = 'title, telefon, email, fax, url, content, adresse, bild, permalink';  
$adisplay = array_map('trim', explode(',', $display));
    $showfields = array();
    foreach ($adisplay as $val) {
	$showfields[$val] = 1;
    }
    get_header(); 
  
    $currentTheme = wp_get_theme();		
    $vers = $currentTheme->get( 'Version' );
    if (version_compare($vers, "2.3", '<')) {  
      // alte Anweisung für den Hero hier....
        get_template_part('template-parts/hero', 'small');
    }
?>

<?php while ( have_posts() ) : the_post();  ?>
	<div id="content">
		<div class="content-container">
		    <div class="content-row">
			    <main<?php echo fau_get_page_langcode($post->ID);?>>
                    <?php echo FAU_Person\Data::create_fau_standort($post->ID, $showfields, 'h1'); ?>
                </main>
            </div>
		</div>
	</div>
<?php endwhile;  
get_footer();