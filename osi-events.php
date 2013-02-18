<?php
/*
Plugin Name: OSI Events
Plugin URI: 
Description: This plugin creates an "Events" post type and makes the events accessible by the OSI Events Calendar
Version: 1.0
Author: AJ Foster
Author URI: http://aj-foster.com/
License: None
*/

date_default_timezone_set('America/New_York');

$public = true;
$hierarchical = false;
$taxonomies = array();
$has_archive

function oe_cpt() {

	register_post_type('events', array(
		'labels' => array(
			'name' => 'Events',
			'singular_name' => 'Event'),
		'public' => $public,
		'hierarchical' => $hierarchical,
		'supports' => array('title', 'editor'),
		'register_meta_box_cb' => 'oe_meta_add',
		'taxonomies' => $taxonomies,
		'has_archive' => $has_archive,
		));
}
add_action('init', 'oe_cpt');









function oe_meta_setup() {
	add_action('add_meta_boxes','oe_meta_add');
	add_action('save_post','oe_meta_save');
}
add_action('load-post.php','oe_meta_setup');
add_action('load-post-new.php','oe_meta_setup');

function oe_meta_add() {
	add_meta_box (
		'oe_meta',
		'Event Information',
		'oe_meta',
		'events',
		'side',
		'default');
}

function oe_meta() {
	global $post;
	wp_nonce_field(basename( __FILE__ ), 'oe_meta_nonce' );
	$start = get_post_meta($post->ID, 'oe-meta-start', true) ? get_post_meta($post->ID, 'oe-meta-start', true) : time();
	$end = get_post_meta($post->ID, 'oe-meta-end', true) ? get_post_meta($post->ID, 'oe-meta-end', true) : time();
	$loc = get_post_meta($post->ID, 'oe-meta-loc', true) ? get_post_meta($post->ID, 'oe-meta-loc', true) : '';
	$category = get_post_meta($post->ID, 'oe-meta-cat', true) ? get_post_meta($post->ID, 'oe-meta-cat', true) : 'other';

	?>
	<table>
		<tr>
			<th><label for="oe-meta-startdate">Start:</label></th>
			<td><input type="text" name="oe-meta-startdate" id="oe-meta-startdate" value="<?php echo date('Y-m-d', $start);?>" size="8" /></td>
			<td><input type="text" name="oe-meta-starttime" id="oe-meta-starttime" value="<?php echo date('g:i', $start);?>" size="3" />
			<select name='oe-meta-startampm'>
				<option value='am' <?php if(date('a', $start) == 'am') echo 'selected="selected"'?>>am</option>
				<option value='pm' <?php if(date('a', $start) == 'pm') echo 'selected="selected"'?>>pm</option>
			</select></td>
		</tr><tr>
			<th><label for="oe-meta-enddate">End:</label></th>
			<td><input type="text" name="oe-meta-enddate" id="oe-meta-enddate" value="<?php echo date('Y-m-d', $end);?>" size="8" /></td>
			<td><input type="text" name="oe-meta-endtime" id="oe-meta-endtime" value="<?php echo date('g:i', $end);?>" size="3" />
			<select name='oe-meta-endampm'>
				<option value='am' <?php if(date('a', $end) == 'am') echo 'selected="selected"'?>>am</option>
				<option value='pm' <?php if(date('a', $end) == 'pm') echo 'selected="selected"'?>>pm</option>
			</select></td>
		</tr><tr>
			<td></td>
			<td><strong>&nbsp;&nbsp;<?php echo date('Y-m-d', esttime()); ?></strong></td>
			<td><strong>&nbsp;&nbsp;<?php echo date('g:i', esttime()); ?></strong></td>
		</tr>
		<tr>
			<th>Where:</th>
			<td colspan='2'><input type="text" name="oe-meta-loc" id="oe-meta-loc" value="<?php echo $loc; ?>" size="30" /></td>
		</tr>
	</table>
	<?php
}

function oe_meta_save() {
	global $post;
	$post_id = $post->ID;
	if (!isset($_POST['oe_meta_nonce']) || !wp_verify_nonce($_POST['oe_meta_nonce'], basename( __FILE__ ))) {
		return $post->ID;
	}

	$post_type = get_post_type_object($post->post_type);

	if (!current_user_can($post_type->cap->edit_post, $post_id)) {
		return $post->ID;
	}

	$new_start = strtotime((isset($_POST['oe-meta-startdate']) ? $_POST['oe-meta-startdate'] : '') . 
		' ' . (isset($_POST['oe-meta-starttime']) ? $_POST['oe-meta-starttime'] : '') . 
		' ' . (isset($_POST['oe-meta-startampm']) ? $_POST['oe-meta-startampm'] : ''));
	$new_end = strtotime((isset($_POST['oe-meta-enddate']) ? $_POST['oe-meta-enddate'] : '') . 
		' ' . (isset($_POST['oe-meta-endtime']) ? $_POST['oe-meta-endtime'] : '') . 
		' ' . (isset($_POST['oe-meta-endampm']) ? $_POST['oe-meta-endampm'] : ''));
	$new_loc = (isset($_POST['oe-meta-loc']) ? $_POST['oe-meta-loc'] : '');
	$new_cat = isset($_POST['oe-meta-cat']) ? $_POST['oe-meta-cat'] : '';

	$old_start = get_post_meta($post_id, 'oe-meta-start', true);
	$old_end = get_post_meta($post_id, 'oe-meta-end', true);
	$old_loc = get_post_meta($post_id, 'oe-meta-loc', true);
	$old_cat = get_post_meta($post_id, 'oe-meta-cat', true);

	if ($new_start && '' == $old_start)
		add_post_meta($post_id, 'oe-meta-start', $new_start, true );
	else if ($new_start && $new_start != $old_start)
		update_post_meta($post_id, 'oe-meta-start', $new_start);
	else if ('' == $new_start && $old_start)
		delete_post_meta($post_id, 'oe-meta-start', $old_start);

	if ($new_end && '' == $old_end)
		add_post_meta($post_id, 'oe-meta-end', $new_end, true );
	else if ($new_end && $new_end != $old_end)
		update_post_meta($post_id, 'oe-meta-end', $new_end);
	else if ('' == $new_end && $old_end)
		delete_post_meta($post_id, 'oe-meta-end', $old_end);

	if ($new_loc && '' == $old_loc)
		add_post_meta($post_id, 'oe-meta-loc', $new_loc, true );
	else if ($new_loc && $new_loc != $old_loc)
		update_post_meta($post_id, 'oe-meta-loc', $new_loc);
	else if ('' == $new_loc && $old_loc)
		delete_post_meta($post_id, 'oe-meta-loc', $old_loc);

	if ($new_cat && '' == $old_cat)
		add_post_meta($post_id, 'oe-meta-cat', $new_cat, true );
	else if ($new_cat && $new_cat != $old_cat)
		update_post_meta($post_id, 'oe-meta-cat', $new_cat);
	else if ('' == $new_cat && $old_cat)
		delete_post_meta($post_id, 'oe-meta-cat', $old_cat);
}







// Worry about this later...

function sortable_event_column($columns) {
	$columns['startdate'] = 'startdate';
	return $columns;
}
add_filter( 'manage_edit-event_sortable_columns', 'sortable_event_column' );

function edit_event_load() {
	add_filter( 'request', 'sort_event' );
}

function sort_event($vars) {
	if (isset($vars['post_type']) && 'event' == $vars['post_type']) {
		if (isset($vars['orderby']) && 'startdate' == $vars['orderby']) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'event-meta-start',
					'orderby' => 'meta_value_num'
				)
			);
		}
	}
	return $vars;
}
add_action( 'load-edit.php', 'edit_event_load' );