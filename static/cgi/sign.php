<?php

$error = 0;           // error status
$codemod = 2138367;   // modificator with which the confirmation ID will be obfuscated

// Database path
$db = "../userdata/signatures.json";

// Get info from form
$action = isset($_GET['action']) ? $_GET['action'] : false;
if(empty($action)) {
  echo "No action defined.";
  exit(1);
} else if ($action === "sign") {
  $name = isset($_GET['name']) ? $_GET['name'] : false;
  $email = isset($_GET['email']) ? $_GET['email'] : false;
  $country = isset($_GET['country']) ? $_GET['country'] : false;
  $zip = isset($_GET['zip']) ? $_GET['zip'] : false;
  $permPriv = isset($_GET['permissionPriv']) ? $_GET['permissionPriv'] : false;
  $permNews = isset($_GET['permissionNews']) ? $_GET['permissionNews'] : false;
  $permPub = isset($_GET['permissionPub']) ? $_GET['permissionPub'] : false;
  
  // Check for missing required fields
  if(empty($name) || empty($email) || empty($permPriv)) {
    echo "At least one required variable is empty.";
    exit(1);
  }
} else if ($action === "confirm") {
  $confirmcode = isset($_GET['code']) ? $_GET['code'] : false;
  $confirmid = isset($_GET['id']) ? $_GET['id'] : false;
  
  // Check for missing required fields
  if(empty($confirmcode) || empty($confirmid)) {
    echo "Confirmation code or ID is missing.";
    exit(1);
  }
} else {
  echo "Invalid action.";
  exit(1);
}

// Validate input


// Read database
if (! file_exists($db)) {
  touch($db);
}
$file = file_get_contents($db, true);
$data = json_decode($file, true);
unset($file);

/// SIGNING ///
if ($action === "sign") {
  // Test whether email is a duplicate
  $total = count($data);
  for ($row = 0; $row < $total; $row++) {
    if ($email === $data[$row]['email']) {
      echo "email $email already exists!";
      $error = 1;
      break 1;
    }
  }

  if ($error === 0) {   // only make entry if no error happened
    // Take sequential ID
    $id = $total;
    // Create a random string for email verification
    $code = rand(1000000000,9999999999) . uniqid();
    $codeid = $id + $codemod;   // this is to obfuscate the real ID of the user if we don't want to publish this number

    // Append new signature to array
    $newsig = array("id" => $id,
                    "name" => $name, 
                    "email" => $email,
                    "country" => $country,
                    "zip" => $zip,
                    "permPriv" => $permPriv,
                    "permNews" => $permNews,
                    "permPub" => $permPub,
                    "code" => $code,
                    "confirmed" => "no");
    $data[] = $newsig;  // newsig is a separated variable for debugging purposes

    // Encode to JSON again and write to file
    $allsig = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($db, $allsig, LOCK_EX);
    unset($allsig);
    
    // Send email asking for confirmation
    $to       = $email;
    $subject  = "One step left to sign the \"Public Money - Public Code\" letter";
    $message  = "Thank you for signing the open \"Public Money - Public Code\" letter! \r\n\r\n" .
                "In order to confirm your signature, please visit following link:\r\n http://pmpc-test.mehl.mx/cgi/sign.php?action=confirm&id=$codeid&code=$code \r\n\r\n" .
                "If your confirmation succeeds, your signature will appear on the website within the next few hours.";
    $headers  = "From: noreply@mehl.mx" . "\r\n" .
                "Message-ID: <confirmation-$code@fsfe.org>" . "\r\n" .
                "X-Mailer: PHP/" . phpversion();

    mail($to, $subject, $message, $headers);
  }
} else if ($action === "confirm") {
  /// CONFIRMATION ///
  
  $id = $confirmid - $codemod;              // substract the obfuscation number from the given ID
  $email = $data[$id]['email'];             // Get the user's email in case we need it
  $code = $data[$id]['code'];               // The confirmation code according to the DB
  $confirmed = $data[$id]['confirmed'];     // The current confirmation status
  
  // Check whether the confirmation code is what we saved in the DB
  if ($confirmed === "no") {
    if ($confirmcode === $code) {
      echo "Your signature with the Email &lt;$email&gt; has been confirmed. <br />";
      echo "Thank you for signing the open letter!";
      
      // Set the user's confirmation key to "yes"
      $data[$id]['confirmed'] = "yes";
      // Encode to JSON again and write to file
      $allsig = json_encode($data, JSON_PRETTY_PRINT);
      file_put_contents($db, $allsig, LOCK_EX);
      unset($allsig);
      
    } else {
      echo "The given signature code is incorrect.";
    }
  } else {
    echo "You already confirmed your email address.";
  }
  
}

echo "<pre>";
print_r($data);
echo "</pre>";
unset($data);
?>
