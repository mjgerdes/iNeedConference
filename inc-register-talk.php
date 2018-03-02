
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

var_dump($_POST);

return $out;
}

function inc_register_talk_showform($out) {

$out['body'] = "<form enctype='multipart/form-data' method='POST' id='talk_form'>"
// auth
. "<label for='auth'><h3>Authentication Code</h3></label>"
. "<input required type='text' id='auth' name='auth' placeholder='e.g. 5-KWLXP' />"


// title
. "<label for='title'><h3>Title of the talk or workshop</h3></label>"
. "<input required type='text' id='talk' name='talk' />"

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