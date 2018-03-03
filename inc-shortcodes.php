<?php

function inc_get_register_attendee_shortcode($args) {
	 // start an output buffer so we can echo and return it later with ob_get_clean()
   ob_start();
include('inc-register.template.php');
return ob_get_clean();
}

add_shortcode('register_attendee', 'inc_get_register_attendee_shortcode');


function inc_get_small_auth_shortcode() {
   ob_start();
// FIXME: url should not be hardcoded
$url = get_site_url() . "/register-to-attend";
echo "<form method='get' action='$url'>";
echo "<label for='small_auth'>Authentication Code</label>"
. "<input type='text' id='small_auth' name='authcode' placeholder='i.e. 5-QRCDB' />";
echo "</form>";


return ob_get_clean();
}

add_shortcode('small_auth', 'inc_get_small_auth_shortcode');


include('inc-register-talk.php');
add_shortcode('register_talk', 'inc_get_register_talk_shortcode');

?>