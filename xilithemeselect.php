<?php
/*
Plugin Name: xili-theme-select
Plugin URI: http://dev.xiligroup.com/xilitheme-select/
Description: A plugin for WordPress that automatically redirects your blog's theme for optimized viewing on Apple's <a href="http://www.apple.com/iphone/">iPhone</a> and <a href="http://www.apple.com/ipodtouch/">iPod touch</a>.
Author: MS xiligroup dev 
Version: 2.0
Author URI: http://dev.xiligroup.com
License: GPLv2
Text Domain: xilithemeselect
Domain Path: /languages/

# 2.0 - 2015-05-08 - New version with more features to choose/select theme, New UI

# inspired initially from iwphone from Robot Content - 2007 - (http://www.contentrobot.com)
# 2008-10 : optimized rewritting with class and optional dashboard settings
# the "iphone / itouch" theme folder must have the same name with an extension ('_4touch') declared in instantiation
# examples of instantiations :
# $wp_ismobile = new xilithemeselector(true); //true if dashboard part 
# $wp_ismobile = new xilithemeselector(false); // false w/o dashboard (default extension = '_4touch')
# $wp_ismobile = new xilithemeselector(false,'_yourext'); //- false w/o dashboard and your extension
#
# USE var $wp_ismobile->iphone if necessary in your functions... (see code below at line > 105)

# This plugin is free software; you can redistribute it and/or
# modify it under the terms of the GNU Lesser General Public
# License as published by the Free Software Foundation; either
# version 2.1 of the License, or (at your option) any later version.
#
# This plugin is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
# Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this plugin; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

define('XILITHEME_VER','2.0');



class xilithemeselector {
	
	//var $listnonappledevices = array(array("Opera","_4opera"),array("opera mini","_4touch"),array("Windows CE","_4touch"),array("Blackberry",""),array("Android","_4touch"));

	var $cookienable = 0; /*disable by default see after instantiation*/

	var $current_theme_folder = ''; // stylesheet
	var $current_theme_Name = '';
	var $current_rule = '';
	var $xilitheme_rules = array();
	var $select_mode = true ; /*as usual*/
	var $thehook2; // used by dashboard
	var $uagent_obj; // var of class uagent_info

	public function __construct( $dashboard = true ) {

		require_once ( plugin_dir_path( __FILE__ ) . 'inc/mdetect.php' ); // Copyright 2010-2011, Anthony Hand https://github.com/ahand/mobileesp

		$this->uagent_obj = new uagent_info();

		$this->xilitheme_rules = $this->get_rules();

		$this->current_theme_folder = get_option('stylesheet'); // raw value before filtering

		add_action( 'plugins_loaded', array(&$this, 'detect_device_browser_to_filter') );

		add_shortcode ( 'xili-theme-link', array(&$this,'shortcode_theme_link' ) );

		//add_filter('template_directory', array(&$this,'change_template_path')); // add for pages (template)
		
		
		/*admin part - if not : use extension only */
		if ( $dashboard == true ) :
			
			add_action('admin_menu', array(&$this, 'add_admin_pages'));
			add_action('init', array(&$this, 'init_textdomain'));
			add_filter('plugin_action_links', array(&$this, 'add_plugin_actions'), 10, 2); /*0.9.3*/

			add_action( 'admin_init', array(&$this,'set_register_setting' ) );
			
			// $options =  get_option('xilithemeselect_options'); old

			// new rules
			$this->current_theme_Name = wp_get_theme();

			//display contextual help
			add_action( 'contextual_help', array( &$this,'add_help_text' ), 10, 3 ); /* 2.0 */
				
		endif;	
		
	}
	
	function init_textdomain () {
		load_plugin_textdomain('xilithemeselect', false, 'xilitheme-select/languages' );
	}
	
	/**
	 * Add action link(s) to plugins page
	 *
	 * @since 0.9.3
	 * @author MS
	 * @copyright Dion Hulse, http://dd32.id.au/wordpress-plugins/?configure-link and scripts@schloebe.de
	 */
		function add_plugin_actions( $links, $file ){
			static $this_plugin;

			if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=xilitheme_select">' . __('Settings') . '</a>';
				$links = array_merge( array( $settings_link ), $links); // before other links
			}
		return $links;
	}

	function get_rules(){
		$options = get_option('xilitheme-select-settings');
		if ( $options ) {
			$keys = array_keys($options);
			$rules = array();
			foreach ( $keys as $key ) {
				$key_rule = str_replace('device_assigned_','', $key);
				$rules[$key_rule]['stylesheet'] = $options[$key];
				if ( $options[$key]!= 'none' ) {
					$one_theme = wp_get_theme( $options[$key] );
					$rules[$key_rule]['template'] = ( $one_theme->get('Template')) ? $one_theme->get('Template') : "";
				}
			}
			return ( $rules );
		} else {
			return false;
		}
	}

	/**
	 * Detect current device/rule
	 * Fired by plugins_loaded
	 *
	 * @since 2.0
	 *
	 *
	 */
	function detect_device_browser_to_filter() {
		if( $this->cookienable == 1 ) $this->set_xili_theme_cookie();
		global $is_iphone, $is_safari;
		if ( $is_safari && stripos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) { // need to be detected before iPhone
			$this->current_rule = 'iPad';
		} else if ( $is_iphone ) {
			$this->current_rule = 'iPhone';
		} else if ( $this->uagent_obj->isTierIphone == $this->uagent_obj->true ) {  // rules from mdetect
			$this->current_rule = 'isTierIphone';
		} else if ($this->uagent_obj->DetectMobileQuick() == $this->uagent_obj->true ) {
			$this->current_rule = 'DetectMobileQuick';
		}
		add_filter( 'stylesheet', array(&$this, 'change_stylesheet') );
		add_filter( 'template', array(&$this,'change_template') );
	}

	/**
	 * Stylesheet filter
	 * Fired by stylesheet
	 *
	 * @since 2.0
	 *
	 *
	 */
	function change_stylesheet ( $stylesheet ) {
		//error_log ( $stylesheet . ' to change_stylesheet rule = ' . $this->current_rule );
		if ( $this->current_rule && $this->select_mode ) {

			if ( isset( $this->xilitheme_rules[$this->current_rule]['stylesheet'] ) && $this->xilitheme_rules[$this->current_rule]['stylesheet'] != 'none' ) {
				//error_log ( $stylesheet . ' to change_stylesheet rule = ' . $this->xilitheme_rules[$this->current_rule]['stylesheet'] );
				if (class_exists('xili_language')) {
					global $xili_language;
					if ( !is_admin()) {
						$xili_language->flag_settings_name = $this->xilitheme_rules[$this->current_rule]['stylesheet'] . '-xili-flag-options' ;
					}
				}
				return $this->xilitheme_rules[$this->current_rule]['stylesheet'];
			}

		}
		return $stylesheet; // no change
	}

	/**
	 * Template filter
	 * Fired by template
	 *
	 * @since 2.0
	 *
	 *
	 */
	function change_template ( $template ) {
		//error_log ( $template . ' to template rule = ' . $this->current_rule );
		if ( $this->current_rule && $this->select_mode ) {
			if ( isset( $this->xilitheme_rules[$this->current_rule]['template'] ) && $this->xilitheme_rules[$this->current_rule]['template'] != '' ) {
				return $this->xilitheme_rules[$this->current_rule]['template'];
			} else { // not child theme
				return $this->xilitheme_rules[$this->current_rule]['stylesheet'];
			}
		}
		return $template; // no change
	}

	/**
	 * Cookies to remember that preset selection is not used
	 * Called by detect_device_browser_to_filter
	 *
	 * @since 2.0
	 *
	 *
	 */
	function set_xili_theme_cookie() {
		$expire = time() + 30000000;
		if ( isset ($_GET["xilitheme"] ) ):
			setcookie("xilitheme" . COOKIEHASH,
							stripslashes( $_GET["xilitheme"] ),
							$expire,
							COOKIEPATH
							);
			if ( $_GET["xilitheme"] != 'false' )  { // && $_GET["xilitheme"] != 'true'
					$this->select_mode = true;
					$this->current_rule = $_GET["xilitheme"];
			} else {
				$this->select_mode = false;
				$this->current_rule = '';
			}
			$refreshinglink = $this->set_thelink( true, $this->current_rule );
			wp_redirect( $refreshinglink );
			exit;

		endif;
		$cookie = $this->get_xili_theme_cookie();
		if ( '' == $cookie ) {
			$this->select_mode = true;
		} else {
			$this->select_mode = ( $cookie != 'false' ) ? true : false ;
			$this->current_rule = ( $cookie != 'false'  ) ? $cookie : false ; // force //&& $cookie != 'true'
		}
	}

	function get_xili_theme_cookie() {

		if ( !empty($_COOKIE["xilitheme" . COOKIEHASH]) ) {
			if ( 'reset' == $_COOKIE["xilitheme" . COOKIEHASH] ) { // used to delete cookie and retrieve basic selection
				unset( $_COOKIE["xilitheme" . COOKIEHASH]);
				return '';
			} else {
				return $_COOKIE["xilitheme" . COOKIEHASH];
			}
		} else {
			return '';
		}
	}

	/**
	 * Create URI to redirect or refresh w/ or w/o param xilitheme
	 *
	 *
	 * @since 2.0
	 *
	 *
	 */
	function set_thelink( $type4refresh = true, $select_mode_rule = false ) {
		$param = ( $select_mode_rule ) ? $this->current_rule : 'false ';
		if ( $type4refresh ) {
			$new_uri = esc_url(add_query_arg( array(
	    		'xilitheme' => false
			), $_SERVER['REQUEST_URI'] ));
		} else {
			$new_uri = esc_url(add_query_arg( array(
	    		'xilitheme' => $param
			), $_SERVER['REQUEST_URI'] ));
		}
		return $new_uri;
	}

	/**
	 * Create link to redirect or refresh
	 *
	 * called directly or via shortcode 'xili-theme-link'
	 *
	 * @since 2.0
	 *
	 *
	 */
	function shortcode_theme_link ( $atts, $content = null ) {
		$atts = shortcode_atts( array(
	        'before'=>'<span class="xilithemelink">',
	        'after'=>'</span>',
	        'default_title'=>__('Link to see with default theme', 'xilithemeselect'),
	        'default_link_content' => __('Default theme', 'xilithemeselect'),
	        'title'=>__('Link to see with selected theme', 'xilithemeselect'),
	        'link_content' => __('Selected theme', 'xilithemeselect')
    	), $atts );

    	if ( $this->cookienable == 1 ) :

			if ( ( $this->get_xili_theme_cookie() != 'false' ) ) {
						$output = stripslashes($atts['before']).'<a href="'
						.$this->set_thelink( false, false ).'" title="'.stripslashes($atts['default_title']).'">'
						.stripslashes($atts['default_link_content']).'</a>'.stripslashes($atts['after']);
			} else {
					$output = stripslashes($atts['before']).'<a href="'
						.$this->set_thelink( false, $this->get_xili_theme_cookie() ).'" title="'.stripslashes($atts['title']).'">'
						.stripslashes($atts['link_content']).'</a>'.stripslashes($atts['after']);
			}
	    	return $output;

	    endif;
	}


// OLD CODE
	

	
	function change_template_path($template_dir) {
		if($this->iphone && $this->mode=='mobilebrowser'){
			//because use TEMPLATEPATH absolute so find page.php or the template page...
			// major add on inspired iwphone
			$template_dir = get_theme_root() . "/".$this->newfolder;
		} elseif ($this->othermobile) {
			$template_dir = get_theme_root() . "/".$this->newfolder;	
		}
		//echo "----".$template_dir;
		return $template_dir;	
	}
	

	/**
	 * admin part
	 */
	function add_admin_pages()
		{

		$this->thehook2 = add_options_page( __('Xili-theme select plugin','xilithemeselect'), __('Xili-theme select','xilithemeselect'), 'edit_plugins', 'xilitheme_select', array( &$this, 'options_page' ) );
		add_action('load-'.$this->thehook2, array(&$this,'onload_options_page'));

		// rules section
		add_settings_section( 'option_section_settings_1', __('Theme / Device + Browser Assignment', 'xilithemeselect'), array( $this, 'display_one_section'), 'xilitheme-select-settings'.'_group');

		$available_themes = wp_get_themes(); // current themes
		// $current_theme = get_stylesheet(); // not raw value as in options

		$themes_to_select = array();
		$themes_to_select['none'] = __('Uses current or select a theme...', 'xilithemeselect');
		foreach ( $available_themes as $key => $one_theme) {

			$child = ( $one_theme->get('Template')) ? ' (' . $one_theme->get('Template') . ') ' : ''; // detect name of parent if child
			$default = ( $key == $this->current_theme_folder ) ? ' *' : ''; // the raw non filtered value
			$themes_to_select[$key] = $one_theme->Name . $child . $default ;
		}

		$device_options = array (
			'iPhone' => __('iPhone or Ipod Touch','xilithemeselect'),
			'iPad' => __('iPad','xilithemeselect'),
			'isTierIphone' => __('Tier, nice touch-optimized mobile','xilithemeselect'),
			'DetectMobileQuick' => __('Other mobile device','xilithemeselect'),
			'OtherDevice' => __('Other device','xilithemeselect')
		);

		foreach ($device_options as $key => $value) {
			// select
			$field_args = array(
				'option_name'	=> 'xilitheme-select-settings',
				'title'			=> $value,
				'type'			=> 'select',
				'id'			=> 'device_assigned_'.$key,
				'name'			=> 'device_assigned_'.$key,
				'desc'			=> __('Assign a device/rule to one of the available themes.', 'xilithemeselect'),
				'std'			=> 'none',
				'label_for'		=> 'theme_assigned_'.$key,
				'class'			=> 'css_class settings',
				'option_values' => $themes_to_select
			);
			add_settings_field( $field_args['id'], $field_args['title'] ,
				array( $this, 'display_one_setting'), 'xilitheme-select-settings'.'_group', 'option_section_settings_1', $field_args );
		}
	}

	function onload_options_page() {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');

	}

	function set_register_setting () {

		register_setting( 'xilitheme-select-settings' .'_group', 'xilitheme-select-settings', array( $this,'xilitheme_select_validate_settings' ) );
	}

	function xilitheme_select_validate_settings ( $input) {
		//error_log ( __LINE__ . serialize($input));
		return $input;
	}

	function options_page() {
		$message = '';
		$data = array();
		?>
		<div id="xilitheme-select-settings" class="wrap columns-2 minwidth" >
			<?php screen_icon('options-general'); ?>
			<h2><?php _e('Xili-theme select settings','xilithemeselect') ?></h2>

			<?php if ( $message ) { ?>
			<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
			<?php } ?>
			<form method="post" enctype="multipart/form-data" action="options.php">
				<input type="hidden" name="action" value="<?php //echo $actiontype ?>" />
				<?php //wp_nonce_field('xilitheme-select-settings'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false );

				$poststuff_class = "";
				$postbody_class = 'class="metabox-holder columns-2"';
				$postleft_id = 'id="postbox-container-2"';
				$postright_id = "postbox-container-1";
				$postleft_class = 'class="postbox-container"';
				$postright_class = "postbox-container";

			?>
				<div id="poststuff" <?php echo $poststuff_class; ?>>
					<div id="post-body" <?php echo $postbody_class; ?> >

						<div id="<?php echo $postright_id; ?>" class="<?php echo $postright_class; ?>">
							<?php do_meta_boxes($this->thehook2, 'side', $data); ?>
						</div>

						<div id="post-body-content">
							<?php
								settings_fields( 'xilitheme-select-settings'.'_group' );	// nounce, action (plugin.php)
								do_settings_sections( 'xilitheme-select-settings'.'_group' );
							?>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
							</p>
							<div <?php echo $postleft_id; ?> <?php echo $postleft_class; ?> style="min-width:360px">
								<?php do_meta_boxes($this->thehook2, 'normal', $data); ?>
							</div>

							<h4><a href="http://dev.xiligroup.com/xilitheme-select/" title="Plugin page and docs" target="_blank" style="text-decoration:none" >
								<img style="vertical-align:middle" src="<?php echo plugins_url( 'images/xilitheme-logo-32.png', __FILE__ ) ;  ?>" alt="xilitheme-select logo"/>
								  xilitheme-select</a>
								 - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >dev.xiligroup.com</a>™ - msc 2007-2015 - v. <?php echo XILITHEME_VER; ?>
							</h4>

						</div>
					</div>
					<br class="clear" />
				</div>
			</form>
		</div>
		<?php
	}

	function display_one_section( $section ){
		switch ( $section['id'] ) {
			case 'option_section_settings_1':
				echo '<p class="section">'.
					sprintf(__('In this section, one available theme is assigned to a device/rule. So, when the server detects that this device is used, the current theme %s is replaced by the selected theme.', 'xilithemeselect') , '<strong>' . $this->current_theme_Name .' (*)</strong>' )
						. '<br />' . __('In the first column, the name of the device (or the rule). In the second, popup selector with list of available themes. The one marked with * is the current default theme.', 'xilithemeselect')
						.'</p>';
				break;
		}
	}

	function display_one_setting( $args ){
		extract( $args );
		$options = get_option('xilitheme-select-settings');
		switch ( $type ) {

			case 'message';
				echo ($desc != '') ? "<span class='description'>$desc</span>" : "...";
				break;

			case 'text':
				$set = ( isset ( $options[$id] ) ) ? $options[$id] : $std ;
				$set = stripslashes($set);
				$set = esc_attr( $set);
				$size_attr = (isset ($size)) ? "size='$size'" : '' ;
				echo "<input $size_attr class='regular-text$class' type='text' id='$id' name='" . $option_name . "[$id]' value='$set' />";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				break;

			case 'hidden':
				$set = ( isset ( $options[$id] ) ) ? $options[$id] :  false ;

				$val = ( $set ) ? '1' : '';
				echo "<input type='hidden' id='$id' name='" . $option_name . "[$id]' value='$val' />";

				echo ( $desc != '' ) ? "<span class='description'>$desc</span>" : "";
				break;


			case 'checkbox':
				// take default if not previous saved
				$set = ( isset ( $options[$id] ) ) ? $options[$id] : false;


				$checked = checked ( $set, $std, false );
				echo "<input $checked class='$class' type='checkbox' id='$id' name='" . $option_name . "[$id]' value='$std' />";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				break;

			case 'select':
				$set = ( isset ( $options[$id] ) ) ? $options[$id] : false;

				echo "<select id='$id' name='" . $option_name . "[$id]' />";

				foreach ( $option_values as $value => $content ) {
					echo "<option value='$value' " . selected ( $set , $value , false) . ">$content</option>";
				}
				echo "</select>";
				echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
				break;
		}
	}

	function add_help_text( $contextual_help, $screen_id, $screen ) {

		if ( $screen->id == 'settings_page_xilitheme_select' ) { // 2.8.8
			$wplink = 'https://wordpress.org/plugins/xilitheme-select/';
			$to_remember =

				'<p><strong>' . __('Things to remember to select theme according device/browser, mobile/desktop:','xilithemeselect') . '</strong></p>' .
				'<p><em>' . __('This version 2.0 (after a pause of 2 years) is completely renewed and uses latest libraries.','xilithemeselect') . '</em></p>' .
				'<ul>' .
					'<li>' . __('Before selection, be sure that good and adapted themes are available.','xilithemeselect') . '</li>' .
					'<li>' . __('Theme for mobile can be very specific w/o responsive feature (javascript).','xilithemeselect') . '</li>' .
					'<li>' . __('Not yet defined, latest line "other device" must not be modified.','xilithemeselect') . '</li>' .
					'<li>' . __('In further version, more flexible rules can be set.','xilithemeselect') . '</li>' .
				'</ul>'.
				'<p><em>' . sprintf(__('This version 2.0 uses latest library/class from Anthony Hand. %s','xilithemeselect'), '<a href="https://github.com/ahand/mobileesp" target="_blank">Github of mobilesp</a>' ) . '</em></p>' .
				'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation in WP plugin repository</a>','xili-language'), $wplink ) . '</p>' ;

			$screen->add_help_tab( array(
				'id'		=> 'xili-theme-select',
				'title'		=> sprintf( __('About %s theme select', 'xili-language'), '[©xili]'),
				'content'	=> $to_remember,
		));

		}
		return $contextual_help;
	}


}

global $wp_ismobile;
$wp_ismobile = new xilithemeselector ( true ); //true if dashboard part - false w/o dashboard - see doc on top
$wp_ismobile->cookienable = 1; /*0 to disable*/

/**** tag insertable in the theme's footer or elsewhere ****/
/* example 1 (in browser): if (function_exists('the_xilithemelink')) the_xilithemelink('link_content=<img src="'.get_bloginfo("stylesheet_directory").'/images/mobilelink.gif" alt=""/>');
*/
/* example 2 (in mobilebrowser): if (function_exists('the_xilithemelink')) the_xilithemelink('default_link_content=browse as default');
*/
function the_xilithemelink( $args = '' ){
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);
		
	global $wp_ismobile; 
	echo $wp_ismobile->shortcode_theme_link( $r );
}	
?>