<?php

include("class.phpmailer.php");

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true; // enable SMTP authentication
// $mail->SMTPSecure = "ssl"; // sets the prefix to the servier
$mail->Host = "komplus.home.pl"; // sets GMAIL as the SMTP server
// $mail->Port = 25; // set the SMTP port
$mail->Username = "empiksla@komplus.pl"; // GMAIL username
$mail->Password = "Komunikator2011"; // GMAIL password
$mail->From = "empiksla@komplus.pl";
$mail->FromName = "EmpikKomunikator";
$mail->AddAddress("xgustaf@gmail.com");
$mail->Subject = "Test PHPMailer Message";
$mail->Body = "Hi! \n\n This was sent with phpMailer_example3.php.";
for ($ii = 0; $ii <=10; $ii++) {
if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
}
else {
  echo 'Message has been sent.';
}
}

?>
