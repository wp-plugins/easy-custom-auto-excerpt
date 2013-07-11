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
		"Tonjoo Easy Custom Auto Excerpt Options Page", 
		'Excerpt Setting', 
		'edit_theme_options', 
		'admin_menu', 
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
		echo "<h2>Tonjoo Easy Custom Auto Excerpt Options</h2>";

		/**
		 * Freaking Hack :D
		 */
 		?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong>Options Save</strong></p></div>
		<?php endif; ?>

		<br>
	Fluid Responsive Slideshow by <a href='http://www.tonjoo.com'>tonjoo</a> ~ 
	<a href='http://www.tonjoo.com/easy-custom-auto-excerpt/'>Plugin Page</a> | 
	<a href='http://wordpress.org/support/view/plugin-reviews/easy-custom-auto-excerpt?filter=5'>Please Rate :)</a> |
	<a href='http://wordpress.org/extend/plugins/easy-custom-auto-excerpt/'>Comment</a> Or <a href='http://wordpress.org/support/plugin/easy-custom-auto-excerpt'>Bug Report</a> |
	<a href='http://wordpress.org/extend/plugins/easy-custom-auto-excerpt/faq/'>FAQ</a> 
	<br>
	<br>


		<form method="post" action="options.php">
			<?php settings_fields('tonjoo_options'); ?>
			<?php $options = get_option('tonjoo_ecae_options'); 

				if(!$options['width'])
					$options['width']=400;

			?>
			<h2>Page Excerpt</h2>
			<table class="form-table">

			<?php

			$text_options = Array(
			  	'label'=>'Default Word Excerpt',
			  	'name'=>'tonjoo_ecae_options[width]',
			  	'value'=>$options['width']
		  	);
			
			tj_print_text_option($text_options);

			
		$excerpt_yes_options = array(
						'0' => array(
							'value' =>	'no',
							'label' =>  'No'
						),
						'1' => array(
							'value' =>	'yes',
							'label' =>  'Yes' 
						)
					);

		$justify_options = array(
						'0' => array(
							'value' =>	'no',
							'label' =>  'No'
						),
						'1' => array(
							'value' =>	'left',
							'label' =>  'Left' 
						),
						'2' => array(
							'value' =>	'right',
							'label' =>  'Right' 
						),
						'3' => array(
							'value' =>	'justify',
							'label' =>  'Justify' 
						),
						'4' => array(
							'value' =>	'center',
							'label' =>  'Center' 
						)
					);


		$home_select = array(
						"name"=>"tonjoo_ecae_options[home]",
						"description" => "Excerpt on home",
						"label" => "Home Excerpt",
						"value" => $options['home'],
						"select_array" => $excerpt_yes_options,
					);
					
		$archive_select = array(
						"name"=>"tonjoo_ecae_options[archive]",
						"description" => "Excerpt on category/author/archive",
						"label" => "Archive Excerpt",
						"value" => $options['archive'],
						"select_array" => $excerpt_yes_options,
					);
		
		$search_select = array(
						"name"=>"tonjoo_ecae_options[search]",
						"description" => "Excerpt on search result",
						"label" => "Search Excerpt",
						"value" => $options['search'],
						"select_array" => $excerpt_yes_options,
					);
		
		$justify_select = array(
						"name"=>"tonjoo_ecae_options[justify]",
						"description" => "The plugin will try to align the text on the excerpt page",
						"label" => "Text Align",
						"value" => $options['justify'],
						"select_array" => $justify_options,
					);
					
		echo tj_print_select_option($home_select);
		echo tj_print_select_option($search_select);
		echo tj_print_select_option($archive_select);
		echo tj_print_select_option($justify_select);	

			?>
			</table>
			
			
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
		</form>
		<p>If you have any questions,comment or suggestion please contact me via tonjoocorp[at]gmail.com or <a href="http://www.tonjoo.com/easy-custom-auto-excerpt/">visit our plugin site</a></p>
	</div>
	<?php
	}

	