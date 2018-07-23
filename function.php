<?php

function alertsEmailBody() {

	global $wpdb;

	$tomorrow = date("Ymd", strtotime('tomorrow'));

	$body .= 'The following events start tomorrow, ' . date("d.m.Y.", strtotime('tomorrow')) . '<br/>';

	// ACF resources: To successfully query sub field values, we need to remember that the row number is not known (there may be 1,2 or even 3 rows of repeater field data). Therefore, we need to use a LIKE clause in our SQL query to allow for a WILDCARD in the meta_key search. To do this, we create a custom filter to replace the standard ‘=’ with ‘LIKE’. (https://www.advancedcustomfields.com/resources/query-posts-custom-fields/)

	// WP codex: posts_where filter applies to the posts where clause and allows you to restrict which posts will show up in various areas of the site 

	function alertsQueryWildcard($where) { 
		$where = str_replace("meta_key = 'dates_$", "meta_key LIKE 'dates_%", $where);
		return $where;
	}
	add_filter('posts_where', 'alertsQueryWildcard');

	$args = array(
		'posts_per_page' => -1,
		'post_type'   => 'post',
		'post_status' => 'publish', 
		'meta_query'  => array(
    		'relation'    => 'AND',
	    	array(
	    		'key'   => 'dates_$_date_start',
	    		'compare' => '=',
	    		'value'   => $tomorrow,
	    	)
  		)
	);
	$the_query = new WP_Query($args);  

	if ($the_query->have_posts()) {
		while ($the_query->have_posts()) {
			$the_query->the_post();
			$body .= '<a href="'. get_permalink() .'" target="_blank" style="color:#3295ae;text-decoration:none;">' . get_the_title() . '</a><br>';
		} 
	}

	wp_reset_query();

}

if (!wp_next_scheduled('schedulingAlerts')) {
 	wp_schedule_event(strtotime('00:00:00'), 'daily', 'schedulingAlerts');
}
add_action('schedulingAlerts', 'sendingAlerts');

function sendingAlerts() {

	add_filter ("wp_mail_content_type", "alertsContentType");
	function alertsContentType() {
	 	return "text/html";
	}

	$to = 'email@address.com'; 
	$subject = 'ICO events starting tomorrow'; 
	$message = alertsEmailBody(); 

	global $wpdb;

	$recipients = $wpdb->get_results("SELECT DISTINCT wp_users.user_email AS email FROM wp_users INNER JOIN wp_usermeta ON wp_users.ID=wp_usermeta.user_id WHERE wp_usermeta.meta_value = 'Subscribed' ");

	foreach($recipients as $recepient) {
		$headers[] = 'Bcc: '.$recepient->email;
	}

	$headers[] = 'Content-Type: text/html; charset=UTF-8';

	function alertsWildcard($where) { 
		$where = str_replace("meta_key = 'dates_$", "meta_key LIKE 'dates_%", $where);
		return $where;
	}
	add_filter('posts_where', 'alertsWildcard');

	$args = array(
		'posts_per_page' => -1,
		'post_type'   => 'post',
		'post_status' => 'publish', 
		'meta_query'  => array(
	    	'relation'    => 'AND',
		    array(
		    	'key'   => 'dates_$_date_start',
		    	'compare' => '=',
		    	'value'   => $tomorrow,
		    )
	  	)
	);
	$the_query = new WP_Query($args);  

	if ($the_query->have_posts()) {
		wp_mail( $to, $subject, $message, $headers); 
	}

	wp_reset_query();
	 
}  

?>