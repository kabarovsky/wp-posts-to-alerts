# wp-posts-to-alerts

This is plugin-free and wanna-do-it-myself solution for alerting WordPress users about some events. 

In this particular case, each WordPress post was an event, and the date of the event was set in ACF repeater sub-field.

alertsEmailBody() is for returning tomorrow's events, called as the $message argument for wp_mail() function.  

wp_next_scheduled() is for scheduling an event (the following function) on a daily basis.

sendingAlerts() checks if there are any events tomorrow, and if yes, wp_mail() will follow and actually send the message.
