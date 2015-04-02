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
function tonjoo_ecae_options_page() 
{	
	// add_plugin_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	
	add_options_page( 
		__("Easy Custom Auto Excerpt Options Page",TONJOO_ECAE), 
		'Excerpt', 
		'moderate_comments', 
		'tonjoo_excerpt', 
		'tonjoo_ecae_options_do_page' );
}

/**
 * Create the options page
 */
function tonjoo_ecae_options_do_page() 
{
	global $select_options, $radio_options;

	require_once( plugin_dir_path( __FILE__ ) . 'walker_dropdown_multiple.php');

	if(!isset( $_REQUEST['settings-updated'])) {		
		$_REQUEST['settings-updated'] = false;
	}

	/**
	 * Save options
	 */
	if($_POST)
	{
		/**
		 * Excerpt in page
		 */
		$excerpt_in_page = isset($_POST['excerpt_in_page']) ? $_POST['excerpt_in_page'] : false;
		$excerpt_in_page_dump = '';
		if(is_array($excerpt_in_page))
		{
			foreach ($excerpt_in_page as $key => $value)
			{
				$excerpt_in_page_dump .= $value.'|';
			}
		}

		$_POST['tonjoo_ecae_options']['excerpt_in_page'] = $excerpt_in_page_dump;

		/**
		 * Advanced Post Page Excerpt
		 */
		$_POST['tonjoo_ecae_options']['home_post_type'] = isset($_POST['home_post_type']) ? serialize($_POST['home_post_type']) : '';
		$_POST['tonjoo_ecae_options']['home_category'] = isset($_POST['home_category']) ? serialize($_POST['home_category']) : '';

		$_POST['tonjoo_ecae_options']['frontpage_post_type'] = isset($_POST['frontpage_post_type']) ? serialize($_POST['frontpage_post_type']) : '';
		$_POST['tonjoo_ecae_options']['frontpage_category'] = isset($_POST['frontpage_category']) ? serialize($_POST['frontpage_category']) : '';
		
		$_POST['tonjoo_ecae_options']['archive_post_type'] = isset($_POST['archive_post_type']) ? serialize($_POST['archive_post_type']) : '';
		$_POST['tonjoo_ecae_options']['archive_category'] = isset($_POST['archive_category']) ? serialize($_POST['archive_category']) : '';

		$_POST['tonjoo_ecae_options']['search_post_type'] = isset($_POST['search_post_type']) ? serialize($_POST['search_post_type']) : '';
		$_POST['tonjoo_ecae_options']['search_category'] = isset($_POST['search_category']) ? serialize($_POST['search_category']) : '';

		$_POST['tonjoo_ecae_options']['excerpt_in_page_advanced'] = isset($_POST['excerpt_in_page_advanced']) ? serialize($_POST['excerpt_in_page_advanced']) : '';
		$_POST['tonjoo_ecae_options']['advanced_page'] = isset($_POST['advanced_page']) ? serialize($_POST['advanced_page']) : '';
		$_POST['tonjoo_ecae_options']['page_post_type'] = isset($_POST['page_post_type']) ? serialize($_POST['page_post_type']) : '';
		$_POST['tonjoo_ecae_options']['page_category'] = isset($_POST['page_category']) ? serialize($_POST['page_category']) : '';


		/**
		 * Tonjoo License
		 */
		if(class_exists('TonjooPluginLicenseECAE'))
		{
			$PluginLicense = new TonjooPluginLicenseECAE($_POST['tonjoo_ecae_options']['license_key']);
			$_POST = $PluginLicense->license_on_save($_POST);
		}

		/**
		 * Update options
		 */
		update_option('tonjoo_ecae_options', $_POST['tonjoo_ecae_options']);
		
		/**
		 * Redirect
		 */
		$location = admin_url("options-general.php?page=tonjoo_excerpt") . '&settings-updated=true';
		echo "<meta http-equiv='refresh' content='0;url=$location' />";
		echo "<h2>Loading...</h2>";
		exit();
	}

	if (!current_user_can('moderate_comments')) {  
		wp_die('You do not have sufficient permissions to access this page.');
	} 

	?>

	<div class="wrap">
	<?php echo "<h2>".__("Easy Custom Auto Excerpt Options",TONJOO_ECAE)."</h2>"; ?>

	<br>
	<?php _e("Easy Custom Auto Excerpt by",TONJOO_ECAE) ?> 
	<a href='https://tonjoostudio.com' target="_blank">Tonjoo Studio</a> ~ 
	<a href='https://tonjoostudio.com/addons/easy-custom-auto-excerpt/' target="_blank"><?php _e("Plugin Page",TONJOO_ECAE) ?></a> | 
	<a href='http://wordpress.org/support/view/plugin-reviews/easy-custom-auto-excerpt?filter=5' target="_blank"><?php _e("Please Rate :)",TONJOO_ECAE) ?></a> |
	<a href='http://wordpress.org/extend/plugins/easy-custom-auto-excerpt/' target="_blank"><?php _e("Comment",TONJOO_ECAE) ?></a> | 
	<a href='https://forum.tonjoostudio.com' target="_blank"><?php _e("Bug Report",TONJOO_ECAE) ?></a> |
	<a href='https://tonjoostudio.com/addons/easy-custom-auto-excerpt/#faq' target="_blank"><?php _e("FAQ",TONJOO_ECAE) ?></a>
	<br>
	<br>

	<?php if(isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated']==true) { ?>
	    <div id="message" class="updated">
	        <p><strong><?php _e('Settings saved.',TONJOO_ECAE) ?></strong></p>
	    </div>
	<?php } ?>

	<form method="post" action="">
		<?php settings_fields('tonjoo_options'); ?>
		<?php 

		$options = get_option('tonjoo_ecae_options'); 

		tonjoo_ecae_load_default($options);

		?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab" id='opt-general-tab' href='#opt-general'><?php _e('General Options',TONJOO_ECAE) ?></a>
			<a class="nav-tab" id='opt-location-tab' href='#opt-location'><?php _e('Excerpt Location',TONJOO_ECAE) ?></a>
			<a class="nav-tab" id='opt-readmore-tab' href='#opt-readmore'><?php _e('Read More Button',TONJOO_ECAE) ?></a>
			
			<?php if(class_exists('TonjooPluginLicenseECAE')): ?>
			<a class="nav-tab" id='opt-license-tab' href='#opt-license'><?php _e('License',TONJOO_ECAE) ?></a>
			<?php endif ?>
		</h2>

		<div class="metabox-holder columns-2" style="margin-right: 300px;">

		<!-- Extra style for options -->
		<style>
			.form-table td {
				vertical-align: middle;
			}

			.form-table th {
				width: 175px;
			}

			.form-table input[type=text], .form-table input[type=number], .form-table select {
				width: 200px;
				margin-right: 10px;
			}

			label.error{
			    margin-left: 5px;
			    color: red;
			}

			.form-table tr th {
			    text-align: left;
			    font-weight: normal;
			}

			.meta-subtitle {
			    margin: 0px -22px !important;
			    border-top:1px solid rgb(238, 238, 238);
			    background-color:#f6f6f6;
			}

			@media (max-width: 767px) {
			    .meta-subtitle {
			      margin-left: -12px !important;
			    }
			}

			label{
				vertical-align: top
			}
		</style>

		<!-- GENERAL OPTIONS -->
		<div id='opt-general' class="postbox-container group" style="width: 100%;min-width: 463px;float: left; ">
		<div class="meta-box-sortables ui-sortable">
		<div id="adminform" class="postbox">
		<h3 class="hndle"><span><?php _e('General Excerpt Options',TONJOO_ECAE) ?></span></h3>
		<div class="inside" style="z-index:1;">
		<table class="form-table">
			<?php require_once( plugin_dir_path( __FILE__ ) . 'options-page-general.php'); ?>
		</table>
		</div>
		</div>
		</div>
		</div>


		<!-- LOCATION OPTIONS -->
		<div id='opt-location' class="postbox-container group" style="width: 100%;min-width: 463px;float: left; ">
		<div class="meta-box-sortables ui-sortable">
		<div id="adminform" class="postbox">
		<h3 class="hndle"><span><?php _e('Excerpt Location Options',TONJOO_ECAE) ?></span></h3>
		<div class="inside" style="z-index:1;">
		<table class="form-table">
			<?php require_once( plugin_dir_path( __FILE__ ) . 'options-page-location.php'); ?>
		</table>
		</div>			
		</div>			
		</div>			
		</div>


		<!-- READMORE OPTIONS -->
		<div id='opt-readmore' class="postbox-container group" style="width: 100%;min-width: 463px;float: left; ">
		<div class="meta-box-sortables ui-sortable">
		<div id="adminform" class="postbox">
		<h3 class="hndle"><span><?php _e('Read More Button',TONJOO_ECAE) ?></span></h3>
		<div class="inside" style="z-index:1;">
		<table class="form-table">
			<?php require_once( plugin_dir_path( __FILE__ ) . 'options-page-readmore.php'); ?>
		</table>
		</div>			
		</div>			
		</div>			
		</div>

		<?php if(class_exists('TonjooPluginLicenseECAE')): ?>
		<!-- GENERAL OPTIONS -->
		<div id='opt-license' class="postbox-container group" style="width: 100%;min-width: 463px;float: left; ">
		<div class="meta-box-sortables ui-sortable">
		<div id="adminform" class="postbox">
		<h3 class="hndle"><span><?php _e('License',TONJOO_ECAE) ?></span></h3>
		<div class="inside" style="z-index:1;">
		<table class="form-table">
			<?php require_once( plugin_dir_path( __FILE__ ) . 'options-license.php'); ?>
		</table>
		</div>			
		</div>			
		</div>			
		</div>
		<?php endif ?>


		<!-- SIDEBAR -->
		<div class="postbox-container" style="float: right;margin-right: -300px;width: 280px;">
		<div class="metabox-holder" style="padding-top:0px;">	
		<div class="meta-box-sortables ui-sortable">
			<div id="email-signup" class="postbox">
				<h3 class="hndle"><span><?php _e('Save Options',TONJOO_ECAE) ?></span></h3>
				<div class="inside" style="padding-top:10px;">
					<?php _e('Save your changes to apply the options',TONJOO_ECAE) ?>
					<br>
					<br>
					<input type="submit" class="button-primary" value="<?php _e('Save Options',TONJOO_ECAE) ?>" />
					
				</div>
			</div>

			
			<!-- ADS -->
			<div class="postbox">			
				<script type="text/javascript">
					/**
					 * Setiap dicopy-paste, yang find dan dirubah adalah
					 * - var pluginName
					 * - premium_exist
					 */

					jQuery(function(){					
						var pluginName = "ecae";
						var url = 'https://tonjoostudio.com/jsonp/?promo=get&plugin=' + pluginName;
						var promoFirst = new Array();
						var promoSecond = new Array();

						<?php if(function_exists('is_ecae_premium_exist')): ?>
						var url = 'https://tonjoostudio.com/jsonp/?promo=get&plugin=' + pluginName + '&premium=true';
						<?php endif ?>

						// strpos function
						function strpos(haystack, needle, offset) {
							var i = (haystack + '')
								.indexOf(needle, (offset || 0));
							return i === -1 ? false : i;
						}

						jQuery.ajax({url: url, dataType:'jsonp'}).done(function(data){
							
							if(typeof data =='object')
							{
								var fristImg, fristUrl;

							    // looping jsonp object
								jQuery.each(data, function(index, value){

									<?php if(! function_exists('is_ecae_premium_exist')): ?>

									fristImg = pluginName + '-premium-img';
									fristUrl = pluginName + '-premium-url';

									// promoFirst
									if(index == fristImg)
								    {
								    	promoFirst['img'] = value;
								    }

								    if(index == fristUrl)
								    {
								    	promoFirst['url'] = value;
								    }

								    <?php else: ?>

								    if(! fristImg)
								    {
								    	// promoFirst
										if(strpos(index, "-img"))
									    {
									    	promoFirst['img'] = value;

									    	fristImg = index;
									    }

									    if(strpos(index, "-url"))
									    {
									    	promoFirst['url'] = value;

									    	fristUrl = index;
									    }
								    }

								    <?php endif; ?>

									// promoSecond
									if(strpos(index, "-img") && index != fristImg)
								    {
								    	promoSecond['img'] = value;
								    }

								    if(strpos(index, "-url") && index != fristUrl)
								    {
								    	promoSecond['url'] = value;
								    }
								});

								//promo_1
								jQuery("#promo_1 img").attr("src",promoFirst['img']);
								jQuery("#promo_1 a").attr("href",promoFirst['url']);

								//promo_2
								jQuery("#promo_2 img").attr("src",promoSecond['img']);
								jQuery("#promo_2 a").attr("href",promoSecond['url']);
							}
						});
					});
				</script>

				<!-- <h3 class="hndle"><span>This may interest you</span></h3> -->
				<div class="inside" style="margin: 23px 10px 6px 10px;">
					<div id="promo_1" style="text-align: center;padding-bottom:17px;">
						<a href="https://tonjoostudio.com" target="_blank">
							<img src="<?php echo plugins_url(HSCOMMENT_DIR_NAME."/assets/loading-big.gif") ?>" width="100%" alt="Tonjoo Studio">
						</a>
					</div>
					<div id="promo_2" style="text-align: center;">
						<a href="https://tonjoostudio.com" target="_blank">
							<img src="<?php echo plugins_url(HSCOMMENT_DIR_NAME."/assets/loading-big.gif") ?>" width="100%" alt="Tonjoo Studio">
						</a>
					</div>
				</div>
			</div>
		</div>
		</div>
		</div>	

		</div>
	</form>
</div>

<?php

}