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
        
        
<?php
$id = empty($instance['id']) ? ' ' : $instance['id'];
$title = empty($instance['title']) ? ' ' : $instance['title'];
if (!empty($id))
{
$post = get_post($id);


$content = '<div class="person">';
if(!empty($title)) $content .= '<h2 class="small">'.$title.'</h2>';
$content .= '<div class="row">';
    if(has_post_thumbnail($id))
    {
        $content .= '<div class="span1">';
        $content .= get_the_post_thumbnail($id, 'person-thumb');
        $content .= '</div>';
    }
    
    $content .= '<div class="span3">';
        $content .= '<h3>';
            if(get_post_meta($id, '_person_titel', true)) $content .= get_post_meta($id, '_person_titel', true).' ';
            $content .= get_the_title($id);
            //if(get_post_meta('firstname', $id)) $content .= get_post_meta('firstname', $id).' ';
            //if(get_post_meta('lastname', $id)) $content .= get_post_meta('lastname', $id);
            if(get_post_meta($id, '_person_abschluss', true)) $content .= ' '.get_post_meta($id, '_person_abschluss', true);
            $content .= '</h3>';
            $content .= '<ul class="person-info">';
            if(get_post_meta($id, '_person_position', true)) $content .= '<li class="person-info person-info-position"><strong>'.get_post_meta($id, '_person_position', true).'</strong></li>';
            if(get_post_meta($id, '_person_institution', true)) $content .= '<li class="person-info person-info-institution">'.get_post_meta($id, '_person_institution', true).'</li>';
            if(get_post_meta($id, '_person_telefon', true)) $content .= '<li class="person-info person-info-phone">'.get_post_meta($id, '_person_telefon', true).'</li>';
            if(get_post_meta($id, '_person_telefax', true)) $content .= '<li class="person-info person-info-fax">'.get_post_meta($id, '_person_telefax', true).'</li>';
            if(get_post_meta($id, '_person_email', true)) $content .= '<li class="person-info person-info-email"><a href="mailto:'.get_post_meta($id, '_person_email', true).'">'.get_post_meta($id, '_person_email', true).'</a></li>';
            if(get_post_meta($id, '_person_url', true)) $content .= '<li class="person-info person-info-www"><a href="'.get_post_meta($id, '_person_url', true).'">'.get_post_meta($id, '_person_url', true).'</a></li>';
            if(get_post_meta($id, '_person_strasse', true)) $content .= '<li class="person-info person-info-address">'.get_post_meta($id, '_person_strasse', true).'</li>';
            if(get_post_meta($id, '_person_raum', true)) $content .= '<li class="person-info person-info-room">' . __('Raum', 'fau') . ' '.get_post_meta($id, '_person_raum', true).'</li>';
            // if(get_post_meta($id, '_person_freitext', true)) $content .= '<div class="person-info person-info-description">'.get_post_meta($id, '_person_freitext', true).'</div>';
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';
        }
    echo $content;

        
        
  ?>      
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>

<?php
/*

function FAUPersonWidget()
{
$widget_ops = array('classname' => 'FAUPersonWidget', 'description' => __('Personen-Visitenkarte anzeigen', 'fau') );
$this->WP_Widget('FAUPersonWidget', 'Personen-Visitenkarte', $widget_ops);
}
function form($instance)
{
$instance = wp_parse_args( (array) $instance, array( 'id' => '' ) );
$id = $instance['id'];
$title = $instance['title'];
//$persons = query_posts('post_type=person');
$persons = get_posts(array('post_type' => 'person', 'posts_per_page' => 9999));
if($persons->post_title) {
$name = $persons->post_title;
}
else
{
$name = $this->get_post_meta_id('firstname').' '.$this->get_post_meta_id('lastname');
}
echo '<p>';
echo '<label for="'.$this->get_post_meta_id('title').'">'. __('Titel', 'fau'). ': ';
echo '<input type="text" id="'.$this->get_post_meta_id('title').'" name="'.$this->get_post_meta_name('title').'" value="'.esc_attr($title).'" />';
echo '</label>';
echo '</p>';
echo '<p>';
echo '<label for="'.$this->get_post_meta_id('id').'">' . __('Person', 'fau'). ': ';
echo '<select id="'.$this->get_post_meta_id('id').'" name="'.$this->get_post_meta_name('id').'">';
foreach($persons as $item)
{
echo '<option value="'.$item->ID.'"';
if($item->ID == esc_attr($id)) echo ' selected';
echo '>'.$item->post_title.'</option>';
}
echo '</select>';
echo '</label>';
echo '</p>';
}
function update($new_instance, $old_instance)
{
$instance = $old_instance;
$instance['id'] = $new_instance['id'];
$instance['title'] = $new_instance['title'];
return $instance;
}
function widget($args, $instance)
{
extract($args, EXTR_SKIP);
echo $before_widget;
$id = empty($instance['id']) ? ' ' : $instance['id'];
$title = empty($instance['title']) ? ' ' : $instance['title'];
if (!empty($id))
{
$post = get_post($id);
$content = '<div class="person">';
if(!empty($title)) $content .= '<h2 class="small">'.$title.'</h2>';
$content .= '<div class="row">';
if(has_post_thumbnail($id))
{
$content .= '<div class="span1">';
$content .= get_the_post_thumbnail($id, 'person-thumb');
$content .= '</div>';
}
$content .= '<div class="span3">';
$content .= '<h3>';
if(get_post_meta('title', $id)) $content .= get_post_meta('title', $id).' ';
if(get_post_meta('firstname', $id)) $content .= get_post_meta('firstname', $id).' ';
if(get_post_meta('lastname', $id)) $content .= get_post_meta('lastname', $id);
if(get_post_meta('title_suffix', $id)) $content .= ' '.get_post_meta('title_suffix', $id);
$content .= '</h3>';
$content .= '<ul class="person-info">';
if(get_post_meta('position', $id)) $content .= '<li class="person-info person-info-position"><strong>'.get_post_meta('position', $id).'</strong></li>';
if(get_post_meta('institution', $id)) $content .= '<li class="person-info person-info-institution">'.get_post_meta('institution', $id).'</li>';
if(get_post_meta('phone', $id)) $content .= '<li class="person-info person-info-phone">'.get_post_meta('phone', $id).'</li>';
if(get_post_meta('fax', $id)) $content .= '<li class="person-info person-info-fax">'.get_post_meta('fax', $id).'</li>';
if(get_post_meta('email', $id)) $content .= '<li class="person-info person-info-email"><a href="mailto:'.get_post_meta('email', $id).'">'.get_post_meta('email', $id).'</a></li>';
if(get_post_meta('webseite', $id)) $content .= '<li class="person-info person-info-www"><a href="http://'.get_post_meta('webseite', $id).'">'.get_post_meta('webseite', $id).'</a></li>';
if(get_post_meta('adresse', $id)) $content .= '<li class="person-info person-info-address">'.get_post_meta('adresse', $id).'</li>';
if(get_post_meta('raum', $id)) $content .= '<li class="person-info person-info-room">' . __('Raum', 'fau') . ' '.get_post_meta('raum', $id).'</li>';
// if(get_post_meta('freitext', $id)) $content .= '<div class="person-info person-info-description">'.get_post_meta('freitext', $id).'</div>';
$content .= '</ul>';
$content .= '</div>';
$content .= '</div>';
$content .= '</div>';
}
echo $content;
*/
?>