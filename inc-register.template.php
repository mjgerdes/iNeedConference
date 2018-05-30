
<?php	
require_once('lib/recaptchalib.php');

/* success of receiving corect POST values and inserting new attendee
If this is set to true, the form is not shown. */
$success = false; 

/* message displayed in the page.
Only displayed if it evaluates to true */
$message =false;

// v2 captcha
$sitekey = "6Ley5lUUAAAAAPnEgdgJLBrdltPgViedhmIWUXlZ";
$secretkey = "6Ley5lUUAAAAABpI_KnJohUOkU8KvP2taExe2iQQ";
$captcha = false;
if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
}

// v1 captcha
$publickey = "6LdOwEUUAAAAAKBw90_IRRuAn5GNp7ATjP9aZUdm";
$privatekey = "6LdOwEUUAAAAAOiPKGPsHfEFXHOpq-bUm1SAan9G";


# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

# was there a reCAPTCHA response?
if ($_POST["recaptcha_response_field"]) {
        $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);
}

// if url is visited with get, then it is for email validation or user status information
if(isset($_GET['authcode'])) {
include("inc-register-validate.template.php");
} else if(isset($_POST["submit_form"])) {
  // we are here after user clicks submit

    $attendee_name = strip_tags($_POST["attendee_name"], "");
    $attendee_lastname = strip_tags($_POST["attendee_lastname"], "");


// we got post data, now we have to check if its ok
if ($attendee_name == "") {
// case if name was left blank
$message = "<b>Please, don't be a stranger. Tell us your name!</b>";
}else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
 // case if no valid email provided
$message = "<b>Please provide a valid email adress!</b>";
} else if (inc_attendee_email_exists($_POST['email'])) {
// case if email already in system
$message = "Sorry, it seems someone with this email has already registered.";
} else if (!$captcha) {
$message = "Please fill out the captcha form.";
} else if ($response.success != true) {
$message = "It seems you are a robot!";
/* old v1 stuff
} else if(!$resp->is_valid) {
// captcha wasnt solved
                # set the error code so that we can display it
                $error = $resp->error;
$message = "It seems you are a robot! Please try to solve the captcha field again.";
*/
}else {
// all good, insert into db
if($_POST['food'] == "other") {
$foodchoice = $_POST['other_spec'];
} else {
$foodchoice = $_POST['food'];
}

// removed vbb
/*
$id = inc_attendee_insert_from_valid($attendee_name, $attendee_lastname, $_POST['email'], strip_tags($_POST['note'], ""), strip_tags($foodchoice, ""), strip_tags($_POST['university'], ""),
inc_attendee_validate_VBB($_POST['vbb'], $_POST['vbb_choice']), isset($_POST['yoga']));
*/

$id = inc_attendee_insert_from_valid($attendee_name, $attendee_lastname, $_POST['email'], strip_tags($_POST['note'], ""), strip_tags($foodchoice, ""), strip_tags($_POST['university'], ""),
0, isset($_POST['yoga']));

if($id) {
// insert success, send email
$attendee = inc_attendee_from_id($id);
if($attendee) {
inc_send_attendee_validation_mail($attendee);
$success = true;
}
}


if(!$success) {
// id was returned null or some other weird error with db
$message = "Sorry, there was some trouble handling your request. Please try again or contact an administrator at help@tacos28.de."; 
} else {
// FIXME: replace email adress with a picture
$message = "Thank you $attendee_name, we are looking forward to meeting you! Please check your email and follow the instructions provided in the validation mail that our robots have just sent to you. Oh, and do <b>check your spam folder</b>. Remember, if you have any further questions about your registration, feel free to reach out to us at help@tacos28.de";
}
} // end of data validation
} // end of having post data


echo "<div>";

if($success) {
// new attendee was inserted into db
echo "<p>$message</p>";
} else if(!isset($_GET['authcode'])) {
// we were not yet successful inserting and get is not set -> we are still filling out the form
?>
<p>If you would like to attend TaCoS 28, please fill out the information below. Note that in order to cover expenses, we ask students for an attendance fee of 20€, to be transferred before registration closes.</p>
<h2>People who talk don't pay</h2>
<p>If you would like to avoid the attendance fee, please, consider <a href='http://tacos28.de/speak-at-tacos28'>speaking at TaCoS 28</a>. A mere lightning talk might get you into TaCoS for free!</p>
<br/>
<?php
if($message) {
echo "<div style='background-color: lightblue; margin: 5px; padding:3px;'><p><b>$message</b></p></div>";
}
?>
<br/>
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>

<form action="#validate_form" method="post" id="validate_form">
    <label for="attendee_name"><h3>Your first name</h3></label>
    <input required type="text" name="attendee_name" id="attendee_name" />
	<label for="attendee_lastname"><h3>Your last name</h3></label>
	<input required type="text" id="attendee_lastname" name="attendee_lastname" />
<label for="email"><h3>Your E-Mail adress</h3></label>
    <input required type="email" name="email" id="email" />
<label for="university"><h3>Your university</h3></label>
<input required type="text" id="university" name="university" />
<fieldset>
<p>Do you have a dietary preference?</p>
<label for="rvegan"><input type="radio" id="rvegan" name="food" value="vegan" checked="checked" />Vegan</label> 
<label for="rvegetarian"><input type="radio" id="rvegetarian" name="food" value="vegetarian" />Vegetarian</label>
<label for="rother"><input type="radio" id="rother" name="food" value="other" />Other <input type="text" name="other_spec" placeholder="Please specify" /></label>

</fieldset>
<ul>
<li>
<label><input type="checkbox" name="yoga" value="yes" />I am interested in participating in the free yoga course.</label>
</li>
</ul>
<label for="note"><h3>Anything you would like to tell us</h3></label>
<textarea name="note" id="note" rows="4"></textarea>
<?php
// echo recaptcha_get_html($publickey, $error);
// v1 disabled, recaptcha v2 follows

echo "<div class='g-recaptcha' data-sitekey='" . $sitekey . "'></div>";
?>
<br/>

<input type="submit" name="submit_form" value="Submit" />
</form>

<?php
} else {
// get is set, just output message
echo "<p>$message</p>";
}



// deleted vbb
/*
<li>
<label><input type="checkbox" name="vbb" value="yes" />I want to purchase reduced price tickets for public transportation in the Berlin and Brandenburg area (ABC-Ticket/Tageskarte) for <select name="vbb_choice"><option value="1">1 day</option><option value="2">2 days</option><option value="3" selected="selected">3 days</option></select>. Price is 5,30 € per day per ticket, you save 2,40 €. This will be included in your registration fee.</label>
</li>
*/
?>
</div>