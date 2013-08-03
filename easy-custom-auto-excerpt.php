<?php
/*
Plugin Name: Easy Custom Auto Excerpt
Plugin URI: http://www.tonjoo.com/easy-custom-auto-excerpt/
Description: Auto Excerpt for your post on home, search and archive.
Version: 0.92
Author: Todi Adiyatmo Wijoyo
Author URI:  http://todiadiyatmo.com
*/
add_filter( 'the_content', 'tonjoo_ecae_execute',10000000000000 );

function tonjoo_ecae_execute($content,$width=400){

	//ambil options dari db
	$options = get_option('tonjoo_ecae_options');



	$width=$options['width'];
	$justify=$options['justify'];
	
	if($options['home']=="yes" && is_home()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}

	if($options['archive']=="yes" && is_archive()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}
	
	if($options['search']=="yes" && is_search()){
		return tonjoo_ecae_excerpt($content, $width,$justify);
	}
	
	else {
		return $content;
	}
	
}


function tonjoo_ecae_excerpt($content, $width, $justify) {

	$options = get_option('tonjoo_ecae_options');

	if (!strlen($content) <= $width) {
		


		

		//trim image
		if($options['show_image']=='yes') :

			//get all image in the content	
			preg_match_all("/<a[^>]+\>+<img[^>]+\>+\<\/a>/", $content,$img_content,PREG_PATTERN_ORDER );
			
			$img_key = 0;
			foreach ($img_content[0] as $img) {

				$content = str_replace($img,"#{$img_key}#", $content);
				$img_key = $img_key+1;
			}	
			$width = $width+$img_key*3;
		else :
			//remove image
			$content = preg_replace("/<a[^>]+\>+<img[^>]+\>+\<\/a>/","" ,$content);
		endif;
		//find last space within length
		$last_space = strrpos(substr($content, 0, $width), ' ');
		$content = substr($content, 0, $last_space);

		//restore_image
		if($options['show_image']=='yes') :
		
			for($i=0;$i<$img_key;$i++){
			
				 $content = str_replace("#{$i}#", $img_content[0][$i], $content);

			}
		endif;
	}
	


	$link = get_permalink();
	$content .= " <a href='$link'>{$options['read_more']}</a>";
	if($justify!="no") {
		$content = "<div style='text-align:$justify'>" . $content . "</div>";
	}
	return $content;
}





require_once( plugin_dir_path( __FILE__ ) . 'tonjoo-library.php');
require_once( plugin_dir_path( __FILE__ ) . 'default.php');
//load option page
require_once( plugin_dir_path( __FILE__ ) . 'options-page.php');