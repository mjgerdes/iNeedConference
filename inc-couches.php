
<?php

include('inc-couches-texts.php');

include('inc-couches-db.php');

function inc_get_couches_shortcode() {
$out = "";
$out .= inc_couches_text_disclaimer();
return $out;
}

?>