<?php
/*
Plugin Name: k0nsl No-Spam IP Geolocation
Plugin URI: http://k0nsl.org/blog/
Description: Uses CloudFlare IP Geolocation and poses the question; what is your country code?
Version: 0.1
Author: k0nsl
Author URI: http://k0nsl.org/blog
Stable tag: trunk
License: GPLv3
Text Domain: k0nsl_nospam_ipgeo

------------------------------------------------------------
 ACKNOWLEDGEMENTS
------------------------------------------------------------
Originally based on WP No-Bot Question by edwardw (http://wordpress.org/extend/plugins/wp-no-bot-question/).

Thanks to:
[myg0t]h4x^^

*/
define('k0nsl_nospam_ipgeo_version','0.1');

register_activation_hook( __FILE__, 'k0nsl_nospam_ipgeo_activate' );
register_deactivation_hook( __FILE__, 'k0nsl_nospam_ipgeo_deactivate' );
register_uninstall_hook( __FILE__, 'k0nsl_nospam_ipgeo_remove' );

add_action('init', 'k0nsl_nospam_ipgeo_init');
add_action('admin_menu', 'k0nsl_nospam_ipgeo_admin_init');

add_action('comment_form_after_fields', 'k0nsl_nospam_ipgeo_comment_field');
add_action('comment_form_logged_in_after', 'k0nsl_nospam_ipgeo_comment_field');
add_filter('preprocess_comment', 'k0nsl_nospam_ipgeo_filter');

add_action('user_registration_email', 'k0nsl_nospam_ipgeo_filter');
add_action('register_form', 'k0nsl_nospam_ipgeo_registration_field');

function k0nsl_nospam_ipgeo_activate() {
	if(get_option('k0nsl_nospam_ipgeo_enable') === false) add_option('k0nsl_nospam_ipgeo_enable',true);
	if(get_option('k0nsl_nospam_ipgeo_registration') === false) add_option('k0nsl_nospam_ipgeo_registration',false);
}

function k0nsl_nospam_ipgeo_deactivate() {
	/* Stub */
}

function k0nsl_nospam_ipgeo_remove() {
	delete_option('k0nsl_nospam_ipgeo_enable');
	delete_option('k0nsl_nospam_ipgeo_registration');
}

function k0nsl_nospam_ipgeo_init() {
	wp_enqueue_script('jquery');
}

function k0nsl_nospam_ipgeo_admin_init() {
	add_submenu_page( 'options-general.php', 'No-Spam IP Geolocation &rarr; Edit Question', 'No-Spam IP Geolocation', 'moderate_comments', 'k0nsl_nospam_ipgeo_page', 'k0nsl_nospam_ipgeo_admin' );
}

function k0nsl_nospam_ipgeo_comment_field() {
	k0nsl_nospam_ipgeo_field('comment');
}

function k0nsl_nospam_ipgeo_registration_field() {
	k0nsl_nospam_ipgeo_field('registration');
}

function k0nsl_nospam_ipgeo_field($context = 'comment') {
	if( current_user_can('editor') || current_user_can('administrator') ||
	    !k0nsl_nospam_ipgeo_get_option('enable') ||
	    ( $context == 'registration' && !k0nsl_nospam_ipgeo_get_option('registration') )
	     ) return;
?>
<p class="comment-form-k0nsl_nospam_ipgeo">
	<label for="k0nsl_nospam_ipgeo_answer"><?php _e('What is your country code?','k0nsl_nospam_ipgeo'); ?> (<?php _e('Shhhh, it is:','k0nsl_nospam_ipgeo'); ?> <?php echo $country_code = $_SERVER["HTTP_CF_IPCOUNTRY"]; ?>)</label>
	<input
		id="k0nsl_nospam_ipgeo_answer"
		name="k0nsl_nospam_ipgeo_answer"
		type="text"
		value=""
		size="30"
		<?php if($context == 'registration') { ?> tabindex="25" <?php }; ?>
	/></p>
<?php
}

function k0nsl_nospam_ipgeo_filter($x) {
	if( current_user_can('editor') || current_user_can('administrator') ||
	    ( /* Is registration? */!is_array($x) && !k0nsl_nospam_ipgeo_get_option('registration') )||
	    $x['comment_type'] == 'pingback' || $x['comment_type'] == 'trackback' ||
	    !k0nsl_nospam_ipgeo_get_option('enable') ) {
		return $x;
	}
	if(!array_key_exists('k0nsl_nospam_ipgeo_answer',$_POST) || trim($_POST['k0nsl_nospam_ipgeo_answer']) == '') {
		wp_redirect( 'http://anonymizer.k0nsl.org/topcat.php', 302 ); exit;
	}
	$answer = $_SERVER["HTTP_CF_IPCOUNTRY"];
		if(trim($_POST['k0nsl_nospam_ipgeo_answer']) == $answer) return $x;
	wp_redirect( 'http://anonymizer.k0nsl.org/topcat.php', 302 ); exit;
}

function k0nsl_nospam_ipgeo_get_option($o) {
	switch($o) {
		case 'enable':
			return (bool)get_option('k0nsl_nospam_ipgeo_enable');
		break;
		case 'question':
			return strval(get_option('k0nsl_nospam_ipgeo_question'));
		break;
		case 'answers':
			$tmp = get_option('k0nsl_nospam_ipgeo_answers');
			if( $tmp === false ) return Array();
			else return $tmp;
		break;
		case 'registration':
			return (bool)get_option('k0nsl_nospam_ipgeo_registration');
		break;
		default:
			return null;
	}
}

function k0nsl_nospam_ipgeo_admin() {
	if(!current_user_can('moderate_comments')) return;
	if(isset($_POST['submit'])) {
		update_option('k0nsl_nospam_ipgeo_enable',(bool)$_POST['k0nsl_nospam_ipgeo_enabled']);
		update_option('k0nsl_nospam_ipgeo_question',(string)$_POST['k0nsl_nospam_ipgeo_question']);
		update_option('k0nsl_nospam_ipgeo_answers',$_POST['k0nsl_nospam_ipgeo_answers']);
		if(array_key_exists( 'k0nsl_nospam_ipgeo_registration', $_POST ))
			update_option('k0nsl_nospam_ipgeo_registration', true);
		else
			update_option('k0nsl_nospam_ipgeo_registration', false);
		add_settings_error('k0nsl_nospam_ipgeo', 'k0nsl_nospam_ipgeo_updated', __('No-Spam IP Geolocation settings updated.','k0nsl_nospam_ipgeo'), 'updated');
	}
	$k0nsl_nospam_ipgeo_enabled = k0nsl_nospam_ipgeo_get_option('enable');
	$k0nsl_nospam_ipgeo_question = k0nsl_nospam_ipgeo_get_option('question');
	$k0nsl_nospam_ipgeo_answers = k0nsl_nospam_ipgeo_get_option('answers');
	$k0nsl_nospam_ipgeo_registration = k0nsl_nospam_ipgeo_get_option('registration');
	?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2>No-Spam IP Geolocation Settings</h2>
	<?php settings_errors(); ?>
	<form method="post" name="k0nsl_nospam_ipgeo_form">
<?php settings_fields('discussion'); ?>
<table class="form-table">
	<tr valign="top">
	<th scope="row"><?php _e('Enable No-Spam IP Geolocation','k0nsl_nospam_ipgeo'); ?></th>
	<td>
		<fieldset>
			<input type="radio" name="k0nsl_nospam_ipgeo_enabled" value="1" <?php if($k0nsl_nospam_ipgeo_enabled) echo 'checked="checked"' ?> /> <?php _e('Yes'); ?>
			<input type="radio" name="k0nsl_nospam_ipgeo_enabled" value="0" <?php if(!$k0nsl_nospam_ipgeo_enabled) echo 'checked="checked"' ?> /> <?php _e('No'); ?>
		</fieldset>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><?php _e('Protect registration?','k0nsl_nospam_ipgeo'); ?></th>
	<td>
		<fieldset>
			<input type="checkbox" name="k0nsl_nospam_ipgeo_registration" value="1" <?php if($k0nsl_nospam_ipgeo_registration) echo 'checked="checked"' ?> />
		</fieldset>
	</td>
	</tr>

	</td>
	</tr>
</table>

<?php submit_button(); ?>
</form>
<p>No-Spam IP Geolocation by <a href="http://k0nsl.org/blog">k0nsl</a>.</p>
</div>
<?php
}

?>
