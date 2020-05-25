<?php

get_header(); 
?>
   
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">	
    <?php
    $display = 'title, telefon, email, fax, url, content, adresse, bild, permalink';  
    $adisplay = array_map('trim', explode(',', $display));
    $showfields = array();
    foreach ($adisplay as $val) {
	$showfields[$val] = 1;
    }

    while ( have_posts() ) {
	the_post(); 
	$id = $post->ID;
         echo FAU_Person\Data::create_fau_standort($id, $showfields, 'h1');
    } 
    ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->
<?php
get_footer();
