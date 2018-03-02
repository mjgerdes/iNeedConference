
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

function inc_register_talk_maybe_filename($tmpname, $size) {
return ""; //FIXME: temporarily deisabled
}

/* The following functions all return an array of 'msg' and  'body', as per the controller shortcode function above. */


/* Validates all input from form and inserts a new talk into db if necessary. If fields are not valid, reprints the form and gives a message. */
function inc_register_talk_validateform($out) {
$predicates = array(
array("auth", "The provided authentication code was invalid. If you are not registered to attend the conference, please do so to get your authentication code. Otherwise, please check your validation email to ensure that you spelled your code correctly.", function ($auth) {
return true; // FIXME: temporarily disabled for testing
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
$filename = inc_register_talk_maybe_filename($_FILES['userfile']['tmp_name'], $_FILES['userfile']['size']);
}

// everything seems to check out, lets insert a new talk into db
//inc_talk_insert_from_valid($_POST['auth'], $_POST['title'], $_POST['subtitle'], $_POST['type'], $_POST['description'], $filename);

$out['body'] = "<h3>How exciting!</h3><p>Thank you for submitting your " . $_POST['type'] . ", we are going to take it under consideration! Please understand that the review process might take some time. We will inform you of our decision, wether '" . $_POST['subtitle'] . "' is a good fit for TaCoS28 as soon as we are able.</p>";
return $out;
}

function inc_register_talk_showform($out) {
$out['body'] = "<form enctype='multipart/form-data' method='POST' id='talk_form'>"
// auth
. "<label for='auth'><h3>Authentication Code</h3></label>"
. "<input required type='text' id='auth' name='auth' placeholder='e.g. 5-KWLXP' />"


// title
. "<label for='title'><h3>Title of the talk or workshop</h3></label>"
. "<input required type='text' id='title' name='title' />"

// subtitle
. "<label for='subtitle'><h3>Subtitle or one-sentence description of the talk</h3></label>"
. "<input required type='text' id='subtitle' name='subtitle' />"

// radios
. "<fieldset>"
. "<label for='rtalk'>Presentation</label>"
. "<input type='radio' id='rtalk' name='type' value='presentation' checked='checked' />"
. "<label =for='rlightning'>Lightning Talk (5 to 10 minute presentation)</label>"
. "<input type='radio' id='rlightning' name='type' value='lightning' />"
. "<label for='rworkshop'>Workshop</label>"
. "<input type='radio' id='rworkshop' name='type' value='workshop' />"
. "</fieldset>"

// longform description
. "<label for='description'><h3>Description</h3></label>"
. "<textarea required id='description' name='description' rows='8'></textarea>"

// pdf
. "<label for='pdf'><h3>Associated Paper<H3><p>If you have a PDF file that is associated with your talk or workshop, please upload it here.</p></label>"
. '<input type="hidden" name="MAX_FILE_SIZE" value="30000" />'
. '<input name="userfile" id="pdf" type="file" />'

// submit
. "<button type='submit' form='talk_form' name='submit_form' value='submit_form'>Submit for Review</button>"
. "</form>";

return $out;
}
?>