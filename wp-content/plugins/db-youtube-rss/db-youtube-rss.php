<?php
/*
Plugin Name: DB YouTube RSS widget
Plugin URI: http://plugins.dickiebirds.com/category/db-youtube-rss/
Description: Widget display latest movies from YouTube channel (via RSS).
Author: Piotr Wesolowski
Version: 0.2
Author URI: http://www.dickiebirds.com/
*/

/*  Copyright 2010 Piotr Wesolowski (wesolowski@dickiebirds.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

class YouTubeRSS_Widget extends WP_Widget {
	function YouTubeRSS_Widget() {
		// widget actual processes
		parent::WP_Widget(false, $name = 'DB YouTube RSS', array(
			'description' => 'Display list of latest movies from YouTube RSS channel'
		));
	}
 
	function widget( $args, $instance ) {
		extract( $args) ;
		
		$db_yt_title = $instance['db_yt_title'];
		$db_yt_user = $instance['db_yt_user'] ;
		$db_yt_channel = $instance['db_yt_channel'];
		$db_yt_maxitems = $instance['db_yt_maxitems'];
		$db_yt_thumb_width = $instance['db_yt_thumb_width'];
		
		echo $before_widget;
		echo $before_title . $db_yt_title . $after_title;
		
		db_yt_rss_markup( $db_yt_user, $db_yt_channel, $db_yt_maxitems, $db_yt_thumb_width );
		
		echo $after_widget;
	}
 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['db_yt_title'] 		= strip_tags($new_instance['db_yt_title']);
		$instance['db_yt_user'] 		= strip_tags($new_instance['db_yt_user']);
		$instance['db_yt_channel'] 		= ( strip_tags($new_instance['db_yt_channel']) ) ? strip_tags($new_instance['db_yt_channel']) : -1;
		$instance['db_yt_maxitems'] 	= strip_tags($new_instance['db_yt_maxitems']);
		$instance['db_yt_thumb_width'] 	= strip_tags($new_instance['db_yt_thumb_width']);
		return $instance;
	}
 
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 
			'db_yt_title'			=> 'Latest movies',
			'db_yt_user' 			=> get_option( 'db_yt_user' ),
			'db_yt_channel' 		=> get_option( 'db_yt_channel' ),
			'db_yt_maxitems' 		=> get_option( 'db_yt_maxitems' ),
			'db_yt_thumb_width' 	=> get_option( 'db_yt_thumb_width' )
		));
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'db_yt_title' ); ?>">Title</label>
			<input id="<?php echo $this->get_field_id( 'db_yt_title' ); ?>" name="<?php echo $this->get_field_name( 'db_yt_title' ); ?>" type="text" class="widefat" value="<?php echo $instance['db_yt_title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'db_yt_user' ); ?>">YouTube username</label>
			<input id="<?php echo $this->get_field_id( 'db_yt_user' ); ?>" name="<?php echo $this->get_field_name( 'db_yt_user' ); ?>" type="text" class="widefat" value="<?php echo $instance['db_yt_user']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'db_yt_channel' ); ?>">Display channel link</label>
			<input id="<?php echo $this->get_field_id( 'db_yt_channel' ); ?>" name="<?php echo $this->get_field_name( 'db_yt_channel' ); ?>" type="checkbox" value="1" <?php if( $instance['db_yt_channel'] == '1' ) echo 'checked="checked"' ?> />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'db_yt_maxitems' ); ?>">Number of items</label>
			<input id="<?php echo $this->get_field_id( 'db_yt_maxitems' ); ?>" name="<?php echo $this->get_field_name( 'db_yt_maxitems' ); ?>" type="text" class="widefat" value="<?php echo $instance['db_yt_maxitems']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'db_yt_thumb_width' ); ?>">Thumbnail width</label>
			<input id="<?php echo $this->get_field_id( 'db_yt_thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'db_yt_thumb_width' ); ?>" type="text" class="widefat" value="<?php echo $instance['db_yt_thumb_width']; ?>" />
		</p>
		<?php
	}
}

function db_yt_rss_markup( $db_yt_user, $db_yt_channel, $db_yt_maxitems, $db_yt_thumb_width ){
	
	$db_yt_user = ( $db_yt_user ) ? $db_yt_user : get_option( 'db_yt_user' );
	$db_yt_channel = ( $db_yt_channel != '' ) ? $db_yt_channel : get_option( 'db_yt_channel' );
	$db_yt_maxitems = ( $db_yt_maxitems ) ? $db_yt_maxitems : get_option( 'db_yt_maxitems' );
	$db_yt_thumb_width = ( $db_yt_thumb_width ) ? $db_yt_thumb_width : get_option( 'db_yt_thumb_width' );
		
	if( $db_yt_user ) { // Display list only when username is given
		
		// Let's prepare links
		$db_yt_rss_url 		= "http://www.youtube.com/rss/user/" . $db_yt_user . "/videos.rss";
		$db_yt_channel_url 	= "http://www.youtube.com/user/" . $db_yt_user;
		
		// Get RSS Feed(s)
		include_once(ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss( $db_yt_rss_url );
		$maxitems = ( $db_yt_maxitems ) ? $db_yt_maxitems : 2;
		$items = array_slice( $rss->items, 0, $maxitems );
		
		$db_yt_thumb_width = ( $db_yt_thumb_width ) ? $db_yt_thumb_width : 220;
		
		if (!empty($items)) {
			?>
			<ul class="db-yt-rss">
				<?php 
					foreach ( $items as $item ) { 
						$youtubeid = youtubeid($item['link']);
						?>
						<li>
							<a href="<?php echo $item['link']; ?>" class="thumb">
								<img src="http://i.ytimg.com/vi/<?php echo $youtubeid; ?>/0.jpg" width="<?php echo $db_yt_thumb_width; ?>" alt="<?php echo $item['title']; ?>" />
							</a>
							<h4><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></h4>
						</li>
				<?php } ?>
			</ul>
			<?php
			if( $db_yt_channel == '1' ) { 
				?><a href="<?php echo $db_yt_channel_url ?>" class="more">See more videos</a><?php 
			}
		}
	} else {
		?>
		<p class="empty">No items</p>
		<?php
	}
}

function youtubeid($url) {
	$url_string = parse_url($url, PHP_URL_QUERY);
	parse_str($url_string, $args);
	return isset($args['v']) ? $args['v'] : false;
}

add_action( 'widgets_init', 'db_widget_init' );

function db_widget_init() {
	register_widget( 'YouTubeRSS_Widget' );
}

function  db_youtube_rss_load_style() {
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	echo '<link rel="stylesheet" type="text/css" href="'. $x . 'db-youtube-rss.css" />'; 
}
add_action('wp_head', 'db_youtube_rss_load_style');

// Hook for adding admin menu.
add_action( 'admin_menu', 'db_youtube_rss_create_menu' );

function db_youtube_rss_create_menu() {

	//create new top-level menu
	add_options_page( __( 'DB YouTube RSS', 'db_youtube_rss' ), __( 'DB YouTube RSS', 'db_youtube_rss' ), 'manage_options', 'db_ytrss_options', 'db_youtube_rss_admin_options_page');

	//call register settings function
	add_action( 'admin_init', 'register_db_youtube_rss_settings' );
}


function register_db_youtube_rss_settings() {
	register_setting( 'db_ytrss_options', 'db_yt_user' );
	register_setting( 'db_ytrss_options', 'db_yt_channel' );
	register_setting( 'db_ytrss_options', 'db_yt_maxitems' );
	register_setting( 'db_ytrss_options', 'db_yt_thumb_width' );
	
	add_option( 'db_yt_maxitems', 2 );
	add_option( 'db_yt_thumb_width', 220 );
}

/*
 * Shows the admininstration form with the plugin settings.
 */
function db_youtube_rss_admin_options_page() {
	?>

	<div class="wrap">
		<h2><?php _e( 'DB YouTube RSS', 'db_youtube_rss' ); ?> settings</h2>
		<form method="post" action="options.php">
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
			</p>
			<?php settings_fields( 'db_ytrss_options' ); ?>
		    <table class="widefat fixed" id="tblspacer" style="width: 660px">
		    	<thead>
		    		<tr>
		    			<th scope="col" style="width: 200px">Settings</th>
		    			<th></th>
		    		</tr>
		    	</thead>
		    	<tbody>
				    <tr>
				        <td><label for="db_yt_user">YouTube username</label></td>
						<td><input type="text" name="db_yt_user" id="db_yt_user" value="<?php echo get_option('db_yt_user'); ?>" class="regular-text" style="width: 400px" /></td>
				    </tr>
				    <tr>
				        <td><label for="db_yt_channel">Display channel link</label></td>
						<td><input type="checkbox" value="1" id="db_yt_channel" name="db_yt_channel" <?php if( get_option( 'db_yt_channel' ) == '1' ) echo 'checked="checked"' ?> /></td>
				    </tr>
				    <tr>
				        <td><label for="db_yt_maxitems">Number of items</label></td>
				        <td><input type="text" name="db_yt_maxitems" id="db_yt_maxitems" value="<?php echo get_option('db_yt_maxitems'); ?>" class="regular-text" style="width: 400px" /></td>
				    </tr>
				    <tr>
				        <td><label for="db_yt_thumb_width">Thumbnail width</label></td>
				        <td><input type="text" name="db_yt_thumb_width" id="db_yt_thumb_width" value="<?php echo get_option('db_yt_thumb_width'); ?>" class="regular-text" style="width: 400px" /></td>
				    </tr>
			    </tbody>
			</table>

			<p class="submit">
				<input type="hidden" name="action" value="update" />
				<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;') ?>" />
			</p>			
		</form>
	</div>

	<?php    
}
?>
