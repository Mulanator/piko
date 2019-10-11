<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Load Composer's autoloader
require 'vendor/autoload.php';

$db = 'piko';               // Datenbank-Name MySQL
$dbhost = 'localhost';      // MySQL Datenbankserver
$dbuser = 'root';           // MySQL User
$dbpw = 'xxx';           // MySQL Passwort
$mailto = array('person1@mail.de', 'person2@mail.de'); //Array an Empfängern
$mailfrom = 'absender@mail.de'; //Email-Konto des Senders
$mailfrompw = 'xxx'; //Passwort für Emailkonto Sender
$mailhost = 'smtp.1und1.com'; //Mailserver für den SMTP Versand

$link = mysqli_connect($dbhost, $dbuser, $dbpw, $db);
if (!$link) {
    echo "<PRE>Fehler: konnte nicht mit MySQL verbinden." . PHP_EOL;
    echo "\nDebug-Fehlernummer: " . mysqli_connect_errno() . PHP_EOL;
    echo "\nDebug-Fehlermeldung: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$today = date("Y-m-d");

$sql = "SELECT * FROM log WHERE Datum LIKE '%$today%'";

$res = mysqli_query($link, $sql);

ob_start();
while($row = mysqli_fetch_assoc($res)) {
   echo "<tr><td>$row[Datum]</td><td>".$row['Erzeugung aktuell']."</td><td>$row[Tagesenergie]</td><td>$row[Gesamtenergie]</td><td>$row[Status]</tr>";
   $tagesleistung = $row['Tagesenergie'];
}
mysqli_close($link);
$content = ob_get_contents();
ob_end_clean();

$body = '<p><strong>Zusammenfassung für den '.date("d.m.Y.").' : ';
$body .= 'Tagesenergie: '.$tagesleistung.' kWh</strong></p>';
$body .= '<table border="1">';
$body .= '<thead><tr><th>Uhrzeit</th><th>Aktuelle Leistung</th><th>Tagesenergie kWh</th><th>Gesamtenergie kWh</th><th>Status</th></tr></thead>';
$body .= "<tbody>$content</tbody>";
$body .= '</table>';

//Mailversand
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = $mailhost;                              // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $mailfrom;                              // SMTP username
    $mail->Password   = $mailfrompw;                            // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom($mailfrom, 'PV Anlage');
    foreach ($mailto as $recipient) {
    	$mail->addAddress($recipient);     // Add a recipient
    }
//    $mail->addAddress($mailto);     // Add a recipient
    $mail->addReplyTo('noreply@invalid.com', 'Information');
    
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Tagesbericht PV Anlage Römerstaße';

    $mail->Body    = $body;

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>
