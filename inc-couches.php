
<?php

include('inc-couches-texts.php');

include('inc-couches-db.php');

function inc_get_couches_shortcode() {
$out = "";

if(isset($_POST['looking_submit'])) {
$res = inc_couches_looking_process();
$out .= $res[0];
$success = $res[1];

if($success) {
// this is all we do for the page in this case
return $out;
}
// if failure, then error message is already in $out
}

// default functions of page
$out .= "<h2>I am ...</h2>"
. inc_couches_looking()
. "<br/>"
. inc_couches_offering()
. "<br/>"
. inc_couches_deleting()
. "<br/>";

$out .= inc_couches_text_disclaimer();
return $out;
}

function inc_couches_looking() {
$out = "";

$out .= "<h3>... looking for a place to stay in the Potsdam/Berlin area!</h3>"
. inc_couches_texts_looking_explain();

$out .= "<form method='POST'  id='looking_form'>"
. "<label for='looking_authcode'>Authentication Code</label>"
. "<input type='text' name='looking_authcode' name='looking_authcode' placeholder='e.g. 5-QIFES' />"
. "<br/><button type='submit' name='looking_submit' value='looking_submit'>Show me the couches!</button>"
. "</form>";

return $out;
}

// this function returns two values, a string (the usual out) and a bool indicating success or failure
// the intent is to reprint the default functions of the page on failure
function inc_couches_looking_process() {
$out = "";

if(!isset($_POST['looking_authcode']) || $_POST['looking_authcode'] == "") {
$out .= "<p>Please provide a valid authentication code.</p>";
return array($out, false);
}

$attendee = inc_attendee_from_authcode(inc_attendee_authcode_from_string($_POST['looking_authcode']));

if(!$attendee) {
$out .= "<p>The provided authcode appears to be invalid.</p>";
return array($out, false);
}

if($attendee->status == inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION)) {
$out .= "<p>Sorry $attendee->name, you must first validate your account as described in your validation email before you can view the couch surfing bazaar.</p>";
return array($out, false);
}

// all seems good
$out .= inc_couches_texts_overview_explain($attendee);
$couches = inc_couches();
if(!$couches) {
$out .= inc_couches_texts_overview_none();
return array($out, true);
}

$out .= "<table>"
. "<th>E-Mail</th>"
. "<th>Location</th>"
. "<th>Description</th>";

foreach($couches as $couch) {
// email
$out .= "<td>$couch->email</td>"
// location
. "<td>$couch->location</td>"
// description
. "<td>$couch->description</td>";
}
$out .= "</table>";

return array($out, true);
}

function inc_couches_offering() {
$out = "";

return $out;
}

function inc_couches_deleting() {
$out = "";

return $out;
}
?>