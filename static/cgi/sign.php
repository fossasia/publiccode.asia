<?php

$codemod = 2138367;   // modificator with which the confirmation ID will be obfuscated
$output = "";
$selfurl = "http://pmpc-test.mehl.mx/cgi/sign.php";  // absolute URL of this PHP script
$db = "../userdata/signatures.json";  // Signature database path
$data = "";

// Get info from form
$action = isset($_GET['action']) ? $_GET['action'] : false;
if(empty($action)) {
  $output .= "No action defined.";
  show_page($output, 1);
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
    $output .= "At least one required variable is empty.";
    show_page($output, 1);
  }
} else if ($action === "confirm") {
  $confirmcode = isset($_GET['code']) ? $_GET['code'] : false;
  $confirmid = isset($_GET['id']) ? $_GET['id'] : false;
  
  // Check for missing required fields
  if(empty($confirmcode) || empty($confirmid)) {
    $output .= "Confirmation code or ID is missing.";
    show_page($output, 1);
  }
} else {
  $output .= "Invalid action.";
  show_page($output, 1);
}
// Continue only if action = sign/confirmation

// Validate input
//TODO

// Read database (should only be called if really needed)
function read_db($db) {
  global $data;   // declare $data a global variable to access it outside this function
  if (! file_exists($db)) {
    touch($db);
  }
  $file = file_get_contents($db, true);
  $data = json_decode($file, true);
  unset($file);
}


/// SIGNING ///
if ($action === "sign") {
  // Test whether email is a duplicate
  $total = count($data);
  for ($row = 0; $row < $total; $row++) {
    if ($email === $data[$row]['email']) {
      $output .= "We already received a signature with this email address.";
      show_page($output, 1);
    }
  }
  
  read_db($db);
  
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
              "In order to confirm your signature, please visit following link:\r\n" . 
              "$selfurl?action=confirm&id=$codeid&code=$code \r\n\r\n" .
              "If your confirmation succeeds, your signature will appear on the website within the next few hours.";
  $headers  = "From: noreply@fsfe.org" . "\r\n" .
              "Message-ID: <confirmation-$code@fsfe.org>" . "\r\n" .
              "X-Mailer: PHP/" . phpversion();

  mail($to, $subject, $message, $headers);
  
  $output .= "Thank you for signing our open letter! <br /><br />";
  $output .= "We just sent an email to your address ($email) for you to confirm your signature.";
  show_page($output, 0);

} else if ($action === "confirm") {
  /// CONFIRMATION ///
  
  $id = $confirmid - $codemod;              // substract the obfuscation number from the given ID
  if ($id < 0) {
    $output .= "Invalid signature ID.";
    show_page($output, 1);
  }
  
  read_db($db);
  
  if (empty($data[$id])) {
    $output .= "The signature ID does not exist.";
    show_page($output, 1);
  }
  
  $email = $data[$id]['email'];             // Get the user's email in case we need it
  $code = $data[$id]['code'];               // The confirmation code according to the DB
  $confirmed = $data[$id]['confirmed'];     // The current confirmation status
  
  // Check whether the confirmation code is what we saved in the DB
  if ($confirmed === "no") {
    if ($confirmcode === $code) {
      // Set the user's confirmation key to "yes"
      $data[$id]['confirmed'] = "yes";
      // Encode to JSON again and write to file
      $allsig = json_encode($data, JSON_PRETTY_PRINT);
      file_put_contents($db, $allsig, LOCK_EX);
      unset($allsig);
      
      $output .= "Your email address ($email) has been confirmed. <br /><br />";
      $output .= "Thank you for signing the open letter! Your signature will appear on the website within the next hours.";
      show_page($output, 0);
      
    } else {
      $output .= "The provided signature code is incorrect.";
      show_page($output, 1);
    }
  } else if ($confirmed === "yes") {
    $output .= "This email address is already confirmed. It can take a few hours until your signature appears online.";
    show_page($output, 1);
  } else {
    $output .= "This signature ID does not exist or the confirmation status is broken.";
    show_page($output, 1);
  }
  
} // END confirm

// --- PRINT OUTPUT IN TEMPLATE FILE ---

function replace_page($template, $placeholder, $content){
    $vars = array($placeholder=>$content);
    return str_replace(array_keys($vars), $vars, $template);
}

function show_page($output, $exit) {
  if ($exit === 0) {
    $headline = "Success";
    $notice = "";
  } else if ($exit === 1) {
    $headline = "Error";
    $notice = "This error could have happened because one or more fields contained invalid information. Please try again. If you think that you see this error by mistake, please contact us.";
  } else {
    $headline = "Thank you";
  }
  $template = file_get_contents('../template/index.html', true);
  $page = replace_page($template, ':HEADLINE:', $headline);
  $page = replace_page($page, ':BODY1:', $output);
  $page = replace_page($page, ':BODY2:', $notice);
  echo $page;
  unset($data);
  exit($exit);
}
?>
