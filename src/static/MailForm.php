<?php

/**
* Shortcode that actually prints the MailForm
*/
function mailform_print() {
  $endpoint = esc_url(home_url('/wp-admin/admin-ajax.php'));

  return PHP_EOL.'<form class="mail-form" method="post" enctype="application/x-www-form-urlencoded" data-action="'.$endpoint.'" action="'.$endpoint.'">'.PHP_EOL.
  ' <label><span>Email Address:</span>'.PHP_EOL.
  '    <input type="email" name="email" placeholder="Email Address" />'.PHP_EOL.
  ' </label>'.PHP_EOL.
  '<input type="hidden" name="action" value="mailform" />'.PHP_EOL.
  '<input type="hidden" name="nojs" value="true" />'.PHP_EOL.
  '    <div class="mail-form--submit"><input type="submit" value="Let me know!" /></div>'.PHP_EOL.
  '  </form>'.PHP_EOL;
}

add_shortcode('mailform', 'mailform_print');



/**
* Process a MailForm submission
* Returns true in case of success, a string containing the error otherwise
*/
function mailform_process() {
  // 1.Check that the email field was sent, or return error 'NO_EMAIL'
  if (!isset($_POST['email'])) { return 'MALFORMED_NOEMAIL'; }

  // 2. Sanitize the email field to avoid injections
  $email = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

  // 3. Score the email address and return in case of error
  $checked = mailform_check($email);
  if (is_string($checked)) { return $checked; }

  // 4. Send the address to SendGrid
  return mailform_send_email($email, $checked);
}

/**
* AJAX callback, also works when JavaScript is disabled
*/
function mailform_ajax() {
  $result = mailform_process();

  // 1. There was an error but probably bad typing -> return an error
  if (is_string($result) && substr($result, 0, 9) === 'MALFORMED') {
    if (isset($_POST['nojs'])) {
      header("Location: ".esc_url(home_url())."?mailform=error");
      echo 'Redirection...';
      exit();
    } else {
      return wp_send_json_error('MALFORMED');
    }
  }

  // 2. Succes or conscious bad email address
  if (isset($_POST['nojs'])) {
    header("Location: ".esc_url(home_url())."?mailform=success");
    echo 'Redirection...';
    exit();
  }

  return wp_send_json_success($result);
}

// Register ajax endpoint
add_action('wp_ajax_mailform', 'mailform_ajax');
add_action('wp_ajax_nopriv_mailform', 'mailform_ajax');









/**
* Scores an email address
*/
function mailform_check($email) {
  // 1. Check the address is a string or return
  if (!is_string($email)) { return 'MALFORMED'; }

  // 2. Check the address is of form something@domain, return otherwise
  $parts = explode('@', $email, 2);

  if (count($parts) !== 2) { return 'MALFORMED'; }

  $local = $parts[0];
  $domain = $parts[1];

  // 3. Check the address parts are not too long, return otherwise
  // Those are the maximum values for an email address
  // Also avoid potential Regex DOS
  if (strlen($local) > 64 || strlen($domain) > 255) { return 'MALFORMED'; }

  // 4. Validates the email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { return 'MALFORMED'; }


  $score = 0;
  $mx_records = array();
  $mx_weights = array();

  // 5. Check the domain
  while (strpos($domain, '.') !== false) {
    array_push($to_ret, $domain);

    // 5.1 Try to find a DNS MX record
    if (getmxrr($domain.'.', $mx_records, $mx_weights)) {
      $score = 1;
      break;
    }

    $subdomains = explode('.', $domain, 2);

    if (count($subdomains) === 2) {
      $domain = $subdomains[1];
    } else { break; }
  }

  if ($score <= 0) { return 'NO_MX'; }

  // 6. SMTP Validation
  // Usually won't work localy, ISPs block the SMTP ports
  // At this point, score = 1
  $mxs = array();

  for ($i = 0; $i < count($mx_records); $i++) { $mxs[$mx_records[$i]] = $mx_weights[$i]; }

  asort($mxs);

  $errno = 0;
  $errstr = '';
  $sock = false;

  $context = stream_context_create([
    'ssl' => [
      'verify_peer' => false,
      'allow_self_signed' => true,
    ],
  ]);

  foreach ($mxs as $host => $weight) {
    if ($sock = fsockopen($host, 587, $errno, $errstr, 60)) {
      stream_set_timeout($sock, 60);
      break;
    }
  }

  // 6.1 If unable to connect to MX Server, return (score = 0)
  if (!$sock) { return 'UNABLE_OPEN_SOCKET'; }


  $matches = null;
  $reply = fread($sock, 2082);
  preg_match('/^([0-9]{3}) /ims', $reply, $matches);
  $code = isset($matches[1]) ? $matches[1] : '';

  // 6.2 If unable to connect to MX Server, return (score = 0)
  if ($code != '220') { return 'UNABLE_TO_CONNECT: '.$reply; }

  mailform_send($sock, "HELO $domain");
  mailform_send($sock, "MAIL FROM: <romain@botr.io>");
  $reply = mailform_send($sock, "RCPT TO: <$email>");

  preg_match('/^([0-9]{3}) /ims', $reply, $matches);
  $code = isset($matches[1]) ? $matches[1] : '';

  if ($code === '250') {
    // 6.3 Address found, score = 9 (8 + 1)
    $score = 9;
  } elseif ($code == '451' || $code == '452') {
    // 6.4 Server may be catch all, score = 5 (4 + 1)
    $score = 5;
  } else {
    // 6.5 Address does not exist for sure (score = 0)
    return 'EMAIL_DOES_NOT_EXIST';
  }

  mailform_send($sock, "RSET");
  mailform_send($sock, "quit");

  fclose($sock);

  return $score;
}










/**
* Actually send the email address to SendGrid
*
* @param String $email The email address to set
* @param Int $score The computed score
*/
function mailform_send_email($email, $score) {
  // 1. Retrieve the API Key
  $SENDGRID_API_KEY = get_theme_mod('botr_send_grid_key');

  // 2. Return if no API Key set
  if ($SENDGRID_API_KEY === "" || $SENDGRID_API_KEY === false) { return 'NO_API_KEY'; }

  $send_data = array(array("email" => $email, "score" => $score));

  // 3. Open connection
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.sendgrid.com/v3/contactdb/recipients",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($send_data),
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer ".$SENDGRID_API_KEY,
      "content-type: application/json"
    ),
  ));

  // 4. Send data and close connection
  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $data = array();

  // 5. Look for errors and return
  if ($err) {
    $data['success'] = false;
    $data['err'] = $err;
    return $data;
  }

  $response = json_decode($response);

  if (isset($response->errors) && count($response->errors) > 0 ) {
    return $response->errors;
  }

  return true;
}


/**
* Utility function println() then read()
*
* @param $sock The socket to use
* @param $msg The message to println
*/
function mailform_send($sock, $msg) {
  fwrite($sock, $msg."\r\n");
  return fread($sock, 2082);
}
