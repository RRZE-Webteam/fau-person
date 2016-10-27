<?php
 

get_header(); ?>
    <?php if ( have_posts() ) : ?> 
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php while ( have_posts() ) : the_post(); ?>

                        <?php 
                        $id = $post->ID;
                        echo FAU_Standort_Shortcodes::fau_standort(array('id'=> $post->ID, 'show' => 'adresse, bild' )); 
			?>
          
                    <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;
get_footer();
