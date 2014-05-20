#!/usr/local/bin/php

<?php

include('PHPMailer_5.2.4/class.phpmailer.php');

//get the current time and minus five so we can get the statuses that have been posted in the last five minutes
// date_default_timezone_set('Europe/London');
// $current = date('Y/m/D h:i:s a', time());
// $minusFive=date('Y/m/D h:i:s a', strtotime("-5 minutes"));
//
//
//
// //run the query
//  "Select * from status where date BETWEEN $current AND $minusfive";



//Create database connections.
$username = "root";
$password = "root";
$host = "localhost:8889";
$dbname = "car";
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
try {
    // This statement opens a connection to your database using the PDO library
    // PDO is designed to provide a flexible interface between PHP and many
    // different types of database servers.  For more information on PDO:
    // http://us2.php.net/manual/en/class.pdo.php
    $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
} catch (PDOException $ex) {
    // If an error occurs while opening a connection to your database, it will
    // be trapped here.  The script will output an error and stop executing.
    // Note: On a production website, you should not output $ex->getMessage().
    // It may provide an attacker with helpful information about your code
    // (like your database username and password).
    die("Failed to connect to the database: " . $ex->getMessage());
}

// This statement configures PDO to throw an exception when it encounters
// an error.  This allows us to use try/catch blocks to trap database errors.
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// This statement configures PDO to return database rows from your database using an associative
// array.  This means the array will have string indexes, where the string value
// represents the name of the column in your database.
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// This block of code is used to undo magic quotes.  Magic quotes are a terrible
// feature that was removed from PHP as of PHP 5.4.  However, older installations
// of PHP may still have magic quotes enabled and this code is necessary to
// prevent them from causing problems.  For more information on magic quotes:
// http://php.net/manual/en/security.magicquotes.php
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    function undo_magic_quotes_gpc(&$array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                undo_magic_quotes_gpc($value);
            } else {
                $value = stripslashes($value);
            }
        }
    }

    undo_magic_quotes_gpc($_POST);
    undo_magic_quotes_gpc($_GET);
    undo_magic_quotes_gpc($_COOKIE);
}





//Get the e-mail addresses of all users who need to be notifed.

$query = "SELECT DISTINCT driver.email
FROM driver INNER JOIN orderdetails
ON driver.driverid = orderdetails.driverid
INNER JOIN status
ON orderdetails.fleetid = status.fleetid
INNER JOIN notification
ON orderdetails.fleetid = notification.fleetid
WHERE notification.email = 'Y'
AND status.notification = '0'";
$sth = $db->query($query);
$result = $sth->fetchAll();

var_dump($result[0]);


//send the e-mail
$mail = new PHPMailer();

$mail->IsSMTP();

    $mail->SMPTAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = "smtp.gmail.com";
    $mail->Mailer = "smtp";
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Port = 465;
    $mail->Username="maplematt";
    $mail->Password="Ripley0409L818rrp!";
    $mail->From ="maplematt@googlemail.com";
    $mail->FromName = 'maplematt@googlemail.com';
    $mail->AddAddress('matthew.boyle1@ge.com', 'matthew.boyle1@ge.com');
    foreach($result[0] as $r){
      $mail->AddAddress($r, $r);

    }






    $mail->WordWrap = 50; // set word wrap
    $mail->Priority = 1;

    //The following lines below can be changed. Should be fairly self explanatory.
    $mail->Subject = 'GE Fleet Services: The status of your order has changed.';

   $mail->Body =  'Hi';
   $mail->IsHTML(true); /* <== call IsHTML() after $mail->Body has been set. */

if(!$mail->Send()) {

echo "Mailer Error: " . $mail->ErrorInfo;

} else {

echo "Message sent!";

}
?>
