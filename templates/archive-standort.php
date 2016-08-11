<?php
 

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
                    $id = $post->ID;

		    echo FAU_Standort_Shortcodes::fau_archive_page($id, 0);
                    the_content();
			?>
          
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
