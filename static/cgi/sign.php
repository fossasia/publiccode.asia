<?php

$error = 0;  // error status

// Database path
$db = "../userdata/signatures.json";

// Get info from form
$name = $_GET['name'];
$email = $_GET['email'];
$country = $_GET['country'];
$zip = $_GET['zip'];
$perm = $_GET['permission'];

// Validate input

// Create a random string for email verification
$code = rand(1000000000,9999999999) . uniqid();

// Read database
$file = file_get_contents($db, true);
$data = json_decode($file, true);
unset($file);

// Test whether email is a duplicate
$total = count($data);
for ($row = 0; $row <= $total; $row++) {
  if ($email === $data[$row]['email']) {
    echo "email $email already exists!";
    $error = 1;
    break 1;
  }
}

if ($error === 0) {   // only make entry if no error happened
  // Take sequential ID
  $id = $total;

  // Append new signature to array
  $newsig = array("id" => $id,
                  "name" => $name, 
                  "email" => $email,
                  "country" => $country,
                  "zip" => $zip,
                  "perm" => $perm,
                  "code" => $code);
  $data[] = $newsig;  // newsig is a separated variable for debugging purposes

  // Encode to JSON again and write to file
  $allsig = json_encode($data, JSON_PRETTY_PRINT);
  file_put_contents($db, $allsig, LOCK_EX);
  unset($allsig);
  
  // Send email asking for confirmation
  $to      = $email;
  $subject = "One step left to sign the open letter";
  $message = "Please confirm $code";
  $headers = "From: noreply@mehl.mx" . "\r\n" .
      "Message-ID: confirmation-$code@fsfe.org" . "\r\n" .
      "X-Mailer: PHP/" . phpversion();

  mail($to, $subject, $message, $headers);
}

echo "<pre>";
print_r($data);
echo "</pre>";
unset($data);
?>
