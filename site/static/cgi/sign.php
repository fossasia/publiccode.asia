<?php
$timer_start = microtime(true);  // Start counter for PHP execution time tracking

$codemod = 2138367;   // modificator with which the confirmation ID will be obfuscated
$output = "";
$selfurl = "http://pmpc.mehl.mx/cgi/sign.php";  // absolute URL of this PHP script
$db = "/usr/share/blog/data/signatures/signatures.json";  // Signature database path
$ipdb = "/usr/share/blog/data/signatures/ips.json";  // IP database path
$spamdb = "/usr/share/blog/data/signatures/spammer_" . date('Y-m-d') . ".json";  // This day's potential spammer database

///////////////////
/// SPAM CHECKS ///
///////////////////

// Test whether visitor fell for honeypot
$honeypot = isset($_POST['url']) ? $_POST['url'] : false;
if (! empty($honeypot)) {   // honeypot input field isn't empty
  $output .= "Invalid input. Error code: 5|°4m";
  show_page($output, 1);
}

// Check whether IP submitted too often
$limit_hits = 5;          // Max. X hits...
$limit_time = 180;        // in X seconds
$limit_spam = 15;         // More than X hits in $limit_time will get this IP to a special DB
$limit_exceeded = FALSE;  // Will be set TRUE if more hits with this IP in $limit_time than $limit_hits 
$now = time();  // Current UNIX time
$ip = sha1(php_uname() . $_SERVER['REMOTE_ADDR']);  // Hashed IP of visitor
read_ips($ipdb);

$ip_unknown = TRUE;
foreach ($ips as $key => &$entry) {
  if ($now - $limit_time > $entry['time']) { // Delete entries that are older than $limit_time
    unset($ips[$key]);
  } else if ($entry['ip'] === $ip) {  // IP matches, and entry under $limit_time
    $ip_unknown = FALSE;
    if ($entry['hits'] >= $limit_hits) {  // Try limit exceeded, iterate and set abort variable
      $entry['hits'] = $entry['hits'] + 1;
      $limit_exceeded = TRUE;
      if ($entry['hits'] > $limit_spam) { // IP exceeds spam limit
        if (! file_exists($spamdb)) { touch($spamdb); }
        $realip = $_SERVER['REMOTE_ADDR'];
        $spammer = file_get_contents($spamdb);
        $pattern = preg_quote($realip);
        if (! preg_match("/$pattern/", $spammer, $match)) {
          file_put_contents($spamdb, $realip . "\n", FILE_APPEND | LOCK_EX);
        }
      }
    } else {  // Try limit not exceeded, just iterate
      $entry['hits'] = $entry['hits'] + 1;
    }
  }
}
// Extend IP database if this IP was unknown
if ($ip_unknown) {
  $ips[] = array("time" => $now, "ip" => $ip, "hits" => 1);
}
// Write IP database back to file
file_put_contents($ipdb, json_encode($ips, JSON_PRETTY_PRINT), LOCK_EX);
unset($ips);

// Abort if IP limit is exceeded
if ($limit_exceeded) {
  $output .= "Too many submits with your IP. Please try again in a few minutes.";
  show_page($output, 1);
}

///////////////////////
/// FORM EVALUATION ///
///////////////////////

// Get basic info from form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : false;
} else {
  $action = isset($_GET['action']) ? $_GET['action'] : false;
}

// Continue only if action = sign/confirmation
// Depending on action, get important variables
if(empty($action)) {
  $output .= "No action defined.";
  show_page($output, 1);
} else if ($action === "sign") {  // sign
  $name = isset($_POST['name']) ? $_POST['name'] : false;
  $email = isset($_POST['email']) ? $_POST['email'] : false;
  $country = isset($_POST['country']) ? $_POST['country'] : false;
  $zip = isset($_POST['zip']) ? $_POST['zip'] : false;
  $permPriv = isset($_POST['permissionPriv']) ? $_POST['permissionPriv'] : false;
  $permNews = isset($_POST['permissionNews']) ? $_POST['permissionNews'] : false;
  $permPub = isset($_POST['permissionPub']) ? $_POST['permissionPub'] : false;
  
  // Check for missing required fields
  if(empty($name) || empty($email) || empty($permPriv)) {
    $output .= "At least one required variable is empty.";
    show_page($output, 1);
  }
} else if ($action === "confirm") { // confirm
  $confirmcode = isset($_GET['code']) ? $_GET['code'] : false;
  $confirmid = isset($_GET['id']) ? $_GET['id'] : false;
  
  // Check for missing required fields
  if(empty($confirmcode) || empty($confirmid)) {
    $output .= "Confirmation code or ID is missing.";
    show_page($output, 1);
  }
} else {  // invalid
  $output .= "Invalid action.";
  show_page($output, 1);
}

// Validate input
//TODO

///////////////
/// SIGNING ///
///////////////
if ($action === "sign") {
  read_db($db);
  
  // Test whether email is a duplicate
  $total = count($data);
  for ($row = 0; $row < $total; $row++) {
    if ($email === $data[$row]['email']) {
      $output .= "We already received a signature with this email address.";
      show_page($output, 1);
    }
  }
  
  // Take sequential ID
  $id = $total;
  // Create a random string for email verification
  $code = rand(1000000000,9999999999) . uniqid();
  $codeid = $id + $codemod;   // this is to obfuscate the real ID of the user if we don't want to publish this number

  // Append new signature to array
  $data[] = array("id" => $id,
                  "name" => $name, 
                  "email" => $email,
                  "country" => $country,
                  "zip" => $zip,
                  "permPriv" => $permPriv,
                  "permNews" => $permNews,
                  "permPub" => $permPub,
                  "code" => $code,
                  "confirmed" => "no");

  // Encode to JSON again and write to file
  $allsig = json_encode($data, JSON_PRETTY_PRINT);
  file_put_contents($db, $allsig, LOCK_EX);
  unset($allsig);
  
  // Send email asking for confirmation
  $to       = $email;
  $subject  = "One step left to sign the \"Public Money - Public Code\" letter";
  $message  = "Dear $name, \r\n\r\n" .
              "Thank you for signing the open \"Public Money - Public Code\" letter! \r\n\r\n" .
              "In order to confirm your signature, please visit following link:\r\n" . 
              "$selfurl?action=confirm&id=$codeid&code=$code \r\n\r\n" .
              "If your confirmation succeeds, your signature will appear on the website within the next few hours.";
  $headers  = "From: noreply@fsfe.org \r\n" .
              "Message-ID: <confirmation-$code@fsfe.org> \r\n" .
              "X-Mailer: PHP";

  mail($to, $subject, $message, $headers);
  
  $output .= "Thank you for signing our open letter! <br /><br />";
  $output .= "We just sent an email to your address ($email) for you to confirm your signature.";
  show_page($output, 0);

} else if ($action === "confirm") {
////////////////////
/// CONFIRMATION ///
////////////////////
  
  $id = $confirmid - $codemod;              // substract the obfuscation number from the given ID
  
  if ($id < 0) {                            // $confirmid is less than $codemod
    $output .= "Invalid signature ID.";
    show_page($output, 1);
  }
  
  read_db($db);
  
  if (empty($data[$id])) {                  // there is no array element with this ID
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
      $allsig = json_encode($data, JSON_PRETTY_PRINT);    // TODO: JSON_PRETTY_PRINT could be turned off to make file smaller
      file_put_contents($db, $allsig, LOCK_EX);
      unset($allsig);
      
      $output .= "Your email address has been confirmed. <br /><br />";
      $output .= "Thank you for signing the open letter! Your signature will appear <a href='/signatures/'>in the signature list</a> within the next hours.";
      show_page($output, 0);
      
    } else {
      $output .= "The provided confirmation code is incorrect.";
      show_page($output, 1);
    }
  } else if ($confirmed === "yes") {
    $output .= "This email address is already confirmed. It can take a few hours until your signature appears <a href='/signatures/'>in the signature list</a>.";
    show_page($output, 1);
  } else {
    $output .= "This signature ID does not exist or the confirmation status is broken.";
    show_page($output, 1);
  }
  
} // END confirm


////////////////
// FUNCTIONS ///
////////////////

// Read signatures database (should only be called if really needed)
function read_db($db) {
  global $data;   // declare $data a global variable to access it outside this function
  if (! file_exists($db)) {
    touch($db);
  }
  $file = file_get_contents($db, true);
  $data = json_decode($file, true);
  unset($file);
}

// Read IP database
function read_ips($ipdb) {
  global $ips;   // declare $data a global variable to access it outside this function
  if (! file_exists($ipdb)) {
    touch($ipdb);
  }
  $file = file_get_contents($ipdb, true);
  $ips = json_decode($file, true);
  unset($file);
}

// Replace a given placeholder in a template HTML page with given content
function replace_page($template, $placeholder, $content){
    $vars = array($placeholder=>$content);
    return str_replace(array_keys($vars), $vars, $template);
}

// Show the filled template page, depending on exit code
function show_page($output, $exit) {
  if ($exit === 0) {
    $headline = "Success";
    $notice = "";
  } else if ($exit === 1) {
    $headline = "Error";
    $notice = "<p>This error could have happened because one or more fields contained invalid information. Please try again. If you think that you see this error by mistake, please contact us.</p>";
  } else {
    $headline = "Thank you";
  }
  $template = file_get_contents('../template/index.html', true);
  $page = replace_page($template, ':HEADLINE:', $headline);
  $page = replace_page($page, ':BODY1:', $output);
  $page = replace_page($page, ':BODY2:', $notice);
  echo $page;
  unset($data);
  
  // PHP execution time tracking
  global $timer_start;
  echo "<!-- PHP execution time: " . (microtime(true) - $timer_start)*1000 . " ms -->\n";
  
  exit($exit);
}
?>
