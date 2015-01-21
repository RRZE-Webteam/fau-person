<?php
 
get_header(); ?>
<div id="primary">
    <div id="content" role="main">
    <?php
    $args = array( 'post_type' => 'person', );
    $loop = new WP_Query( $args );
    while ( $loop->have_posts() ) : $loop->the_post();?>
        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
 
                <!-- Display featured image in right-aligned floating div -->
                <div style="float: right; margin: 10px">
                    <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                </div>
 
                <!-- Display Title and Author Name -->
                
                <strong>Title: </strong><?php the_title(); ?><br />
                <strong>Werte: </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), '_person_nachname', true ) ); ?>
                <?php $personen = get_post_meta( get_the_ID() ); ?>
                <?php print_r($personen); ?>
                <?php foreach($personen as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        echo $key . ' hat den Wert ' . $value2;
                        echo '<br />';
                    }
                        
                } ?>
                <br />

            </header>
 
            <!-- Display movie review contents -->
            <div class="entry-content"><?php the_content(); ?></div>
        </div>
 
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>