<?php
get_header(); ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php 
                        echo FAU_Standort_Shortcodes::fau_standort_page(get_the_ID());
                    ?>          
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
