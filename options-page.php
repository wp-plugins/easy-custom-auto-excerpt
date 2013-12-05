<?php

add_action( 'admin_init', 'tonjoo_ecae_options_init' );
add_action( 'admin_menu', 'tonjoo_ecae_options_page' );

/**
 * Init plugin options to white list our options
 */
function tonjoo_ecae_options_init(){

	register_setting( 'tonjoo_options', 'tonjoo_ecae_options' );
}

/**
 * Load up the menu page
 */
function tonjoo_ecae_options_page() {
	
	// add_plugin_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	
	add_options_page( 
		__("Tonjoo Easy Custom Auto Excerpt Options Page",TONJOO_ECAE), 
		'Excerpt', 
		'edit_theme_options', 
		'tonjoo_excerpt', 
		'tonjoo_ecae_options_do_page' );
}

/**
 * Create the options page
 */
function tonjoo_ecae_options_do_page() {

	if (!current_user_can('manage_options')) {  
		wp_die('You do not have sufficient permissions to access this page.');  
	}  


	

	global $select_options, $radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	
	<style>
	label{
		vertical-align: top
	}

	.form-table input{
		width: 275px;
	}
	</style>
	<div class="wrap">
		<?php screen_icon();
		echo "<h2>".__("Tonjoo Easy Custom Auto Excerpt Options")."</h2>";
		?>


	<br>
	<?php _e("Easy Custom Auto Excerpt by",TONJOO_ECAE) ?> <a href='http://www.tonjoo.com'>tonjoo</a> ~ 
	<a href='http://www.tonjoo.com/easy-custom-auto-excerpt/'><?php _e("Plugin Page",TONJOO_ECAE) ?></a> | 
	<a href='http://wordpress.org/support/view/plugin-reviews/easy-custom-auto-excerpt?filter=5'><?php _e("Please Rate :)",TONJOO_ECAE) ?></a> |
	<a href='http://wordpress.org/extend/plugins/easy-custom-auto-excerpt/'><?php _e("Comment",TONJOO_ECAE) ?></a> Or <a href='http://wordpress.org/support/plugin/easy-custom-auto-excerpt'><?php _e("Bug Report",TONJOO_ECAE) ?></a> |
	<a href='http://wordpress.org/extend/plugins/easy-custom-auto-excerpt/faq/'><?php _e("FAQ",TONJOO_ECAE) ?></a> |
	<a href='http://tonjoo.com/donate'><?php _e("Donate Us",TONJOO_ECAE) ?></a> 
	<br>
	<br>


	<form method="post" action="options.php">
		<?php settings_fields('tonjoo_options'); ?>
		<?php 

		$options = get_option('tonjoo_ecae_options'); 

		tonjoo_ecae_load_default($options);

		?>
		<h2>Page Excerpt</h2>
		<table class="form-table">

			<?php

			$text_options = array(
				'label'=>__('Excerpt Size',TONJOO_ECAE),
				'description'=>__('Excerpt lenght, word will be preserved'),
				'name'=>'tonjoo_ecae_options[width]',
				'value'=>$options['width']
				);
			
			tj_print_text_option($text_options);

			$yes_no_options = array(
				'0' => array(
					'value' =>	'no',
					'label' =>  __("No",TONJOO_ECAE)
					),
				'1' => array(
					'value' =>	'yes',
					'label' =>  __("Show All Images",TONJOO_ECAE)
					),
				'2' => array(
					'value' =>	'first-image',
					'label' =>  __('Show only First Image',TONJOO_ECAE) 
					),
				'3' => array(
					'value' =>	'featured-image',
					'label' =>  __('Use Featured Image',TONJOO_ECAE)
					)
				);

			$image_select = array(
				"name"=>"tonjoo_ecae_options[show_image]",
				"description" => "",
				"label" => __("Display Image in excerpt",TONJOO_ECAE),
				"value" => $options['show_image'],
				"select_array" => $yes_no_options,
				);

			echo tj_print_select_option($image_select);


			$featured_image_excerpt = array(
				'0' => array(
					'value' =>	'no',
					'label' =>  __('No',TONJOO_ECAE)
					),
				'1' => array(
					'value' =>	'yes',
					'label' =>  __('Yes',TONJOO_ECAE) 
					)
				);

			$image_select = array(
				"name"=>"tonjoo_ecae_options[featured_image_excerpt]",
				"description" => "",
				"label" => __("Display featured image if post excerpt is set",TONJOO_ECAE),
				"value" => $options['featured_image_excerpt'],
				"select_array" => $featured_image_excerpt,
				);

			echo tj_print_select_option($image_select);

			$yes_no_options = array(
				'0' => array(
					'value' =>	'no',
					'label' =>  __('No',TONJOO_ECAE)
					),
				'1' => array(
					'value' =>	'yes',
					'label' =>  __('Yes',TONJOO_ECAE) 
					)
				);

			$image_select = array(
				"name"=>"tonjoo_ecae_options[strip_shortcode]",
				"description" => __("If you select 'yes' any shortcode will be ommited from the excerpt",TONJOO_ECAE),
				"label" => __("Strip shortcode in excerpt",TONJOO_ECAE),
				"value" => $options['strip_shortcode'],
				"select_array" => $yes_no_options,
				);

			echo tj_print_select_option($image_select);
			
			$excerpt_yes_options = array(
				'0' => array(
					'value' =>	'no',
					'label' =>  __('No',TONJOO_ECAE)
					),
				'1' => array(
					'value' =>	'yes',
					'label' =>  __('Yes',TONJOO_ECAE) 
					)
				);

			$justify_options = array(
				'0' => array(
					'value' =>	'no',
					'label' =>  __('No',TONJOO_ECAE)
					),
				'1' => array(
					'value' =>	'left',
					'label' =>  __('Left',TONJOO_ECAE) 
					),
				'2' => array(
					'value' =>	'right',
					'label' =>  __('Right',TONJOO_ECAE) 
					),
				'3' => array(
					'value' =>	'justify',
					'label' =>  __('Justify',TONJOO_ECAE) 
					),
				'4' => array(
					'value' =>	'center',
					'label' =>  __('Center',TONJOO_ECAE) 
					)
				);


			$home_select = array(
				"name"=>"tonjoo_ecae_options[home]",
				"description" => "",
				"label" => __("Home Excerpt",TONJOO_ECAE),
				"value" => $options['home'],
				"select_array" => $excerpt_yes_options,
				);

			$archive_select = array(
				"name"=>"tonjoo_ecae_options[archive]",
				"description" => "",
				"label" => __("Archive Excerpt",TONJOO_ECAE),
				"value" => $options['archive'],
				"select_array" => $excerpt_yes_options,
				);

			$search_select = array(
				"name"=>"tonjoo_ecae_options[search]",
				"description" => "",
				"label" => __("Search Excerpt",TONJOO_ECAE),
				"value" => $options['search'],
				"select_array" => $excerpt_yes_options,
				);

			$justify_select = array(
				"name"=>"tonjoo_ecae_options[justify]",
				"description" => __("The plugin will try to align the text on the excerpt page",TONJOO_ECAE),
				"label" => __("Text Align",TONJOO_ECAE),
				"value" => $options['justify'],
				"select_array" => $justify_options,
				);

			echo tj_print_select_option($home_select);
			echo tj_print_select_option($search_select);
			echo tj_print_select_option($archive_select);
			echo tj_print_select_option($justify_select);	


			$text_options = Array(
				'label'=>__('Read More Text.If you do not want read more, fill with "-" (without quote)',TONJOO_ECAE),
				'name'=>'tonjoo_ecae_options[read_more]',
				'value'=>$options['read_more']
				);
			
			tj_print_text_option($text_options);

			?>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value=<?php  _e("Save Options",TONJOO_ECAE) ?> />
		</p>
	</form>
	<p><?php _e("If you have any questions,comment or suggestion please contact us via support[at]tonjoo.com or ",TONJOO_ECAE) ?><a href="http://www.tonjoo.com/easy-custom-auto-excerpt/"> <?php _e('visit our plugin site',TONJOO_ECAE) ?></a></p>
</div>
<?php


}

