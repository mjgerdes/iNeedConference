
<?php	
require_once('lib/recaptchalib.php');

/* success of receiving corect POST values and inserting new attendee
If this is set to true, the form is not shown. */
$success = false; 

/* message displayed in the page.
Only displayed if it evaluates to true */
$message =false;


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

} else if(!$resp->is_valid) {
// captcha wasnt solved
                # set the error code so that we can display it
                $error = $resp->error;
$message = "It seems you are a robot! Please try to solve the captcha field again.";
}else {
// all good, insert into db
if($_POST['food'] == "other") {
$foodchoice = $_POST['other_spec'];
} else {
$foodchoice = $_POST['food'];
}

$id = inc_attendee_insert_from_valid($attendee_name, $_POST['email'], strip_tags($_POST['note'], ""), strip_tags($foodchoice, ""), strip_tags($_POST['university'], ""));

if($id) {
// insert success, send email
$attendee = inc_attendee_from_id($id);
if($attendee) {
inc_send_attendee_validation_mail($attendee);
}
}
$success = true;
// FIXME: replace email adress with a picture
$message = "Thank you $attendee_name, we are looking forward to meeting you! Please check your email and follow the instructions provided in the validation mail that our robots have just sent to you. Oh, and do <b>check your spam folder</b>. Remember, if you have any further questions about your registration, feel free to reach out to us at help@tacos28.de";
} // end of data validation
} // end of having post data


echo "<div>";

if($success) {
// new attendee was inserted into db
echo "<p>$message</p>";
} else if(!isset($_GET['authcode'])) {
// we were not yet successful inserting and get is not set -> we are still filling out the form
?>
<p>If you would like to attend TaCoS 28, please fill out the information below.</p>
<?php
if($message) {
echo "<p><b>$message</b></p>";
}
?>
<br/>
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<form action="#validate_form" method="post" id="validate_form">
    <label for="attendee_name"><h3>Your name</h3></label>
    <input required type="text" name="attendee_name" id="attendee_name" />
<label for="email"><h3>Your E-Mail adress</h3></label>
    <input required type="email" name="email" id="email" />
<label for="university"><h3>Your university</h3></label>
<input required type="text" id="university" name="university" />
<fieldset>
<p>Do you have a dietary preference?</p>
<label for="rvegan">Vegan</label> 
<input type="radio" id="rvegan" name="food" value="vegan" checked="checked" />
<label for="rvegetarian">Vegetarian</label>
<input type="radio" id="rvegetarian" name="food" value="vegetarian" />
<label for="rother">Other</label>
<input type="radio" id="rother" name="food" value="other" />
<input type="text" name="other_spec" placeholder="Please specify" />
</fieldset>
<label for="note"><h3>Anything you would like to tell us</h3></label>
<textarea name="note" id="note" rows="4"></textarea>
<?php
echo recaptcha_get_html($publickey, $error);
?>
    <br/>

<input type="submit" name="submit_form" value="Submit" />
</form>

<?php
} else {
// get is set, just output message
echo "<p>$message</p>";
}

?>
</div>