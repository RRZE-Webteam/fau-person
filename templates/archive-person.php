<?php
 

get_header(); 
?>
    <?php if ( have_posts() ) : ?> 
        <div id="content">
            <div class="container">
                <div class="row">
                    <div class="span8">
                    <?php while ( have_posts() ) : the_post(); ?>

                        <?php 
                        $id = $post->ID;
                        echo FAU_Person_Shortcodes::fau_person(array('id'=> $post->ID, 'show' => 'bild, mehrlink' )); 
			?>

                    <?php endwhile; ?>                                  
                    </div>
                </div>
            </div>
        </div>
    <div class="navigation">
        <div class="nav-previous"><?php previous_posts_link( sprintf( '&laquo; %s', __('Vorherige Kontakte', FAU_PERSON_TEXTDOMAIN ) ) ); ?></div>
        <div class="nav-next"><?php next_posts_link( sprintf( '&raquo; %s', __('Weitere Kontakte', FAU_PERSON_TEXTDOMAIN ) ) ); ?></div>
    </div>
    <?php else : ?>
        <h2 class="center"><?php _e( 'Nichts gefunden', FAU_PERSON_TEXTDOMAIN) ?></h2>
        <p class="center">
    <?php _e( 'Leider sind noch keine Kontakte eingepflegt.', FAU_PERSON_TEXTDOMAIN); ?>
        </p>
    <?php endif;
get_footer();
    

