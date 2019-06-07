# wp-posts-to-alerts

Each WordPress post is an event, and the date of the event has been set in ACF repeater sub-field.

alertsEmailBody() is for returning tomorrow's events, called for $message argument in wp_mail() function.  

wp_next_scheduled() is for scheduling an event (the following function) on a daily basis.

sendingAlerts() checks if there are any events tomorrow, and if yes, wp_mail() will follow and actually send the message.

Resources:

https://codex.wordpress.org/Function_Reference/wp_next_scheduled <br>
https://codex.wordpress.org/Plugin_API/Filter_Reference/wp_mail <br>
https://www.advancedcustomfields.com/resources/query-posts-custom-fields/
