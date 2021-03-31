<?php

$display = 'title, telefon, email, fax, url, content, adresse, bild, permalink';  
$adisplay = array_map('trim', explode(',', $display));
    $showfields = array();
    foreach ($adisplay as $val) {
	$showfields[$val] = 1;
    }
    get_header(); 
   
?>

<?php while ( have_posts() ) : the_post();  
    get_template_part('template-parts/hero', 'small'); ?>

    <div id="content">
	<div class="container">
	    <div class="row">
		 <div <?php post_class("entry-content"); ?>>
		    <main id="droppoint">
			<?php echo FAU_Person\Data::create_fau_standort($post->ID, $showfields, 'h1'); ?>
		    </main>
		</div>		
	    </div>
	</div>
    </div>
	
	
<?php endwhile;  
get_template_part('template-parts/footer', 'social'); 
get_footer();