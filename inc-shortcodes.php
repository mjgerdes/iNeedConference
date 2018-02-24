<?php

function inc_get_register_attendee_shortcode($args) {
	 // start an output buffer so we can echo and return it later with ob_get_clean()
   ob_start();
include('inc-register.template.php');
return ob_get_clean();
}

add_shortcode('register_attendee', 'inc_get_register_attendee_shortcode');


?>