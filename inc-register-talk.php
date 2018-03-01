
<?php

function inc_get_register_talk_shortcode() {
$out = array("msg" => "", "body" => "");

if(isset($_POST['submit_form'])) {
  // user clicked submit
$res = inc_register_talk_validateform($out);
} else {
// original page, display form
$res = inc_register_talk_showform($out);
}

if($res['msg'] != "") {
echo "<p><b>" . $res['msg'] . "</b></p>";
echo "</br>";
}

echo "<div>" . $res['body'] . "</div>";

}


/* The following functions all return an array of 'msg' and  'body', as per the controller shortcode function above. */

function inc_register_talk_validateform($out) {
$out = array("msg" => "", "body" => "");

return $out;
}

function inc_register_talk_showform($out) {


return $out;
}
?>