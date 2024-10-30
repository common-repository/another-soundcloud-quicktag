<?php
/*
Plugin Name: Another SoundCloud Quicktag
Plugin URI: http://wordpress.nemack.com
Description: Another SoundCloud Quicktag. Establish a new button in the tinymce editor, so you can easy link to SoundCloud files. The plugin works with multiple links in one post... Based on SoundCloud Shortcode (ver 1.1.9) by Johannes Wagener.
Version: 1.2
Author: Enrico Nemack <mail@nemack.com>
Author URI: http://nemack.com
*/

/*
Another SoundCloud Quicktag (Wordpress Plugin)
Copyright (C) 2011 Enrico Nemack

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


add_filter( "pre_kses", "soundcloud_reverse_shortcode" );
function soundcloud_reverse_shortcode_preg_replace_callback( $a ){
       $pattern = '/([a-zA-Z0-9\-_%=&]*)&?url=([^&]+)&?([a-zA-Z0-9\-_%&=]*)/';
       preg_match( $pattern, str_replace( "&amp;", "&", $a[3] ), $params );
       return( '[soundcloud width="' . esc_attr( $a[1] ) . '" height="' .
esc_attr( $a[2] ) . '" params="' . esc_attr( $params[1] . $params[3] )
. '" url="' . urldecode( $params[2] ) . '"]' );
}

function soundcloud_reverse_shortcode( $content ){
       $pattern = '/<object.*width="([\d]+%?)".*height="([\d]+%?)".*src="http:\/\/.*soundcloud\.com\/player.swf\?(.*)".*<\/object>( <span[^>]*>.*<\/span>|)/U';
       $pattern_ent = htmlspecialchars( $pattern, ENT_NOQUOTES );
       if ( preg_match( $pattern_ent, $content ) )
               return( preg_replace_callback( $pattern_ent,
'soundcloud_reverse_shortcode_preg_replace_callback', $content ) );
       else
               return( preg_replace_callback( $pattern,
'soundcloud_reverse_shortcode_preg_replace_callback', $content ) );
}

add_shortcode( "soundcloud", "soundcloud_shortcode" );
function soundcloud_shortcode( $atts,$url='' ) {
       if ( empty( $url ) )
               extract(shortcode_atts( array( 'url' => '', 'params' => '', 'height'
=> '', 'width' => '100%' ), $atts ) );
       else
               extract(shortcode_atts( array( 'params' => '', 'height' => '',
'width' => '100%' ), $atts ) );
       $encoded_url = urlencode( $url );
       if ( $url = parse_url( $url ) ){
               $splitted_url = split( "/", $url['path'] );
               $media_type = $splitted_url[count($splitted_url) - 2];

               if ( $height == "" ){
                       if ( $media_type == "groups" || $media_type == "sets" ){
                               $height = "225";
                       } else {
                               $height = "81";
                       }
               }
               $player_params = "url=$encoded_url&g=1&$params";
               
               preg_match('/(.+\.)?(((staging|sandbox)-)?soundcloud\.com)/', $url['host'], $matches);
               $player_host = "player." . $matches[2];
               
               return "<object height=\"" . esc_attr( $height ) . "\" width=\"" .
esc_attr( $width ) . "\"><param name=\"movie\" value=\"http://" .
esc_attr( $player_host ) . "/player.swf?" . esc_attr( $player_params )
. "\"></param><param name=\"allowscriptaccess\"
value=\"always\"></param><embed allowscriptaccess=\"always\"
height=\"" . esc_attr( $height ) . "\" src=\"http://" . esc_attr(
$player_host ) . "/player.swf?" . esc_attr( $player_params ) . "\"
type=\"application/x-shockwave-flash\" width=\"" . esc_attr( $width )
. "\"> </embed> </object>";
       }
}

add_action( 'init', 'AnotherSoundCloudQuicktag' ); 
function AnotherSoundCloudQuicktag() { 
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "AnotherSoundcloudQuicktag_mce_external_plugins");
    	add_filter('mce_buttons', 'register_AnotherSoundcloudQuicktag_button', 0);
		wp_enqueue_style('soundcoud-dialog', plugins_url('/another-soundcloud-quicktag/jquery-ui.css'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-dialog');
   }
}

function register_AnotherSoundcloudQuicktag_button($buttons) {
   array_push($buttons, "separator", "AnotherSoundcloudQuicktag");
   return $buttons;
}

function AnotherSoundcloudQuicktag_mce_external_plugins( $plugins ) {
	$plugins['AnotherSoundcloudQuicktag'] = plugins_url('/another-soundcloud-quicktag/tinymce3/editor_plugin.js');
	return $plugins;
}

add_action("admin_footer", "AnotherSoundcloudQuicktag_dialog");
function AnotherSoundcloudQuicktag_dialog() {
	echo '<div class="hidden">';
	echo '<div id="soundcloudDialog">';
	echo '<p>Please enter the URL at which the song can be heared.</p>';
	echo '<p><b>Example: </b><span class="example">http://soundcloud.com/chiddy-bang/kids-feat-mgmt-1</span></p><p></p>';
	echo '<p>SoundCloud link: <input id="soundcloudDialog-link" type="text" maxlength="255"></p>';
	echo '<p>start with autoplay: <select id="soundcloudDialog-autoplay" name="autoplay"><option value="true">yes</option><option value="false" selected>no</option></select> &nbsp; &nbsp;';
	echo 'show comments: <select id="soundcloudDialog-comments" name="comments"><option value="true">yes</option><option value="false">no</option></select></p>';
	echo '</div></div></div>';
}
