<?php
/*
Plugin Name: xili-theme-select
Plugin URI: http://dev.xiligroup.com/xilitheme-select/
Description: A plugin for WordPress that automatically redirects your blog's theme for optimized viewing on Apple's <a href="http://www.apple.com/iphone/">iPhone</a> and <a href="http://www.apple.com/ipodtouch/">iPod touch</a>.
Author: MS xiligroup dev 
Version: 1.0.4
Author URI: http://dev.xiligroup.com

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

define('XILITHEME_VER','1.0.4');

class xilithemeselector {
	
	var $themextend = ""; /*no extension = no touch theme*/
	var $iphone = false;
	var $currentthemefolder; /*the normal theme used by default*/
	var $newfolder;
	var $listofdevices = array("iPhone","iPod"); /* if present selector is activated - can be upgraded ! - iPad if you need !!*/
	var $xitype; /*type of the selected device*/
	var $xiversion; /*release of the selected device*/
	/**/
	var $testnoappledevices = true; /* if true call a function to check no apple devices*/
	var $othermobile = false;
	var $listnonappledevices = array(array("Opera","_4opera"),array("opera mini","_4touch"),array("Windows CE","_4touch"),array("Blackberry",""),array("Android","_4touch"));
	var $othermobiletype = ""; /* filled when $othermobile == true */
	/**/
	var $cookienable = 0; /*disable by default see after instantiation*/
	var $mode = 'mobilebrowser'; /*as usual*/
	
	
	public function __construct( $dashboard = true, $extend = "_4touch" ) {
		$this->xilithemeselector( $dashboard, $extend );
	}
	
	
	function xilithemeselector( $dashboard = true, $extend = "_4touch" ){
		if ($extend != "" && $dashboard == false) $this->themextend = $extend;
		
		add_action('plugins_loaded',array(&$this,'detectiPhone'));
		add_filter('stylesheet',array(&$this,'get_stylesheet'));
		add_filter('template',array(&$this,'get_template'));
		//add_filter('template_directory', array(&$this,'change_template_path')); // add for pages (template)
		
		
		/*admin part - if not : use extension only */
		if ($dashboard == true) :
			
			add_action('admin_menu', array(&$this, 'add_admin_pages'));
			add_action('init', array(&$this, 'init_textdomain'));
			add_filter('plugin_action_links', array(&$this, 'xilisel_filter_plugin_actions'), 10, 2); /*0.9.3*/
			
			$options =  get_option('xilithemeselect_options');
			if (!is_array($options))
			{
				$options['xilithemeextension'] = "_4touch";
				$options['isxilithemefullname'] = 'extension';
				$options['xilithemefullname'] = "default_4touch";
			}
			if (!isset($options['xilithemeextension']))
				$options['xilithemeextension'] = "_4touch";
			if ('' == $options['xilithemefullname'])
				$options['xilithemefullname'] = "default_4touch";	
			foreach ($options as $option_name => $option_value)
	        	$this-> {$option_name} = $option_value;
			$this->themextend = $this->xilithemeextension;
			$this->isfullname = false;
			if ($this->isxilithemefullname == 'full') 
										$this->isfullname = true;
				
		endif;	
		
	}
	
	function init_textdomain () {
		load_plugin_textdomain('xilithemeselect', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
	}
	
	/**
 * Add action link(s) to plugins page
 * 
 * @since 0.9.3
 * @author MS
 * @copyright Dion Hulse, http://dd32.id.au/wordpress-plugins/?configure-link and scripts@schloebe.de
 */
	function xilisel_filter_plugin_actions($links, $file){
		static $this_plugin;

		if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

		if( $file == $this_plugin ){
			$settings_link = '<a href="options-general.php?page=xilithemeselect">' . __('Settings') . '</a>';
			$links = array_merge( array($settings_link), $links); // before other links
		}
	return $links;
}


	
	function detectiPhone($query){
		/* prepare target theme folders according options and test them */
		
		$this->currentthemefolder = str_replace(get_theme_root()."/","",get_stylesheet_directory());
		
		if($this->isfullname) :
			/*test if theme with fullname exist*/
			$curpath = get_theme_root()."/".$this->xilithemefullname;
			if (file_exists($curpath)) :
				$this->newfolder = $this->xilithemefullname;
			else :
				//echo "CAUTION: THE ".$this->xilithemefullname." THEME FOLDER IS NOT PRESENT";
				$this->newfolder = $this->currentthemefolder; /*display the theme for current browsers*/
			endif;
		else :
			/*test if theme with extension exist*/
			$curpath = get_theme_root()."/".$this->currentthemefolder.$this->themextend;
			if (file_exists($curpath)) :
				$this->newfolder = $this->currentthemefolder.$this->themextend;
			else :
				//echo "CAUTION: THE ".$this->currentthemefolder." THEME FOLDER WITH EXTENSION ".$this->themextend." IS NOT PRESENT";
				$this->newfolder = $this->currentthemefolder; /*display the theme for current browsers*/
			endif;	
		endif;
		/* analyse type of browsers */
		$container = $_SERVER['HTTP_USER_AGENT'];
		//print_r($container); //this prints out the user agent array. uncomment to see it shown on page.
		$userdevices = $this->listofdevices;
		
		foreach ( $userdevices as $userdevice ) {
			if (eregi($userdevice,$container)){
				$this->iphone = true;
				$this->xitype = $userdevice;
				/* version for future uses - like 3D css 2.1 in theme*/
				$this->xiversion = preg_replace('#((.+)(Version/)(.+) M(.+))#i','$4' ,$container);
				//print_r(preg_grep("#i#",array('a'=>'opo','b'=>'uiop')));
				//echo " ---";
				break;
			}
		}
		
		if ($this->testnoappledevices == true && $this->iphone == false) {
		   $this->checknoappledevices();
		   
		}
	}
	
	function checknoappledevices() {
		
		$container = $_SERVER['HTTP_USER_AGENT'];
		$userdevices = $this->listnonappledevices;
		
		foreach ( $userdevices as $userdevice ) {
			
			if (ereg($userdevice[0],$container)){
				$this->othermobile = true;
				$this->othermobiletype = $userdevice[0];
				$this->otherthemextend = $userdevice[1];
				$curpath = get_theme_root()."/".$this->currentthemefolder.$this->otherthemextend;
				if (file_exists($curpath)) {
					$this->newfolder = $this->currentthemefolder.$this->otherthemextend;
				} else {
				echo "<br />CAUTION: THE ".$this->currentthemefolder." THEME FOLDER WITH EXTENSION ".$this->otherthemextend." IS NOT PRESENT";
					$this->newfolder = $this->currentthemefolder; /*display the theme for current browsers*/
				}
				break;
			}
		}
	}
		
	/* theme's selection according mobile */
	function get_stylesheet($stylesheet) {
		if($this->iphone && $this->mode=='mobilebrowser'){
			return $this->newfolder;
		} elseif ($this->othermobile){	
			return $this->newfolder;
		} else {
			return $stylesheet;
		}
	}
	
	function get_template($template) {
		if($this->cookienable == 1) $this->set_xilitheme_cookie();
		
		if($this->iphone && $this->mode=='mobilebrowser'){
			return $this->newfolder;
		} elseif ($this->othermobile){	
			return $this->newfolder;
		} else {
			return $template;
		}
	}
	
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
	
	/* cookie part for theme selection if iphone of ipod */
	function set_xilitheme_cookie() {
	$expire = time() + 30000000;
		if (!empty($_GET["xilitheme"])):
			setcookie("xilitheme" . COOKIEHASH,
							stripslashes($_GET["xilitheme"]),
							$expire,
							COOKIEPATH
							);
			$this->mode = $_GET["xilitheme"];
			
			$refreshinglink = $this->build_thelink();
			
			if (function_exists('wp_redirect'))
					wp_redirect($refreshinglink);
				else
					header("Location: ". $refreshinglink);
			
			exit;/**/
		endif;
		if ($this->get_xilitheme_cookie()==''):
			$this->mode = "mobilebrowser";
		else:
			$this->mode = $this->get_xilitheme_cookie();
		endif;
	}
	
	function get_xilitheme_cookie() {
		if (!empty($_COOKIE["xilitheme" . COOKIEHASH])) :
			return $_COOKIE["xilitheme" . COOKIEHASH];
		else:
			return '';
		endif;
	}
	
	function build_thelink($type4refresh=true,$modelink="browser") {
		$querystring = "";
			/*restore the _GET*/
			$permalink = get_option('permalink_structure');
			if ( '' != $permalink) :
			   $q = "?";
			   $querystring = str_replace(preg_replace('|/[^/]+?$|', '/', $_SERVER['PHP_SELF']),'',$_SERVER['REQUEST_URI']);
			   
			   if ($type4refresh): 
			   			//ignore this ?xilitheme $_GET value
			   			$querystring = preg_replace('#\?xilitheme(.+)browser#isU', '',$querystring );
			   endif;			
			else:
				$q = "&";
				$i=0;
				foreach ($_GET as $key => $value) {
            		if ($key != "xilitheme") {  // ignore this particular $_GET value
                		if ($i == 0) $querystring = "?";
                		if ($i > 0)  $querystring .= "&";
                		$querystring .= $key."=".$value;
                		$i++;
            		}
				}
			endif;
			if ($type4refresh):
				 return get_option('siteurl').'/'.$querystring;
			else:
				if ($querystring == "")
					return get_option('siteurl').'/?xilitheme='.$modelink;
				else
					return get_option('siteurl').'/'.$querystring.$q.'xilitheme='.$modelink;	
			endif;		
	}
	function display_themetype($displayhtml = array('before'=>'<span class="xilithemelink">','after'=>'</span>','bro'=>'browser','mobbro'=>'iTouch')){
	/* return the link display in current theme or in mobile theme */
		if ($this->iphone == true && $this->cookienable == 1) :
			
			if (($this->get_xilitheme_cookie() == 'browser')) {	
						$output = stripslashes($displayhtml['before']).'<a href="'
						.$this->build_thelink(false,"mobilebrowser").'">'
						.stripslashes($displayhtml['mobbro']).'</a>'.stripslashes($displayhtml['after']);
			} else {	
					$output = stripslashes($displayhtml['before']).'<a href="'
						.$this->build_thelink(false,"browser").'">'
						.stripslashes($displayhtml['bro']).'</a>'.stripslashes($displayhtml['after']);
			}
	    	return $output;
	    endif;
	    
	}
	

	
	
	
	/* admin part */

	function add_admin_pages()
		{
		add_options_page('Xilitheme select', 'Xilitheme select', 'edit_plugins', 'xilithemeselect', array(&$this, 'option_page'));
		}
	function option_page()
		{
			if ( isset($_POST['submitted']) ) {
		$options = array();
		$options['xilithemeextension'] = $_POST['xilithemeextension'];
		$options['isxilithemefullname'] = $_POST['isxilithemefullname'];
		$options['xilithemefullname'] = $_POST['xilithemefullname'];
		
		update_option('xilithemeselect_options', $options);
		foreach ($options as $option_name => $option_value)
	        $this-> {$option_name} = $option_value;	  
		echo '<div id="message" class="updated fade"><p>'.__("Plugin<strong> xilithemeselect</strong> settings saved.","xilithemeselect").'</p></div>';
		}
		
	?>
		<div class='wrap'>
		<h2><?php _e("Xilitheme select settings","xilithemeselect"); ?></h2>
		<p><cite><a href='http://www.xiliphone.mobi' target='_blank'>Xilitheme select</a></cite> <?php _e("provides an automatic selection of the theme for iphone's user when visiting your site.<br />Don't forget to upload a specific theme for ipod touch / iphone with a right folder's name in the wp-content/themes folder.<br /> If an error occur, the current theme is displayed in ipod touch / iphone browser.","xilithemeselect"); ?></p>
		<p><?php
		 
		if (function_exists('is_child_theme') && is_child_theme() ) {
				$theme_name = get_option("stylesheet").' '.__('child of','xili-language').' '.get_option("template");
		} else {
				$theme_name = get_option("template");
		}
		_e("the current theme is","xilithemeselect"); echo ": <em>".$theme_name ?>. </em><strong><br />
		<?php _e("itouch theme's folder","xilithemeselect"); echo ": <em>".$this->currentthemefolder.$this->xilithemeextension."</em> "; 
		$curpath = get_theme_root()."/".$this->currentthemefolder.$this->xilithemeextension;
			if (file_exists($curpath)) :
				_e("is available","xilithemeselect");
			else :
				_e("is NOT available","xilithemeselect");
			endif;			
		?>, </strong>
		<br /><strong>
		<?php _e("itouch theme's folder","xilithemeselect"); echo ": <em>".$this->xilithemefullname."</em> "; 
		$curpath = get_theme_root()."/".$this->xilithemefullname;
			if (file_exists($curpath)) :
				_e("is available","xilithemeselect");
			else :
				_e("is NOT available","xilithemeselect");
			endif;			
		?>  </strong>
		<br /><?php _e("in the current wp-content/themes folder.","xilithemeselect");?></p>
		<form name="xiliphone" action="<?php echo $action_url; ?>" method="post">
			<input type="hidden" name="submitted" value="1" />
				
			<fieldset class="options">
				<ul>
					<li>
					<label for="xilithemeextension">
						<?php _e("itouch theme's folder extension:","xilithemeselect"); ?>
						<input type="text" id="xilithemeextension" name="xilithemeextension"
							size="7" maxlength="8"
							value="<?php echo $this->xilithemeextension; ?>" />
					</label>
					</li>
					<li>
					<label for="isxilithemefullname">
						<?php _e("Option: extension or full name:","xilithemeselect"); ?>
						<select name="isxilithemefullname">
                    <option value="full"<?php if ($this->isxilithemefullname == 'full') { ?> selected="selected"<?php } ?> ><?php _e('full name','xilithemeselect'); ?></option>
                    <option value="extension"<?php if ($this->isxilithemefullname == 'extension') { ?> selected="selected"<?php } ?> ><?php _e('extension','xilithemeselect'); ?></option>
                    </select>
					</label>
					</li>
					<li>
					<label for="xilithemefullname">
						<?php _e("itouch theme's folder full name:","xilithemeselect"); ?>
						<input type="text" id="xilithemefullname" name="xilithemefullname"
							size="20" maxlength="30"
							value="<?php echo $this->xilithemefullname; ?>" />
					</label>
					</li>
					
				</ul>
			</fieldset>
			<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes'); ?> &raquo;" /></p>
		</form>
		<h4><a href="http://dev.xiligroup.com/xilitheme-select/" title="Plugin page and docs" target="_blank" style="text-decoration:none" ><img style="vertical-align:middle" src="<?php echo plugins_url( 'xilitheme-logo-32.png', __FILE__ ) ;  ?>" alt="xilitheme-select logo"/>  xilitheme-select</a> - © <a href="http://dev.xiligroup.com" target="_blank" title="<?php _e('Author'); ?>" >dev.xiligroup.com</a>™ - msc 2007-2013 - v. <?php echo XILITHEME_VER; ?></h4>
	</div>
	<?php
	}

}

global $wp_ismobile;
$wp_ismobile = new xilithemeselector ( true ); //true if dashboard part - false w/o dashboard - see doc on top
$wp_ismobile->cookienable = 1; /*0 to disable*/

/**** tag insertable in the theme's footer or elsewhere ****/
/* example 1 (in browser): if (function_exists('the_xilithemelink')) the_xilithemelink('mobbro=<img src="'.get_bloginfo("stylesheet_directory").'/images/mobilelink.gif" alt=""/>');
*/
/* example 2 (in mobilebrowser): if (function_exists('the_xilithemelink')) the_xilithemelink('bro=naviguer en mode normal');
*/
function the_xilithemelink($args = ''){
	
	if ( is_array($args) )
		$r = &$args;
	else
		parse_str($args, $r);
		
	$defaults = array('before'=>'<span class="xilithemelink">','after'=>'</span>','bro'=>'browser','mobbro'=>'iTouch');
	$r = array_merge($defaults, $r);
	
	global $wp_ismobile; 
	echo $wp_ismobile->display_themetype($r);
}	
?>