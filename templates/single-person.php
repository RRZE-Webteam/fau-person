<?php
 

get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <?php get_template_part('hero', 'small'); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
                    $id = $post->ID;

		    echo fau_person_page($id);
                    the_content();
			?>
          
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
