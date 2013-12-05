<?php
/*
Plugin Name: Easy Custom Auto Excerpt
Plugin URI: http://www.tonjoo.com/easy-custom-auto-excerpt/
Description: Auto Excerpt for your post on home, search and archive.
Version: 1.0.7
Author: Todi Adiyatmo Wijoyo
Author URI:  http://todiadiyatmo.com
*/

function tonjoo_ecae_plugin_init()
{
  define(TONJOO_ECAE,'easy-custom-auto-excerpt');
// Localization
 load_plugin_textdomain( TONJOO_ECAE, false,  dirname( plugin_basename( __FILE__ ) ).'/languages' ); 


}

add_action('plugins_loaded', 'tonjoo_ecae_plugin_init');

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'tonjoo_ecae_donate');

function tonjoo_ecae_donate($links)
{
    $donate_link = '<a href="http://tonjoo.com/donate/">Donate</a>';
    array_unshift($links, $donate_link);
    return $links;
}

add_filter('the_content', 'tonjoo_ecae_execute', 1);

function tonjoo_ecae_execute($content, $width = 400)
{
    
    //ambil options dari db
    $options = get_option('tonjoo_ecae_options');
    
    $options = tonjoo_ecae_load_default($options);
    
    
    
    $width   = $options['width'];
    $justify = $options['justify'];
    
    if ($options['home'] == "yes" && is_home()) {
        return tonjoo_ecae_excerpt($content, $width, $justify);
    }
    
    if ($options['archive'] == "yes" && is_archive()) {
        return tonjoo_ecae_excerpt($content, $width, $justify);
    }
    
    if ($options['search'] == "yes" && is_search()) {
        return tonjoo_ecae_excerpt($content, $width, $justify);
    }
    
    else {
        return $content;
    }
    
}


function tonjoo_ecae_excerpt($content, $width, $justify)
{
    
    $options = get_option('tonjoo_ecae_options');
    $options = tonjoo_ecae_load_default($options);
    global $post;
    
    
    
    $pos = strpos($post->post_content, '<!--more-->');
    
    if ($options['strip_shortcode'] == 'yes') {
        $content = strip_shortcodes($content);
    }
    
    //if read more
    if ($pos) {
        $content = substr($content, 0, $pos);
    } elseif ($post->post_excerpt != '') {
        $content = $post->post_excerpt;
    } elseif (!(strlen($content) <= (int) $width)) {
        
        
        $hyperlink_image_replace = new eace_content_regex("|#", "/<a[^>]*>(\n|\s)*(<img[^>]+>)(\n|\s)*<\/a>/");
        $image_replace           = new eace_content_regex("|(", "/<img[^>]+\>/");
        
        //biggest -> lowest
        $pre_replace        = new eace_content_regex("+=", "/<pre.*?\>([^`]*?)<\/pre>/");
        $ul_replace         = new eace_content_regex("=+", "/<ul.*?\>([^`]*?)<\/ul>/");
        $ol_replace         = new eace_content_regex("/=", "/<ol.*?\>([^`]*?)<\/ol>/");
        $table_replace      = new eace_content_regex("=/", "/<table.*?\>([^`]*?)<\/table>/");
        $blockquote_replace = new eace_content_regex("/+", "/<blockquote.*?\>([^`]*?)<\/blockquote>/");
        $hyperlink_replace  = new eace_content_regex("+/", "/<a.*?\>([^`]*?)<\/a>/");
        $bold_replace       = new eace_content_regex("?+", "/<b.*?\>([^`]*?)<\/b>/");
        $italic_replace     = new eace_content_regex("+?", "/<i.*?\>([^`]*?)<\/i>/");
        $heading_replace    = new eace_content_regex("+?", "/<(h1|h2|h3|h4).*?\>([^`]*?)<\/(h1|h2|h3|h4)>/");
        $crayon_replace     = new eace_content_regex("+*", "/<!-- Crayon.*?-->([^`]*?)-->/");
        
        //trim image
        $option_image = $options['show_image'];
        
        
        if ($option_image == 'yes' || $option_image == 'first-image'):
            $number = false;
        //limit the image excerpt
            if ($option_image == 'first-image')
                $number = 1;
            
            $hyperlink_image_replace->replace($content, $width, $number);
            
            $image_replace->replace($content, $width, $number);
        else:
            //remove image , this is also done for featured-image option
            $hyperlink_image_replace->remove($content);
            $image_replace->remove($content);
        endif;
        
        
        $pre_replace->replace($content, $width);
        $ul_replace->replace($content, $width);
        $ol_replace->replace($content, $width);
        $table_replace->replace($content, $width);
        $blockquote_replace->replace($content, $width);
        $hyperlink_replace->replace($content, $width);
        $bold_replace->replace($content, $width);
        $italic_replace->replace($content, $width);
        $heading_replace->replace($content, $width);
        $crayon_replace->replace($content, $width);
        
        //use wp kses to fix broken element problem
        $content = wp_kses($content, array());
        
        //find last space within length
        $last_space = strrpos(substr($content, 0, $width), ' ');
        
        //strip the text
        $content = substr($content, 0, $last_space);
        
        $crayon_replace->restore($content);
        $pre_replace->restore($content);
        $ul_replace->restore($content);
        $ol_replace->restore($content);
        $table_replace->restore($content);
        $blockquote_replace->restore($content);
        $hyperlink_replace->restore($content);
        $bold_replace->restore($content);
        $heading_replace->restore($content);
        $italic_replace->restore($content);

        
        if ($option_image == 'yes') {
            
            $hyperlink_image_replace->restore($content);
            $image_replace->restore($content);

        } elseif ($option_image == 'first-image') {
            
            
            //catch all of hyperlink and image on the content => '|#'  and '|('' 
            preg_match_all('/\|\([0-9]*\|\C|\|\#[0-9]*\|\#/', $content, $result, PREG_PATTERN_ORDER);
            
            if (isset($result[0])) {
                
                $remaining = array_slice($result[0], 0, 1);
                
                //delete remaining image
                $content = preg_replace('/\|\([0-9]*\|\C/', '', $content);
                $content = preg_replace('/\|\#[0-9]*\|\#/', '', $content);
                
                //restore first image found  
                $content = "<div style='text-align:center'>" . $remaining[0] . "</div>" . $content;
                
                $hyperlink_image_replace->restore($content, 1);
                $image_replace->restore($content, 1);

            }
        } elseif ($option_image == 'featured-image') {
            //check featured image;
            $featured_image = has_post_thumbnail(get_the_ID());
            
            if ($featured_image)
                $image = get_the_post_thumbnail(get_the_ID());

            // 
            $content = "<div style='text-align:center'>" . $image . "</div>". $content;
        }

        
    }
    
    $link = get_permalink();
    
    if (trim($options['read_more']) != '-') {
        $content .= " <a class='ecae-link' href='$link'>{$options['read_more']}</a>";
    }
    
    
    
    if ($justify != "no") {
        $content = "<div class='ecae' style='text-align:$justify'>" . $content . "</div>";
    }
    
    return "<!-- Generated by Easy Custom Auto Excerpt -->$content";
}


class eace_content_regex
{
    var $key;
    var $holder;
    var $regex;
    var $unique_char;
    
    public function __construct($unique_char, $regex)
    {
        
        $this->regex       = $regex;
        $this->unique_char = $unique_char;
    }
    
    public function replace(&$content, &$width, $number = false)
    {
        //get all image in the content    
        preg_match_all($this->regex, $content, $this->holder, PREG_PATTERN_ORDER);
        
        $this->key = 0;
        
        
        
        //only cut bellow the $number variabel treshold ( to limit the number of replacing)
        if ($number)
            array_slice($this->holder[0], 0, $number);
        
        
        foreach ($this->holder[0] as $img) {
            
            
            $content   = str_replace($img, "{$this->unique_char}{$this->key}{$this->unique_char}", $content);
            $this->key = $this->key + 1;
            
            
            
        }
        
        //calculate the extra character need for the excerpt to work properly
        $width = $width + $this->key * 5;
        
        
    }
    function restore(&$content, $maximal = false)
    {
        //maximal number to restore
        if (!$maximal)
            $maximal = $this->key;
        
        //serves as counter, how many replace are made
        $i = 0;
        
        for ($i; $i < $maximal; $i++) {
            if (isset($this->holder[0][$i]))
                $content = str_replace("{$this->unique_char}{$i}{$this->unique_char}", $this->holder[0][$i], $content);
        }
        
        return $i;
    }
    
    function remove(&$content)
    {
        
        $content = preg_replace($this->regex, "", $content);
    }
}


require_once(plugin_dir_path(__FILE__) . 'tonjoo-library.php');
require_once(plugin_dir_path(__FILE__) . 'default.php');
//load option page
require_once(plugin_dir_path(__FILE__) . 'options-page.php');