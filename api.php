<?php

/* This information should be filled out for each agency's implementation. */

$agency = "";
$passcode = "";

$key = hash("md5", $agency . $passcode);

// Hopefully, this will be a loop for events.

if (isset($_GET['key']) && $_GET['key'] == $key) {

	$start = (isset($_GET['start'])) ? $_GET['start'] : '0';
	$end = (isset($_GET['end'])) ? $_GET['end'] : '-1';

	define('WP_USE_THEMES', false);
	//require_once('../../../wp-load.php');
	require_once('../wp/wp-load.php');
	header ("content-type: text/xml");

	retrieveEvents($start, $end);
}
else echo "Unauthorized";

function retrieveEvents($startLimit, $endLimit) {

	$returnXML = new SimpleXMLElement("<eventlisting></eventlisting>");

	$eventsQuery = array('post_type' => 'osi-events', 'posts_per_page' => -1, 'meta_key' => 'oe-form-start', 'orderby' => 'meta_value', 'order' => 'ASC');
	$loop = new WP_Query($eventsQuery);

	while ($loop->have_posts()) {

		$loop->the_post();
		global $post;

		$startDate = get_post_meta($post->ID, 'oe-form-start', true);

		if ($startDate > $startLimit && ($startDate < $endLimit || $endLimit == "-1")) {

			$event = $returnXML->addChild('event');

			$title = get_the_title();
			$event->addChild('title', $title);

			$description = get_the_content();
			$event->addChild('description', $description);

			$start = $startDate;
			$event->addChild('start', $start);

			$end = get_post_meta($post->ID, 'oe-form-end', true);
			$event->addChild('end', $end);

			$loc = get_post_meta($post->ID, 'oe-form-loc', true);
			$event->addChild('location', $loc);

			$contact = get_post_meta($post->ID, 'oe-form-contact', true);
			$event->addChild('contact', $contact);

			$url = get_post_meta($post->ID, 'oe-form-url', true);
			$event->addChild('url', $url);

			$notes = get_post_meta($post->ID, 'oe-form-notes', true);
			$event->addChild('notes', $notes);

			$event->addChild('agency', $agency);
		}
	}

	echo $returnXML->asXML();
}