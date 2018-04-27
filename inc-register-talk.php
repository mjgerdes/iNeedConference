
<?php
require("inc-talk.php");

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
echo "<div style='background-color: lightblue; margin: 5px; padding: 4px;'><p><b>" . $res['msg'] . "</b></p></div>";
echo "</br>";
}

echo "<div>" . $res['body'] . "</div>";

}

function inc_internal_seems_pdf($path) {
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
return $ext == "pdf";
}

function inc_internal_talk_next_talkid() {
global $wpdb;
$table_name = inc_talk_table_name();
$wpdb->get_results(
"SELECT id from $table_name");

// query was done, we only need to know the rows affected
return $wpdb->num_rows + 1;
}

function inc_register_talk_maybe_filename($attendee_id, $tmpname, $size) {
// this shuold not happen but oh well sanity check
if($size == 0) {
return "";
}

$filename = $attendee_id . "-" . inc_internal_talk_next_talkid() . ".pdf";
$fullpath = INC_DIR . "/pdf/" . $filename;
if(move_uploaded_file($tmpname, $fullpath)) {
// success
return $filename;
}

// some error occured, return empty signaling error
return "";
}

/* The following functions all return an array of 'msg' and  'body', as per the controller shortcode function above. */


/* Validates all input from form and inserts a new talk into db if necessary. If fields are not valid, reprints the form and gives a message. */
function inc_register_talk_validateform($out) {
$attendee = NULL; // if auth is valid, this will hold the attendee associated with the talk
$predicates = array(
array("auth", "The provided authentication code was invalid. If you are not registered to attend the conference, please do so to get your authentication code. Otherwise, please check your validation email to ensure that you spelled your code correctly.", function ($auth) use (&$attendee) {
// note: we captured by reference, so this will modify the $attendee at outer function scope
$attendee = inc_attendee_from_authcode(inc_attendee_authcode_from_string($auth));
return (bool)$attendee;
}),
array("title", "Sorry, your talk must have a title.", function($title) { return $title != ""; }),
array("subtitle", "Sorry, your talk must have a subtitle or short description.", function ($subtitle) { return $subtitle != ""; }),
array("type", "Please choose the type of contribution to TaCoS28.", function ($type) { return $type != ''; }),
array("description", "Please provide a longform description of the talk or workshop. If you think that a description is already sufficiently provided in the PDF you uploaded, please state so in the description.", function ($description) { return $description != ''; }));

foreach($predicates as $pred) {
$field = $pred[0];
$failure_response = $pred[1];
if(!$pred[2]($_POST[$field])) {
$out = inc_register_talk_showform($out);
$out['msg'] = $failure_response;
return $out;
}
}

// separately check the file
if(isset($_FILES['userfile'])) {
    if(!($_FILES['userfile']['error'] === UPLOAD_ERR_OK)) {
       $out['msg'] = "Sorry, file upload failed with error code " . $_FILES['userfile']['error'];
	   return inc_register_talk_showform($out);
}

if(!inc_internal_seems_pdf($_FILES['userfile']['name'])) {
$out['msg'] = "Sorry, but the uploaded file must be a pdf (and have a pdf extension).";
return inc_register_talk_showform($out);
}

if($_FILES['userfile']['size'] > 31457280) {
$out['msg'] = "Sorry, the file you uploaded was too large.";
return inc_register_talk_showform($out);
}

// good so far, try to move the file to a permanent location
$filename = inc_register_talk_maybe_filename($attendee->id, $_FILES['userfile']['tmp_name'], $_FILES['userfile']['size']);

if($filename == "") {
$out['msg'] = "Sorry, an error occured while trying to process the file you provided. Please try submitting your request again, perhaps changing the filename.";
return inc_register_talk_showform($out);
}
}

// everything seems to check out, lets insert a new talk into db
inc_talk_insert_from_valid($attendee->id, $_POST['title'], $_POST['subtitle'], $_POST['type'], $_POST['description'], $filename);

$out['body'] = "<h3>How exciting!</h3><p>Thank you, ". $attendee->name .", for submitting your " . $_POST['type'] . ", we are going to take it under consideration! Please understand that the review process might take some time. We will inform you of our decision wether '" . $_POST['subtitle'] . "' is a good fit for TaCoS28 as soon as we are able.</p>";
return $out;
}

function inc_register_talk_intromessage() {
return '<p>If you are a student and would like to speak before a highly interested audience of fellow students, the TaCoS may be the right venue for you. Consider proposing a contribution to the conference by filling out the form below.</p>'
. '<p>To encourage diversity in contributions, we would like to keep the list of acceptable topics intentionally vague, and therefore ask only that your presentation or workshop be, broadly speaking, situated within the field of computational linguistics. If you feel that you have a topic, but that it might not warrant the scope of a full presentation or workshop, please, consider giving a lightning talk, a very short, mini presentation designed expressly for this purpose.</p>'
. '<p>Please understand that by submitting a proposal you are entering a review process, in which the adequacy of your contribution to the conference will be judged by students involved in the TaCoS 28 organization. At no point is inclusion in the conference guaranteed. But don' . "'" . 't worry, there may still be some slots available in the schedule.</p>';
}

function inc_register_talk_showform($out) {
$out['body'] = inc_register_talk_intromessage()
. "<form enctype='multipart/form-data' method='POST' id='talk_form'>"
// auth
. "<label for='auth'><h3>Authentication Code</h3><p>You received this after <a href='/register-to-attend/'>registering to attend</a>.</label>"
. "<input required type='text' id='auth' name='auth' placeholder='e.g. 5-KWLXP' />"


// title
. "<label for='title'><h3>Title of the talk or workshop</h3></label>"
. "<input required type='text' id='title' name='title' />"

// subtitle
. "<label for='subtitle'><h3>Subtitle or one-sentence description of the talk</h3></label>"
. "<input required type='text' id='subtitle' name='subtitle' />"

// radios
. "<fieldset>"
. "<label for='rtalk'><input type='radio' id='rtalk' name='type' value='presentation' checked='checked' /> Presentation</label>"
. "<label for='rlightning'><input type='radio' id='rlightning' name='type' value='lightning' />Lightning Talk (5 to 10 minute presentation)</label>"
. "<label for='rworkshop'><input type='radio' id='rworkshop' name='type' value='workshop' /> Workshop</label>"
. "</fieldset>"

// longform description
. "<label for='description'><h3>Additional notes and description</h3><p>A longer description of your contribution and anything else you'd like to tell us, for instance, a prefered time and date.</p></label>"
. "<textarea required id='description' name='description' rows='8'></textarea>"

// pdf
. "<label for='pdf'><h3>Associated Paper<H3><p>If you have a PDF file that is associated with your talk or workshop, please upload it here.</p></label>"
. '<input type="hidden" name="MAX_FILE_SIZE" value="31457280" />'
. '<input name="userfile" id="pdf" type="file" />'

// submit
. "<button type='submit' form='talk_form' name='submit_form' value='submit_form'>Submit for Review</button>"
. "</form>";

return $out;
}
?>