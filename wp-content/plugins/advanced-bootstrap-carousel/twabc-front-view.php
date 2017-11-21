<?php
// Shortcode
function twabc_shortcode($atts, $content = null) {
		// Set default shortcode attributes
	$options = get_option( 'twabc_settings' );
	if(!$options){
		twabc_set_options ();
		$options = get_option( 'twabc_settings' );
	}
	$options['id'] = '';

	// Parse incomming $atts into an array and merge it with $defaults
	$atts = shortcode_atts($options, $atts);

	return twabc_frontend($atts);
}
add_shortcode('twabc-carousel', 'twabc_shortcode');

// Display carousel
function twabc_frontend($atts){

	// Build the attributes
	$id = rand(0, 999); // use a random ID so that the CSS IDs work with multiple on one page
	$args = array(
		'post_type' => 'twabc',
		'posts_per_page' => '-1',
		'orderby' => $atts['orderby'],
		'order' => $atts['order']
	);
	if($atts['category'] != ''){
		$args['twabc_category'] = $atts['category'];
	}
	if(!isset($atts['image_size'])) $atts['image_size'] = 'full';
	if(!isset($atts['use_javascript_animation'])) $atts['use_javascript_animation'] = '1';
	if($atts['id'] != ''){
		$args['p'] = $atts['id'];
	}

	// Collect the carousel content. Needs printing in two loops later (bullets and content)
	$loop = new WP_Query( $args );
	$images = array();
	$output = '';
	while ( $loop->have_posts() ) {
		$loop->the_post();
		if ( '' != get_the_post_thumbnail(get_the_ID(), $atts['image_size']) ) {
			$post_id = get_the_ID();
			$title = get_the_title();
			$content = get_the_excerpt();
			
			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), $atts['image_size']);
			$image_src_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'featured_preview');
			$image_src = $image_src[0];
			$image_src_thumb = $image_src_thumb[0];
			$images[] = array('post_id' => $post_id, 'title' => $title, 'content' => $content, 'img_src' => $image_src);
		}
	}
	// Check we actually have something to show
	if(count($images) > 0){
		ob_start();
		wp_enqueue_style( 'twabc', plugins_url('asset/css/twabc-advanced.css',__FILE__ ), array(), TWABC_VERSION );
		?>
		<div id="twabc_<?php echo $id; ?>" class="carousel slide <?php if($atts['effect'] === 'fade') {echo "carousel-fade"; } else if($atts['effect'] === 'vslide') {echo "vertical-slider"; } ?> <?php if($atts['showindicator'] === 'numbered') {echo "carousel-indicator-numbered"; } ?>" <?php if($atts['use_javascript_animation'] == '0'){ echo ' data-ride="carousel"'; } ?> data-interval="<?php echo $atts['interval']; ?>">
			
			<?php // First content - the carousel indicators
			if( count( $images ) > 1 ){ 
			
			if($atts['showindicator'] != 'false') { ?>
            
				<ol class="carousel-indicators">
				<?php 
				$ind = 0;
				foreach ($images as $key => $image) { 
				$ind++;
				?>
					<li data-target="#twabc_<?php echo $id; ?>" data-slide-to="<?php echo $key; ?>" <?php echo $key == 0 ? 'class="active"' : ''; ?>><?php if($atts['showindicator'] === 'numbered') {echo $ind;} ?></li>
				<?php } ?>
				</ol>
			<?php }
			}?>

			<div class="carousel-inner">
			<?php
			// Carousel Content
			foreach ($images as $key => $image) {
				?>

				<div class="item <?php echo $key == 0 ? 'active' : ''; ?>" id="twabc-item-<?php echo $image['post_id']; ?>">
					<?php
					// Regular behaviour - display image with link around it
					// The Caption div
					// Caption
						echo '<img src="'.$image['img_src'].'" alt="'.$image['title'].'" />';
						if(strlen($image['content']) > 0){
							echo '<div class="carousel-caption">';
							echo $image['content'];
							echo '</div>';
						}	
					?>
				</div>
			<?php } ?>
			</div>

			<?php // Previous / Next controls
			if( count( $images ) > 1 ){
				if($atts['showcontrols'] === 'true' && $atts['twbs'] == '3') { ?>
					<a class="left carousel-control" href="#twabc_<?php echo $id; ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
					<a class="right carousel-control" href="#twabc_<?php echo $id; ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
				<?php }
			} ?>

		</div>

        <?php // Javascript animation fallback
        if($atts['use_javascript_animation'] == '1'){ ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#twabc_<?php echo $id; ?>').carousel({
					interval: <?php echo $atts['interval']; ?>
				});
			});
		</script>
        <?php }

        // Collect the output
		$output = ob_get_contents();
		ob_end_clean();
	}
	
	// Restore original Post Data
	wp_reset_postdata();  
	
	return $output;
}

