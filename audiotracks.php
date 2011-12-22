<?php



/*
Plugin Name: AudioTracks
Plugin URI: http://www.benhuson.co.uk/wordpress-plugins/audiotracks
Description: Manage a directory of audio tracks in WordPress. 
Version: 0.2.beta
Author: Ben Huson
Author URI: http://www.benhuson.co.uk
License: GPL2
*/



/*
Copyright 2010 Ben Huson (http://www.benhuson.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



add_action( 'init', 'audiotrack_init' );

function audiotrack_init() {
	
	$labels = array(
		'name'               => _x( 'Audio Tracks', 'audiotrack general name' ),
		'singular_name'      => _x( 'Audio Track', 'audiotrack singular name' ),
		'add_new'            => _x( 'Add Track', 'audiotrack' ),
		'add_new_item'       => __( 'Add New Audio Track' ),
		'edit_item'          => __( 'Edit Audio Track' ),
		'new_item'           => __( 'New Audio Track' ),
		'view_item'          => __( 'View Audio Track' ),
		'search_items'       => __( 'Search Audio Tracks' ),
		'not_found'          => __( 'No audio tracks found' ),
		'not_found_in_trash' => __( 'No audio tracks found in Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'         => __( 'Audio Tracks' )
	);
	
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'rewrite'            => true,
		//'taxonomies'         => array( 'music_genre' ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'thumbnail' )
	);
	
	register_post_type( 'audiotrack', $args );
	
	// #TCON Genre
	
	$labels = array(
		'name'              => _x( 'Genres', 'taxonomy general name' ),
		'singular_name'     => _x( 'Genre', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Genres' ),
		'all_items'         => __( 'All Genres' ),
		'parent_item'       => __( 'Parent Genre' ),
		'parent_item_colon' => __( 'Parent Genre:' ),
		'edit_item'         => __( 'Edit Genre' ), 
		'update_item'       => __( 'Update Genre' ),
		'add_new_item'      => __( 'Add New Genre' ),
		'new_item_name'     => __( 'New Genre Name' ),
		'menu_name'         => _x( 'Genres', 'taxonomy general name' )
	); 	
	
	register_taxonomy( 'ID3_TCON', array( 'audiotrack' ), array(
		'hierarchical' => false,
		'labels'       => $labels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'audio-genre' ),
	) );
	
	// #TALB Album/Movie/Show title
	
	$labels = array(
		'name'              => _x( 'Albums', 'taxonomy general name' ),
		'singular_name'     => _x( 'Album', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Albums' ),
		'all_items'         => __( 'All Albums' ),
		'parent_item'       => __( 'Parent Album' ),
		'parent_item_colon' => __( 'Parent Album:' ),
		'edit_item'         => __( 'Edit Album' ), 
		'update_item'       => __( 'Update Album' ),
		'add_new_item'      => __( 'Add New Album' ),
		'new_item_name'     => __( 'New Album' ),
		'menu_name'         => _x( 'Albums', 'taxonomy general name' )
	); 	
	
	register_taxonomy( 'ID3_TALB', array( 'audiotrack' ), array(
		'hierarchical' => false,
		'labels'       => $labels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'audio-album' ),
	) );
	
}

add_filter( 'post_updated_messages', 'audiotrack_updated_messages' );

function audiotrack_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['audiotrack'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Track updated. <a href="%s">View track</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Track updated.'),
		/* translators: %s: date and time of the Track */
		5 => isset($_GET['revision']) ? sprintf( __('Track restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Track published. <a href="%s">View track</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Track saved.'),
		8 => sprintf( __('Track submitted. <a target="_blank" href="%s">Preview track</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Track scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview track</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Track draft updated. <a target="_blank" href="%s">Preview track</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	
	return $messages;
	
}








add_action( 'admin_menu', 'audiotrack_add_meta_boxes' );

function audiotrack_add_meta_boxes() {
	
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'audiotrack_track_meta', 'Track Details', 'audiotrack_track_meta_box_inner', 'audiotrack', 'normal' );
		add_meta_box( 'audiotrack_file_meta', 'Audio File', 'audiotrack_file_meta_box_inner', 'audiotrack', 'normal' );
	}
	
}

//http://www.id3.org/id3v2.3.0#head-e4b3c63f836c3eb26a39be082065c21fba4e0acc

function audiotrack_track_meta_box_inner() {
	
	global $post;
	
	$ID3_TPE1   = get_post_meta( $post->ID, 'ID3_TPE1', true );
	$ID3_TCOM   = get_post_meta( $post->ID, 'ID3_TCOM', true );
	//$audio_year   = get_post_meta( $post->ID, 'audio_year', true );
	//$audio_grouping   = get_post_meta( $post->ID, 'audio_grouping', true );
	//$audio_duration   = get_post_meta( $post->ID, 'audio_duration', true );
	//$audio_bpm   = get_post_meta( $post->ID, 'audio_bpm', true );
	//$audio_compilation   = get_post_meta( $post->ID, 'audio_compilation', true );
	
	// Use nonce for verification
	echo '<input type="hidden" name="audiotrack_track_noncename" id="audiotrack_track_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	
	// The actual fields for data entry
	echo '<label for="ID3_TPE1">Artist</label> ';
	echo '<input type="text" name="ID3_TPE1" value="' . $ID3_TPE1 . '" size="25" /><br />';
	
	echo '<label for="ID3_TCOM">Composer</label> ';
	echo '<input type="text" name="ID3_TCOM" value="' . $ID3_TCOM . '" size="25" /><br />';
	
	/*
	echo '<label for="audio_year">Year</label> ';
	echo '<input type="text" name="audio_year" value="' . $audio_year . '" size="25" /><br />';
	
	echo '<label for="audio_grouping">Grouping</label> ';
	echo '<input type="text" name="audio_grouping" value="' . $audio_grouping . '" size="25" /> <small>Generally used to denote movements within a classical work.</small><br />';
	
	echo '<label for="audio_duration">Duration</label> ';
	echo '<input type="text" name="audio_duration" value="' . $audio_duration . '" size="25" /><br />';
	
	echo '<label for="audio_bpm">BPM</label> ';
	echo '<input type="text" name="audio_bpm" value="' . $audio_bpm . '" size="25" /><br />';
	
	echo '<label for="audio_compilation">Part of a compilation</label> ';
	echo '<input type="checkbox" name="audio_compilation" value="1" /><br />';
	
	echo '<label for="audio_bpm">Track number</label> ';
	echo '<input type="text" name="audio_bpm" value="' . $audio_bpm . '" size="3" /> ';
	echo 'of <input type="text" name="audio_bpm" value="' . $audio_bpm . '" size="3" /><br />';
	
	echo '<label for="audio_bpm">Disk number</label> ';
	echo '<input type="text" name="audio_bpm" value="' . $audio_bpm . '" size="3" /> ';
	echo 'of <input type="text" name="audio_bpm" value="' . $audio_bpm . '" size="3" /><br />';
	
	echo 'Lyrics';
	*/

}

function audiotrack_file_meta_box_inner() {
	
	global $post;
	
	$audiotrack_mp3_file    = get_post_meta( $post->ID, 'audiotrack_mp3_file', true );
	//$audio_kind        = get_post_meta( $post->ID, 'audio_kind', true );
	//$audio_format      = get_post_meta( $post->ID, 'audio_format', true );
	//$audio_channels    = get_post_meta( $post->ID, 'audio_channels', true );
	//$audio_size        = get_post_meta( $post->ID, 'audio_size', true );
	//$audio_bit_rate    = get_post_meta( $post->ID, 'audio_bit_rate', true );
	//$audio_sample_rate = get_post_meta( $post->ID, 'audio_sample_rate', true );
	
	// Use nonce for verification
	echo '<input type="hidden" name="audiotrack_file_noncename" id="audiotrack_file_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	
	// The actual fields for data entry
	echo '<p><label for="audiotrack_mp3_file">MP3 File</label> ';
	echo '<input type="text" name="audiotrack_mp3_file" value="' . $audiotrack_mp3_file . '" size="25" /></p>';
	
	/*
	echo '<p><label for="audio_kind">Kind</label> ';
	echo '<input type="text" name="audio_kind" value="' . $audio_kind . '" size="25" /></p>';
	
	echo '<p><label for="audio_format">Format</label> ';
	echo '<input type="text" name="audio_format" value="' . $audio_format . '" size="25" /></p>';
	
	echo '<p><label for="audio_channels">Channels</label> ';
	echo '<input type="text" name="audio_channels" value="' . $audio_channels . '" size="25" /></p>';

	echo '<p><label for="audio_size">Size</label> ';
	echo '<input type="text" name="audio_size" value="' . $audio_size . '" size="25" />kb</p>';
	
	echo '<p><label for="audio_bit_rate">Bit Rate</label> ';
	echo '<input type="text" name="audio_bit_rate" value="' . $audio_bit_rate . '" size="25" />kbps</p>';
	
	echo '<p><label for="audio_sample_rate">Sample Rate</label> ';
	echo '<input type="text" name="audio_sample_rate" value="' . $audio_sample_rate . '" size="25" />kHz</p>';
	*/
	
}

add_action( 'save_post', 'audio_save_postdata' );

function audio_save_postdata( $post_id ) {
	
	if ( !isset( $_POST['audiotrack_track_noncename'] ) ) {
		return $post_id;
	}
	
	if ( !wp_verify_nonce( $_POST['audiotrack_file_noncename'], plugin_basename( __FILE__ ) ) ) {
		return $post_id;
	}
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
	// to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return $post_id;
	
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}
	
	$mydata['ID3_TPE1'] = $_POST['ID3_TPE1'];
	$mydata['ID3_TCOM'] = $_POST['ID3_TCOM'];
	$mydata['audiotrack_mp3_file'] = $_POST['audiotrack_mp3_file'];
	
	update_post_meta( $post_id, 'ID3_TPE1', $mydata['ID3_TPE1'] );
	update_post_meta( $post_id, 'ID3_TCOM', $mydata['ID3_TCOM'] );
	update_post_meta( $post_id, 'audiotrack_mp3_file', $mydata['audiotrack_mp3_file'] );
	
	return $mydata;
	
}



?>