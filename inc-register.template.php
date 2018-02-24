
<?php	
$success = false;
$message = "Please, fill out the information below.";

if(isset($_POST["submit_form"])) {
  // we are here after user clicks submit

    $attendee_name = strip_tags($_POST["attendee_name"], "");
if ($attendee_name == "") {
// case if name was left blank
$message = "<b>Please, don't be a stranger. Tell us your name!</b>";
}else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
 // case if no valid email provided
$message = "<b>Please provide a valid email adress!</b>";
} else {
// all good, insert into db
global $wpdb;
$table_name = inc_attendee_table_name();

    $wpdb->insert( 
        $table_name, 
        array(
		"time" => current_time('mysql'),
		"auth" => inc_generate_auth(),
            'name' => $attendee_name,
			"email" => $_POST['email'],
			"status" => inc_attendee_status_code(-1),
			"note" => strip_tags($_POST['note'], "")
        )
    );
$success = true;
}
}

// form
if($success) {
echo "<p>Thank you $attendee_name, we are looking forward to meeting you! Please check your email and follow the instructions provided in the validation mail that our robots have just sent to you. Remember, if you have any further questions about your registration, feel free to reach out to us at help@tacos28.de</p>";
} else {

echo "<p>$message</p>";

?>

<br/>
<form action="#validate_form" method="post" id="validate_form">
    <label for="attendee_name"><h3>Your name</h3></label>
    <input required type="text" name="attendee_name" id="attendee_name" />

<label for="email"><h3>Your E-Mail adress</h3></label>
    <input required type="email" name="email" id="email" />

<label for="note"><h3>Anything you would like to tell us</h3></label>
<input type="textarea" name="note" id="note" />

<input type="submit" name="submit_form" value="submit" />
</form>

<?php
}
?>
