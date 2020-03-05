<?php
get_header(); 
//	enqueueForeignThemes();
	

?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                    <?php 
		 
                      echo FAU_Person\Data::fau_person_page($id);
                    ?>         
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile;
get_footer();
