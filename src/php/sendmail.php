<?php
function my_set_error($json, $msg_desc, $field = null, $field_msg = null)
{
  $json['status'] = 'error';
  $json['status_desc'] = $msg_desc;
  if(!empty($field)){
    $json['error_msg'][$field] = $field_msg;
  }
  return $json;
}

function my_validation($json, $from, $name, $message)
{
  $msg_desc = "Invalid Input!";
  if (empty($from)) {
    $json = my_set_error($json, $msg_desc, 'f_email', 'This is required!');
  } elseif (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
    $json = my_set_error($json, $msg_desc, 'f_email', 'Invalid email format!');
  }
  if (empty($name)) {
    $json = my_set_error($json, $msg_desc, 'f_name', 'This is required!');
  }
  if (empty($message)) {
    $json = my_set_error($json, $msg_desc, 'f_message', 'This is required!');
  }
  return $json;
}

$json = array(
  'status' => "success",
  'status_desc' => 'Thanks! Your message has been sent!',
  'error_msg' => array(
    'f_email' => '',
    'f_name' => '',
    'f_message' => '',
  )
);

$from     = !empty($_POST['f_email']) ?  $_POST['f_email'] : '';
$name    = !empty($_POST['f_name']) ?  $_POST['f_name'] : '';
$message  = !empty($_POST['f_message']) ? $_POST['f_message'] : '';
$subject  = !empty($_POST['f_subject']) ? $_POST['f_subject'] : 'General';

$json = my_validation($json, $from, $name, $message);

/** PHP Mailer */

// get PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/Exception.php";
require_once "PHPMailer/OAuth.php";
require_once "PHPMailer/POP3.php";
require_once "PHPMailer/SMTP.php";

$mail = new PHPMailer;

// setting SMTP Server
//Set PHPMailer to use SMTP.
$mail->isSMTP();
//Set SMTP host name
// IDWebhost isi dengan piyaman.idweb.host
$mail->Host = "id dengan host";
//Set this to true if SMTP host requires authentication to send email
$mail->SMTPAuth = true;
//Provide username and password
$mail->Username = "isi dengan username email";
$mail->Password = "isi dengan password email";
//If SMTP requires TLS encryption then set it
$mail->SMTPSecure = "ssl";
//Set TCP port to connect to
$mail->Port = 465;

// message that will be displayed when everything is OK :)
$okMessage = 'Your message successfully submitted. Thank you, I will get back to you soon!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';

$mail->From = "admin@putrisulistyowati.site";
$mail->FromName = $_POST['f_name'];
$mail->addAddress('zakarianur6@gmail.com', 'Putri Sulistyowati');
$mail->isHTML(true);

$mail->Subject = $_POST['f_subject'];
$mail->Body    = "<i>From : ".$_POST['f_email']."</i><br><i>Name : ".$_POST['f_name']."</i><br><i>Message : ".$_POST['f_message']."</i>";

if (!$mail->send()) {
  $m_err = error_get_last()['message'];  
  $json = my_set_error($json, 'Unable to send Email! '.$m_err);
}

echo json_encode($json);
