<?php
 

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
                    $id = $post->ID;

		    echo FAU_Person_Shortcodes::fau_person_shortlist($id, 0);
                    the_content();
			?>
          
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
    

