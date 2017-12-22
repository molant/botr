<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  $search_console_code = get_theme_mod('botr_search_console_code');

  if ($search_console_code !== '') {
    echo '<meta name="google-site-verification" content="'.$search_console_code.'" />';
  }
  ?>

  <?php wp_head();?>

</head>

<body>

  <div id="main-content">
