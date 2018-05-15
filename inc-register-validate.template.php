
<?php

// we know get is set

$attendee = inc_attendee_from_authcode(inc_attendee_authcode_from_string($_GET['authcode']));

if(!$attendee) {
$message = "Sorry, there seems to be a problem with validating your registration. Are you sure the authentication code is spelled correctly?";
} else if($attendee->status != inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION)) {
// we tell users in the mail that they can go to this page to check on their status, so let's print out some information about their registration
$message = "Hello $attendee->name, our robots say the following about your registration:<br/><pre>\n"
. inc_attendee_status_code_pretty(inc_attendee_status_code_reverse($attendee->status))
 . "\n</pre>";
} else {
// this case is actually validating an email link

$attendee->status = inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_PAYMENT); // set to awaiting payment
inc_attendee_update_status($attendee);

$message = "Thank you $attendee->name for validating your registration attempt. To conclude your registration, please follow the payment instructions that were provided to you.";
}

?>