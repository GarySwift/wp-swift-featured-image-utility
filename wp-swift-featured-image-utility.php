<?php
/*
Plugin Name:       WP Swift: Featured Image Utility
Plugin URI: 
Description:       A utility plugin that handles featured images in WordPress content
Version:           1.0.0
Author:            Gary Swift
Author URI:        https://github.com/GarySwift
License:           MIT License
License URI:       http://www.opensource.org/licenses/mit-license.php
Text Domain:       wp-swift-featured-image-utility
*/

/*
 * Get the featured image of post and return as ACF image object
 *
 * @param - $post, $sizes
 * 
 * @return - $image (array)
 */
function get_featured_image($post_id=false) {
	if(!$post_id) {
		global $post;
		$post_thumbnail_id = get_post_thumbnail_id( $post );
	}
	else {
		$post=get_post($post_id);
		$post_thumbnail_id = $post->ID;

	}
	$sizes = array('letterbox', 'medium_large', 'fp-small', 'fp-medium', 'fp-large', 'fp-xlarge', 'featured-small', 'featured-medium', 'featured-large', 'featured-xlarge');
	
	$image=false;
	if ( has_post_thumbnail($post->ID) ) :
 		$image = array(); 
		$post_thumbnail_id = get_post_thumbnail_id( $post );
		$thumb = get_post( $post_thumbnail_id );
	    $image['title'] = $thumb->post_title;
	    $image['alt'] = get_post_meta( $thumb->ID, '_wp_attachment_image_alt', true ); //alt text
	    $image['caption'] = $thumb->post_excerpt;
	    $image['description'] = $thumb->post_content;
    	$thumb_url_array = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail', true);
    	$image['sizes']['thumbnail'] = $thumb_url_array[0]; 
    	$medium_url_array = wp_get_attachment_image_src($post_thumbnail_id, 'medium', true);
    	$image['sizes']['medium'] = $medium_url_array[0]; 
    	$large_url_array = wp_get_attachment_image_src($post_thumbnail_id, 'large', true);
    	$image['sizes']['large'] = $large_url_array[0];
    	   	$large_width=$large_url_array[1]; 
    	$large_height=$large_url_array[2]; 
    	if($large_height>$large_width) {
    		$image['orientation']='portrait'; 
    	}
    	else {
    		$image['orientation']='landscape'; 
    	}
    	if($large_height>1000) {
    		$image['fullsize']=' fullsize'; 
    	}
    	else {
    		$image['fullsize']=''; 
    	} 
    	$image['url'] = $thumb->guid;
    	foreach ($sizes as $size) {
			$large_url_array = wp_get_attachment_image_src($post_thumbnail_id, $size, true);
			$image['sizes'][$size] = $large_url_array[0]; 
    	}
	endif;
	return $image;
}

/*
 	Example Usage of get_featured_image()

	$sizes = array('letterbox', 'medium_large', 'thumbnail_large');
	$image =  get_featured_image($post, $sizes); 
	<img class=""  data-interchange="[<?php echo $image['sizes']['thumbnail_large']; ?>, small], [<?php echo $image['sizes']['medium_large']; ?>, medium], [<?php echo $image['sizes']['letterbox']; ?>, large]" alt="<?php echo ($image['alt'] ? $image['alt']  : 'Image'); ?>" title="<?php echo ($image['title'] ? $image['title']  : 'defaultImgTitle' ); ?>">
*/


function the_image($single_post=true, $display_size='large', $image_class='thumbnail') {
	global $post;

	// $sizes = array('medium_large', 'fp-small', 'fp-medium', 'fp-large', 'icon', 'letterbox', 'letterbox-medium' );

	// if ( !$single_post && ( (get_post_format( $post_id ) != 'gallery') && (get_post_format( $post_id ) != 'video') ) ):
		if( !$single_post && get_field('letterbox_image')) {
	        $image = get_field('letterbox_image');
	    	$image_small = $image['sizes']['medium_large'];
	        $image_large = $image['url'];
	        $image_link = $image['original_image']['sizes']['large'];		
		}
		else {
			$image =  get_featured_image(); //$post->ID
			if($image) {
				$image_small = $image['sizes']['medium_large'];
				$image_large = $image['sizes'][$display_size];
				$image_link = $image['sizes']['large'];	
			}	
		}

		if($image): 
			?>
			<div class="text-center">
				<a href="<?php echo $image_link ?>" class="image-popup-vertical-fit" title="<?php the_title() ?><?php echo ($image['caption'] ? ' &vert; '.$image['caption']  : '' ) ?>">
					<img class="<?php echo $image_class ?>"  data-interchange="[<?php echo $image_small ?>, small], [<?php echo $image_large; ?>, medium], [<?php echo $image_large; ?>, large]" alt="<?php echo ($image['alt'] ? $image['alt']  : 'Image'); ?>" title="<?php echo ($image['title'] ? $image['title']  : 'defaultImgTitle' ); ?>">
				</a>
			</div>
			<?php 
		endif;
	// endif; 
}