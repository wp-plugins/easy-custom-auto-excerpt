<?php
/*
Plugin Name: Easy Custom Auto Excerpt
Plugin URI: http://www.tonjoo.com/easy-custom-auto-excerpt/
Description: Auto Excerpt for your post on home, search and archive.
Version: 1.0.8
Author: Todi Adiyatmo Wijoyo
Author URI:  http://todiadiyatmo.com
*/

 define("TONJOO_ECAE", 'easy-custom-auto-excerpt');

function tonjoo_ecae_plugin_init()
{
   
    // Localization
    load_plugin_textdomain(TONJOO_ECAE, false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    
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

add_filter('the_content', 'tonjoo_ecae_execute', 10);

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
    //total excerpt current width
    $total_width=0;

    $options = get_option('tonjoo_ecae_options');
    $options = tonjoo_ecae_load_default($options);
    global $post;

    //check shortcode optons

    if ($options['strip_shortcode'] == 'yes') {
        $content = strip_shortcodes($content);
    }
    

    $pos = strpos($content, '<!--more-->');
    
    $array_replace_list = array();
    
    //if read more
    if ($pos) {
        $content = substr($content, 0, $pos);
    } elseif ($post->post_excerpt != '') {
        $content = $post->post_excerpt;
        
    } elseif ($width == 0) {
        $content = '';
    } elseif (!(strlen($content) <= (int) $width)) {
        
        
        
        $hyperlink_image_replace = new eace_content_regex("|#", "/<a[^>]*>(\n|\s)*(<img[^>]+>)(\n|\s)*<\/a>/",$options,true);
        $image_replace           = new eace_content_regex("|(", "/<img[^>]+\>/",$options,true );
        
        //biggest -> lowest the change code

        $html_replace = array();

        $extra_markup = $options['extra_html_markup'];

        $extra_markup = trim($extra_markup);

        //prevent white space explode
        if($extra_markup!='')
            $extra_markup = explode('|',$extra_markup);
        else
            $extra_markup = array();



        $extra_markup_tag=array('*=','(=',')=','_=','<=','>=','/=','\=',']=','[=','{=','}=','|=');

        //default order
        $array_replace_list['video']='=}';
        $array_replace_list['table']='={';    
        $array_replace_list['p']='=!';
        $array_replace_list['b']='=&';
        $array_replace_list['a']='=*';
        $array_replace_list['i']='=)';
        $array_replace_list['h1']='=-';
        $array_replace_list['h2']='`=';
        $array_replace_list['h3']='!=';
        $array_replace_list['h4']='#=';
        $array_replace_list['h5']='$=';
        $array_replace_list['h6']='%=';
        $array_replace_list['ul']='=#';
        $array_replace_list['ol']='=$';
        $array_replace_list['strong']='=(';
        $array_replace_list['pre']='=@';
        $array_replace_list['blockquote']='=^';
     


   
        foreach ($extra_markup as $markup) {

     

            $counter = 0;

            if(!isset($array_replace_list[$markup]))
                $array_replace_list[$markup]=$extra_markup_tag[$counter];

            $counter++;
        }

        //push every markup into processor
        foreach ($array_replace_list as $key=>$value) {


            //use image processing algorithm for table and video
            if($key=='video'||$key=='table')
                $push   = new eace_content_regex("{$value}", "/<{$key}.*?\>([^`]*?)<\/{$key}>/",$options,true);
            else
                $push   = new eace_content_regex("{$value}", "/<{$key}.*?\>([^`]*?)<\/{$key}>/",$options);

            array_push($html_replace, $push);
        }



      

        $crayon_replace = new eace_content_regex("+*", "/<!-- Crayon.*?-->([^`]*?)-->/",$options);
        
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
      
        foreach ($html_replace as $replace) {



             $replace->replace($content, $width,false,$total_width);
        }

  
       
        $crayon_replace->replace($content, $width,false,$total_width);
        
        //use wp kses to fix broken element problem
        $content = wp_kses($content, array());

        if(strpos($content,'<!--STOP THE EXCERPT HERE-->')===false){

            //give the stop mark so the plugin can stop
            $content=$content.'<!--STOP THE EXCERPT HERE-->';
        }

        //strip the text
        $content = substr($content, 0, strpos($content,'<!--STOP THE EXCERPT HERE-->'));

        //do the restore 3 times, avoid nesting
        $crayon_replace->restore($content);
        foreach ($html_replace as $restore) {
             $restore->restore($content, $width);
        }
        foreach ($html_replace as $restore) {
             $restore->restore($content, $width);
        }
        foreach ($html_replace as $restore) {
             $restore->restore($content, $width);
        }
        $crayon_replace->restore($content);
        
        
        if ($option_image == 'yes') {
            
            $hyperlink_image_replace->restore($content,false,true);
            $image_replace->restore($content,false,true);
            
        } elseif ($option_image == 'first-image') {
            
            
            //catch all of hyperlink and image on the content => '|#'  and '|('' 
            preg_match_all('/\|\([0-9]*\|\(|\|\#[0-9]*\|\#/', $content, $result, PREG_PATTERN_ORDER);
           
             if (isset($result[0])) {
                
                $remaining = array_slice($result[0], 0, 1);
                
                if(isset($remaining[0])){                
                    
                    //delete remaining image
                    $content = preg_replace('/\|\([0-9]*\|\C/', '', $content);
                    $content = preg_replace('/\|\#[0-9]*\|\#/', '', $content);

                    //restore first image found  
                    $content = "<div style='text-align:center'>" . $remaining[0] . "</div>" . $content;

                    $hyperlink_image_replace->restore($content, 1,true);
                    $image_replace->restore($content, 1,true);
                }
            }
        } elseif ($option_image == 'featured-image') {
            //check featured image;
            $featured_image = has_post_thumbnail(get_the_ID());
            $image = false;

            if ($featured_image)
                $image = get_the_post_thumbnail(get_the_ID());
            
            // only put image if there is image :p
            if($image)
                $content = "<div style='text-align:center'>" . $image . "</div>" . $content;


        }

        //delete remaining image
        $content = preg_replace('/\|\([0-9]*\|\C/', '', $content);
        $content = preg_replace('/\|\#[0-9]*\|\#/', '', $content);



        
        
    }

    //delete remaining
    $extra_markup_tag=array('*='.'(=',')=','_=','<=','>=','/=','\=',']=','[=','{=','}=','|=');

    foreach ($extra_markup_tag as $value) {

        $char = str_split($value);


        $content = preg_replace("/"."\\"."{$char[0]}"."\\"."{$char[1]}"."[0-9]*"."\\"."{$char[0]}"."\\"."{$char[1]}"."/", '', $content);
   
    }
    


    foreach($array_replace_list as $key=>$value) {


        $char = str_split($value);


        $content = preg_replace("/"."\\"."{$char[0]}"."\\"."{$char[1]}"."[0-9]*"."\\"."{$char[0]}"."\\"."{$char[1]}"."/", '', $content);
     }
    
    
    $link = get_permalink();
    
    $block = '';
    if ($options['read_more_new_line'] != 'no') {
        $block = 'display:block;';
    }
    
    if (trim($options['read_more']) != '-') {

        //failsafe
        $options['read_more_text_before'] = isset($options['read_more_text_before'] )? $options['read_more_text_before']  : '...';

        $readmore = $options['read_more_text_before'] . " <a class='ecae-link' style='text-align:" . $options['read_more_align'] . ";" . $block . "' href='$link'>{$options['read_more']}</a>";

        $content = str_replace('<!-- READ MORE TEXT -->',$readmore, $content);
    }
    
    if ($justify != "no") {
        $content = "<div class='ecae' style='text-align:$justify'>" . $content . "</div>";
    }


    
    
    return "<!-- Generated by Easy Custom Auto Excerpt -->$content<!-- Generated by Easy Custom Auto Excerpt -->";
}


class eace_content_regex
{
    var $key;
    var $holder;
    var $regex;
    var $unique_char;
    var $image;
    var $options;
    
    public function __construct($unique_char, $regex,$options,$image=false)
    {
        
        $this->regex       = $regex;
        $this->unique_char = $unique_char;

        $this->image = $image;
        $this->options = $options;


    }
    
    public function replace(&$content, &$width, $number = false, &$total_width=0)
    {
        //get all image in the content    
        preg_match_all($this->regex, $content, $this->holder, PREG_PATTERN_ORDER);
        
        $this->key = 0;
        

        //only cut bellow the $number variabel treshold ( to limit the number of replacing)
        if ($number)
            array_slice($this->holder[0], 0, $number);
        
       

 

        foreach ($this->holder[0] as $text) {   
        
             
            $unique_key = "{$this->unique_char}{$this->key}{$this->unique_char}";
            
            $content   = str_replace($text, $unique_key, $content);


            if(!$this->image&&strpos($content,'<!--STOP THE EXCERPT HERE-->')===false){

            
                $total_width = $total_width + strlen(wp_kses($text,array()));
         

                if($total_width>$width){
                    //tell plugin to stop at this point

                    $content = str_replace($unique_key, "{$unique_key}<!--STOP THE EXCERPT HERE--><!--- SECRET END TOKEN ECAE --->",$content);
                    //exit loop


          
                    //if use word cut technique
                    if($this->options['excerpt_method']=='word'){
                        $overflow = $total_width - $width;

                        $current_lenght =  strlen(wp_kses($text,array()));

                        $overflow = $current_lenght-$overflow;

                        $this->holder[0][$this->key] = substr($text,0,$overflow);

                        $this->holder[0][$this->key] = wp_kses($this->holder[0][$this->key],array()); 

                        $this->holder[0][$this->key]  = "<p>{$this->holder[0][$this->key]}<!-- READ MORE TEXT --></p>";  
                    }
                    //if use preserve paragraph technique
                    else{
                         $this->holder[0][$this->key]  = "{$this->holder[0][$this->key]}<!-- READ MORE TEXT -->";  
                    }         


                     //strip the text
                    $content = substr($content, 0, strpos($content,'<!--- SECRET END TOKEN ECAE --->'));
                    

                    break;
                }    
            }

             $this->key = $this->key + 1;
            
        }
 
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