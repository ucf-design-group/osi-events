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


/* The following sets up the Custom Post Type for osi-events and its Custom Meta Box */

function oe_cpt() {

	register_post_type('osi-events', array(
		'labels' => array(
			'name' => 'Events',
			'singular_name' => 'Event',
			'add_new' => 'New Event',
			'add_new_item' => 'Add New Event',
			'edit_item' => 'Edit Event',
			'new_item' => 'New Event',
			'view_item' => 'View Event',
			'items_archive' => 'Event Archive',
			'search_items' => 'Search Events'),
		'description' => 'Events compatible with both the local site and the OSI Calendar',
		'public' => true,
		'hierarchical' => false,
		'supports' => array('title', 'editor'),
		'taxonomies' => array(),
		'has_archive' => false
		));

	register_post_type('events',array());
}
add_action('init', 'oe_cpt');


function oe_icon() {
	?>
	<style type="text/css" media="screen">
	#menu-posts-osi-events .wp-menu-image {
		background: url(<?php echo plugins_url("osi-events/rsc/calendar.png") ?>) no-repeat 6px -17px !important;
	}
	#menu-posts-osi-events:hover .wp-menu-image, #menu-posts-osi-events.wp-has-current-submenu .wp-menu-image {
		background-position: 6px 7px !important;
	}
	</style>
<?php }
add_action( 'admin_head', 'oe_icon' );


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
		'osi-events',
		'normal',
		'default');
}


function oe_meta() {

	global $post;
	wp_nonce_field(basename( __FILE__ ), 'oe-form-nonce' );
	$start = get_post_meta($post->ID, 'oe-form-start', true) ? get_post_meta($post->ID, 'oe-form-start', true) : time();
	$end = get_post_meta($post->ID, 'oe-form-end', true) ? get_post_meta($post->ID, 'oe-form-end', true) : time();
	$loc = get_post_meta($post->ID, 'oe-form-loc', true) ? get_post_meta($post->ID, 'oe-form-loc', true) : '';
	$contact = get_post_meta($post->ID, 'oe-form-contact', true) ? get_post_meta($post->ID, 'oe-form-contact', true) : '';
	$url = get_post_meta($post->ID, 'oe-form-url', true) ? get_post_meta($post->ID, 'oe-form-url', true) : '';
	$notes = get_post_meta($post->ID, 'oe-form-notes', true) ? get_post_meta($post->ID, 'oe-form-notes', true) : '';
	

	$checked = "checked = 'checked'";
	$disabled = "";
	$style = "";
	if ($end == 'none') {
		$checked = "";
		$disabled = "disabled='disabled'";
		$style = "style='background-color:#EEEEEE'";
		$end = $start;
	}

	?>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="<?php echo plugins_url("osi-events/eventform.css") ?>">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js"></script>
	<script type='text/javascript' src='<?php echo plugins_url("osi-events/eventform.js") ?>'></script>
	<table id="oe-form-main">
		<tr>
			<th><label for="oe-form-startdate">Start:</label></th>
			<td><input type="text" name="oe-form-startdate" id="oe-form-startdate" value="<?php echo date('Y-m-d', $start);?>" onchange='startDateCheck()' /></td>
			<td><input type="text" name="oe-form-starttime" id="oe-form-starttime" value="<?php echo date('g:00', $start);?>" onchange='startTimeCheck()' /></td>
			<td><select name='oe-form-startampm' id='oe-form-startampm'>
					<option value='am' <?php if(date('a', $start) == 'am') echo 'selected="selected"'?>>am</option>
					<option value='pm' <?php if(date('a', $start) == 'pm') echo 'selected="selected"'?>>pm</option>
				</select>
			</td>
		</tr><tr>
			<td></td>
			<td id="oe-form-datelabel"><em> <?php echo date('Y-m-d', $start);?></em></td>
			<td colspan="2"><em> <?php echo date('g', $start) . " or " . date('g:i', $start);?></em></td>
		</tr><tr>
			<th><label for="oe-form-enddate">End:</label></th>
			<td><input type="text" name="oe-form-enddate" id="oe-form-enddate" value="<?php echo date('Y-m-d', $end);?>" <?php echo $style.' '.$disabled; ?> onchange='endDateCheck()' /></td>
			<td><input type="text" name="oe-form-endtime" id="oe-form-endtime" value="<?php echo date('g:00', $end);?>" <?php echo $style.' '.$disabled; ?> /></td>
			<td><select name='oe-form-endampm' id='oe-form-endampm' <?php echo $style.' '.$disabled; ?>>
					<option value='am' <?php if(date('a', $end) == 'am') echo 'selected="selected"'?>>am</option>
					<option value='pm' <?php if(date('a', $end) == 'pm') echo 'selected="selected"'?>>pm</option>
				</select>
			</td>
		</tr><tr>
			<td></td>
			<td colspan='3'><input type='checkbox' name='oe-form-endcheck' id='oe-form-endcheck' value='use' <?php echo $checked; ?> onchange='endCheckBox()' /><span> Use End Time</span></td>
		</tr><tr>
			<th colspan='4'><label for="oe-form-loc">Location:</label></th>
		</tr><tr>
			<td colspan='4'><input type="text" name="oe-form-loc" id="oe-form-loc" value="<?php echo $loc; ?>" /></td>
		</tr><tr>
			<th colspan='4'><label for="oe-form-contact">E-Mail Contact (optional):</label></th>
		</tr><tr>
			<td colspan='4'><input type="text" name="oe-form-contact" id="oe-form-contact" value="<?php echo $contact; ?>" /></td>
		</tr>
	</table>
	<table id="oe-form-secondary">
		<tr>
			<th><label for="oe-form-url">URL for More Info (optional):</label></th>
		</tr><tr>
			<td><input type="text" name="oe-form-url" id="oe-form-url" value="<?php echo $url; ?>" /></td>
		</tr><tr>
			<th><label for="oe-form-notes">Important Notes (optional):</label></th>
		</tr><tr>
			<td><textarea name="oe-form-notes" id="oe-form-notes"><?php echo $notes; ?></textarea></td>
		</tr>
	</table>
	<div id="oe-form-clear">Have questions/comments/issues concerning this form?  Please <a href="mailto:web@aj-foster.com">let me know</ad>.</div>
	<?php
}


function oe_meta_save() {

	global $post;
	$post_id = $post->ID;
	if (!isset($_POST['oe-form-nonce']) || !wp_verify_nonce($_POST['oe-form-nonce'], basename( __FILE__ ))) {
		return $post->ID;
	}

	$post_type = get_post_type_object($post->post_type);

	if (!current_user_can($post_type->cap->edit_post, $post_id)) {
		return $post->ID;
	}

	$input = array();

	$input['start'] = strtotime((isset($_POST['oe-form-startdate']) ? $_POST['oe-form-startdate'] : '') . 
		' ' . (isset($_POST['oe-form-starttime']) ? $_POST['oe-form-starttime'] : '') . 
		' ' . (isset($_POST['oe-form-startampm']) ? $_POST['oe-form-startampm'] : '') . " America/New_York");
	
	if (isset($_POST['oe-form-endcheck']) && $_POST['oe-form-endcheck'] == 'use') {
		$input['end']= strtotime((isset($_POST['oe-form-enddate']) ? $_POST['oe-form-enddate'] : '') . 
			' ' . (isset($_POST['oe-form-endtime']) ? $_POST['oe-form-endtime'] : '') . 
			' ' . (isset($_POST['oe-form-endampm']) ? $_POST['oe-form-endampm'] : '') . " America/New_York");
	}
	else {
		$input['end'] = 'none';
	}

	$input['loc'] = (isset($_POST['oe-form-loc']) ? $_POST['oe-form-loc'] : '');
	$input['contact'] = (isset($_POST['oe-form-contact']) ? $_POST['oe-form-contact'] : '');
	$input['url'] = (isset($_POST['oe-form-url']) ? $_POST['oe-form-url'] : '');
	$input['notes'] = (isset($_POST['oe-form-notes']) ? $_POST['oe-form-notes'] : '');

	foreach ($input as $field => $value) {

		$old = get_post_meta($post_id, 'oe-form-' . $field, true);

		if ($value && '' == $old)
			add_post_meta($post_id, 'oe-form-' . $field, $value, true );
		else if ($value && $value != $old)
			update_post_meta($post_id, 'oe-form-' . $field, $value);
		else if ('' == $value && $old)
			delete_post_meta($post_id, 'oe-form-' . $field, $old);
	}
}


/* The following creates a new column in the osi-events post listing and sorts the posts by date */

function edit_event_columns($columns) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Event Title'),
		'startdate' => __('Start Date'),
		'date' => __('Date Added'),
	);
	return $columns;
}
add_filter('manage_edit-osi-events_columns','edit_event_columns') ;


function manage_event_columns($column, $post_id) {
	global $post;
	switch($column) {
		case 'startdate':
			$start = get_post_meta( $post_id, 'oe-form-start', true );
			if (empty( $start )) echo __( 'Unknown' );
			else echo date('Y-m-d', $start);
			break;
	}
}
add_action( 'manage_osi-events_posts_custom_column', 'manage_event_columns', 10, 2 );


function sortable_events_column($columns) {
	$columns['startdate'] = 'startdate';
	return $columns;
}
add_filter( 'manage_edit-osi-events_sortable_columns', 'sortable_events_column' );


function edit_events_load() {
	add_filter( 'request', 'sort_events' );
}


function sort_events($vars) {
	if (isset($vars['post_type']) && 'osi-events' == $vars['post_type']) {
		$vars['orderby'] = 'startdate';
		if (isset($vars['orderby']) && 'startdate' == $vars['orderby']) {
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'oe-form-start',
					'orderby' => 'meta_value_num'
				)
			);
		}
	}
	return $vars;
}
add_action( 'load-edit.php', 'edit_events_load' );


/*function oe_menu() {

	add_options_page('Custom Plugin Page', 'OSI Events Settings', 'manage_options', 'osi-events', 'oe_settings_page');
	add_action( 'admin_init', 'register_oe_settings' );
}
add_action('admin_menu', 'oe_menu');


function register_oe_settings() {

	register_setting( 'oe-settings-group', 'oe_public' );
}

function oe_settings_page() {
?>
<div>
	<h2>My custom plugin</h2>
	<p>Options relating to the OSI Events plugin</p>
	<form action="options.php" method="post">
		<?php settings_fields('oe_settings'); ?>
		<?php do_settings_sections('osi-events'); ?> 
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
</div>

<?php
}

function plugin_admin_init(){

	register_setting('oe_settings', 'oe_settings', 'oe_settings_validate' );
	add_settings_section('oe_settings_main', 'Plugin Settings', 'oe_settings_main_text', 'osi-events');
	add_settings_field('oe_settings_public', 'this is parameter 2 of add_settings_field()', 'plugin_setting_string', 'osi-events', 'oe_settings_main');
}
add_action('admin_init', 'plugin_admin_init');

function oe_settings_main_text() { ?>
	<p>Description text</p>
<?php
}

function plugin_setting_string() {
	echo "<input id='oe_settings_public' name='oe_settings[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

function oe_settings_validate($input) {
	$newinput['text_string'] = trim($input['text_string']);
	if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string'])) {
		$newinput['text_string'] = '';
	}
	return $newinput;
}*/


add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
add_options_page('Custom Plugin Page', 'Custom Plugin Menu', 'manage_options', 'plugin', 'plugin_options_page');
}

// display the admin options page
function plugin_options_page() {
?>
<div>
<h2>My custom plugin</h2>
Options relating to the Custom Plugin.
<form action="options.php" method="post">
<?php settings_fields('plugin_options'); ?>
<?php do_settings_sections('plugin'); ?>
 
<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div>
 
<?php
}


add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init(){
	register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
	add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
	add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
}

function plugin_setting_string() {
	$options = get_option('plugin_options');
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}


function plugin_options_validate($input) {
	$newinput['text_string'] = trim($input['text_string']);
	if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string'])) {
		$newinput['text_string'] = '';
	}
	return $input;
}

/*
function plugin_options_validate($input) {
	$options = get_option('plugin_options');
	$options['text_string'] = trim($input['text_string']);
	if(!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
	$options['text_string'] = '';
	}
	return $options;
}
*/