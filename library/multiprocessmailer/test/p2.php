<?php

require_once('./Swift-4.1.1/lib/swift_required.php');


class px {
  //Create the Transport the call setUsername() and setPassword()
  $transport = Swift_SmtpTransport::newInstance('komplus.home.pl')
    ->setUsername('empiksla@komplus.pl')
    ->setPassword('Komunikator2011')
    ;
  
  //Create the Mailer using your created Transport
  $mailer = Swift_Mailer::newInstance($transport);
  
  //Create a message
  $message = Swift_Message::newInstance('Wonderful Subject')
    ->setFrom(array('empiksla@komplus.pl' => 'EmpikKomunikator'))
    ->setTo(array('xgustaf@gmail.com'))
    ->setBody('Here is the message itself')
    ;
  
  $failedRecipients = array();
  $numSent = 0;

    $message->setTo(array('xgustaf@gmail.com' => 'rr'));
    $message->setBody('abc: ' . $ii);
    $numSent += $mailer->send($message, $failedRecipients);
    echo '.';


  // var_dump($failedRecipients);
}



?>