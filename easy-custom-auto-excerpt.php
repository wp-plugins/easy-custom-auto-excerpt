<?php
/*
Plugin Name: Easy Custom Auto Excerpt
Plugin URI: http://www.tonjoo.com/easy-custom-auto-excerpt/
Description: Auto Excerpt for your post on home, search and archive.
Version: 1.0.1
Author: Todi Adiyatmo Wijoyo
Author URI:  http://todiadiyatmo.com
*/
add_filter( 'the_content', 'tonjoo_ecae_execute',10000000000000 );

function tonjoo_ecae_execute($content,$width=400){

	//ambil options dari db
	$options = get_option('tonjoo_ecae_options');

	$options = tonjoo_ecae_load_default($options);



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
	$options = tonjoo_ecae_load_default($options);
	global $post;

	//return real excerpt if exist
	if($post->post_excerpt!='')
		return $post->post_excerpt;

	if (!strlen($content) <= $width) {
		


		
		$hyperlink_image_replace = new eace_content_regex("|#","/<a[^>]+\>+<img[^>]+\>+\<\/a>/");
		$image_replace = new eace_content_regex("|?","/<img[^>]+\>/");
		
		//biggest -> lowest
		$hyperlink_replace = new eace_content_regex("=+","/<a.*?\>([^`]*?)<\/a>/");
		$pre_replace = new eace_content_regex("+=","/<pre.*?\>([^`]*?)<\/pre>/");
		$ul_replace = new eace_content_regex("+=","/<ul.*?\>([^`]*?)<\/ul>/");
		$ol_replace = new eace_content_regex("+=","/<ol.*?\>([^`]*?)<\/ol>/");
		$table_replace = new eace_content_regex("+=","/<table.*?\>([^`]*?)<\/table>/");
		$blockquote_replace = new eace_content_regex("+=","/<blockquote.*?\>([^`]*?)<\/blockquote>/");
		// $div_replace = new eace_content_regex("+=","/<a.*?\>([^`]*?)<\/a>/");
		
		//trim image
		if($options['show_image']=='yes') :
			
			$hyperlink_image_replace->replace($content,$width);
			$image_replace->replace($content,$width);
			
			
		else :
			//remove image
			$hyperlink_image_replace->remove($content);
			$image_replace->remove($content);
		endif;

		//trim hyperlink
	
		$hyperlink_replace->replace($content,$width);
		$pre_replace->replace($content,$width);
		$ul_replace->replace($content,$width);
		$ol_replace->replace($content,$width);
		$table_replace->replace($content,$width);
		$blockquote_replace->replace($content,$width);
		

		//find last space within length
		$last_space = strrpos(substr($content, 0, $width), ' ');
		$content = substr($content, 0, $last_space);

		if($options['show_image']=='yes') :
			$hyperlink_image_replace->restore($content);
			$image_replace->restore($content);
		endif;
		$hyperlink_replace->restore($content);
		$pre_replace->restore($content);
		$ul_replace->restore($content);
		$ol_replace->restore($content);
		$table_replace->restore($content);
		$blockquote_replace->restore($content);
	

	}
	


	$link = get_permalink();
	$content .= " <a href='$link'>{$options['read_more']}</a>";
	if($justify!="no") {
		$content = "<div style='text-align:$justify'>" . $content . "</div>";
	}
	return $content;
}


 class eace_content_regex{
	var $key;
	var $holder;
	var $regex;
	var $unique_char;

	public function __construct($unique_char,$regex){

		$this->regex = $regex;
		$this->unique_char = $unique_char;
	}

	public function replace(&$content,&$width){
		//get all image in the content	
		preg_match_all($this->regex, $content,$this->holder,PREG_PATTERN_ORDER );
		
		$this->key = 0;
		foreach ($this->holder[0] as $img) {

			$content = str_replace($img,"{$this->unique_char}{$this->key}{$this->unique_char}", $content);
			$this->key = $this->key+1;
		}	
		$width = $width+$this->key*5;
	}
	function restore(&$content){
		for($i=0;$i<$this->key;$i++){
			
				$content =  str_replace("{$this->unique_char}{$i}{$this->unique_char}", $this->holder[0][$i], $content);

			}
	}

	function remove(&$content){
		 preg_replace($this->regex,"" ,$content);
	}
}


require_once( plugin_dir_path( __FILE__ ) . 'tonjoo-library.php');
require_once( plugin_dir_path( __FILE__ ) . 'default.php');
//load option page
require_once( plugin_dir_path( __FILE__ ) . 'options-page.php');