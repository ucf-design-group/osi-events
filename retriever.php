<?php

// Hopefully, this will be a loop for events.

if (isset($_GET['key']) && $_GET['key'] == 1234) {

	$start = (isset($_GET['start'])) ? $_GET['start'] : '';
	$end = (isset($_GET['end'])) ? $_GET['end'] : '';

	define('WP_USE_THEMES', false);
	require_once('../../../wp-load.php');
	header ("content-type: text/xml");

	retrieveEvents($start, $end);
}
else echo "Unauthorized";

function retrieveEvents($start, $end) {

	$returnXML = new SimpleXMLElement("<eventlisting></eventlisting>");

	$eventsQuery = array('post_type' => 'osi-events', 'posts_per_page' => -1, 'meta_key' => 'oe-form-start', 'orderby' => 'meta_value', 'order' => 'ASC');
	$loop = new WP_Query($eventsQuery);

	while ($loop->have_posts()) {

		$loop->the_post();
		global $post;

		$startDate = get_post_meta($post->ID, 'oe-form-start', true);

		if ($startDate > $start && $startDate < $end) {

			$event = $returnXML->addChild('event');

			$title = get_the_title();
			$event->addChild('title', $title);

			$description = get_the_content();
			$event->addChild('description', $description);

			$start = $startDate;
			$event->addChild('start', $start);

			$end = get_post_meta($post->ID, 'oe-form-end', true);

			$loc = get_post_meta($post->ID, 'oe-form-loc', true);
		}
	}

	echo $returnXML->asXML();
}