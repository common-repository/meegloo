<?php
/**
 * @package Meegloo
 * @version 0.3
 */
/*
Plugin Name: Meegloo
Plugin URI: http://wordpress.org/extend/plugins/meegloo/
Description: Easily embed <a href="http://meegloo.com/">Meegloo</a> streams into your blog
Author: Flaming Tarball
Version: 0.3
Author URI: http://flamingtarball.com/
*/

function meegloo_the_content($content) {
	$key = MD5(get_option('meegloo_key'));
	$wpurl = get_bloginfo('wpurl');
	$ex = "/^https?\:\/\/([a-zA-z0-9\.]+)(\:\d+)?/i";
	
	$wp_domain = preg_match($ex, $wpurl, $match);
	if(count($match) == 2) {
		$wp_domain = $match[1];
	} else {
		return $content;
	}
	
	$ex = "/\<p\>http:\/\/(.+\.)?meegloo\.com\/([\w-]+)\/?\<\/p\>/i";
	while(preg_match($ex, $content, $match)) {
		if($match) {
			$username = $match[1];
			$stream = $match[2];
			$domain = "${username}meegloo.com";
			
			if(is_feed()) {
				$content = str_replace($match[0], '<iframe src="http://' . $domain . '/' . $stream . '/embed.html?key=' . $key . '&amp;domain=' . $wp_domain . '" width="320" height="480"><a href="http://' . $domain . '/' . $stream . '">Visit the Meegloo stream</a></iframe>', $content);
			} else {
				$content = str_replace($match[0], '<script type="text/javascript" src="http://' . $domain . '/' . $stream . '/embed.js?key=' . $key . '&domain=' . $wp_domain . '"></script>', $content);
			}
		} else {
			break;
		}
	}
	
	return $content;
}

function meegloo_admin_menu() {
	add_submenu_page(
		'options-general.php',
		'Meegloo',
		'Meegloo',
		'administrator',
		__FILE__,
		'meegloo_settings_page'
	);
}

function meegloo_register_settings() {
	register_setting('meegloo', 'meegloo_key');
}

if (is_admin()) {
	add_action('admin_menu', 'meegloo_admin_menu');
	add_action('admin_init', 'meegloo_register_settings');
} else {
	add_filter('the_content', 'meegloo_the_content');
}

function meegloo_settings_page() { ?>
	<div class="wrap">
		<h2>Meegloo Settings</h2>
		
		<form method="post" action="options.php">
			<?php settings_fields('meegloo'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Embed Key</th>
					<td>
						<p>To get your embed key, go to <a href="http://meegloo.com/profile/edit/">your Meegloo profile</a> and copy the key from the right-hand section of the page.</p>
						<input type="text" name="meegloo_key" value="<?php echo get_option('meegloo_key'); ?>" />
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php } ?>