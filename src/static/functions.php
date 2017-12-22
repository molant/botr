<?php

define('BOTR_DYNAMIC_CSS', FALSE);

require_once('MailForm.php');
require_once('AltcoinPrice.php');

/**
* <title>
*/
function botr_theme_support() { add_theme_support('title-tag'); }
add_action('after_setup_theme', 'botr_theme_support');

/**
* Custom headers
*/
function botr_custom_headers($headers) {
  if (!is_admin()) { $headers['x-ua-compatible'] = 'IE=edge'; }
  $headers['strict-transport-security'] = 'max-age=31536000';

  return $headers;
}
add_filter('wp_headers', 'botr_custom_headers');

/**
* Setup logo
*/
function botr_logo_setup() {
  $defaults = array(
    'height'      => 100,
    'width'       => 400,
    'flex-height' => true,
    'flex-width'  => true,
    'header-text' => array('site-title', 'site-description'),
  );
  add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'botr_logo_setup');





/**
* Defer script tags
*/
function botr_add_defer_attribute($tag, $handle) {
  $t = str_replace(' src', ' defer="defer" src', $tag);
  $t = str_replace("type='text/javascript'", "type='application/javascript'", $t);
  $t = str_replace('type="text/javascript"', 'type="application/javascript"', $t);
  return $t;
}

add_filter('script_loader_tag', 'botr_add_defer_attribute', 10, 2);




/**
* Enqueue scripts and styles
*/
function botr_enqueue_scripts_and_styles() {
  if (!BOTR_DYNAMIC_CSS) {
    wp_enqueue_style('botr_css', get_stylesheet_uri(), array(), null);
  }

  wp_enqueue_script('botr_js', get_template_directory_uri().'/index.js', array(), null);
}

add_action('wp_enqueue_scripts', 'botr_enqueue_scripts_and_styles');





/**
* Dynamic load of CSS
*/
function botr_the_style() {
  echo '<noscript id="botr_css-css"><link rel="stylesheet" href="'.get_stylesheet_uri().'" type="text/css" media="all" /></noscript>';
  ?>
  <script>
  var loadDeferredStyles = function() {
    var addStylesNode = document.getElementById("botr_css-css");
    var replacement = document.createElement("div");
    replacement.innerHTML = addStylesNode.textContent;
    document.body.appendChild(replacement)
    addStylesNode.parentElement.removeChild(addStylesNode);
  };
  var raf = requestAnimationFrame || mozRequestAnimationFrame ||
  webkitRequestAnimationFrame || msRequestAnimationFrame;
  if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
  else window.addEventListener('load', loadDeferredStyles);
  </script>
  <?php
}

if (BOTR_DYNAMIC_CSS) {
  add_action('wp_footer', 'botr_the_style');
}





/**
* Settings
*/
function botr_customize_register($wp_customize){
  // 1. Custom section
  $wp_customize->add_section('botr_settings_section', array(
    'title' => 'Botr.io',
    'description' => 'Custom options',
    'priority' => 160,
    'capability' => 'edit_theme_options',
  ));

  // 2. Google Analytics Code
  $wp_customize->add_setting('botr_analytics_code', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'default' => '',
    'sanitize_callback' => 'sanitize_text_field'
  ));

  $wp_customize->add_control('botr_analytics_code', array(
    'type' => 'text',
    'priority' => 10,
    'section' => 'botr_settings_section',
    'label' => 'Tracking ID',
    'description' => 'Your Google Analytics Tracking ID.',
    'input_attrs' => array(
      'style' => 'border: 1px solid #900',
      'placeholder' => 'UA-XXXXXXXXX-X',
    )
  ));

  // 3. Send grid API
  $wp_customize->add_setting('botr_search_console_code', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'default' => '',
    'sanitize_callback' => 'sanitize_text_field'
  ));

  $wp_customize->add_control('botr_search_console_code', array(
    'type' => 'text',
    'priority' => 10,
    'section' => 'botr_settings_section',
    'label' => 'Search Console Code',
    'description' => 'Verification code for Google Search Console.',
    'input_attrs' => array(
      'style' => 'border: 1px solid #900',
      'placeholder' => '',
    )
  ));

  // 4. Search Console Code
  $wp_customize->add_setting('botr_send_grid_key', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    'default' => '',
    'sanitize_callback' => 'sanitize_text_field'
  ));

  $wp_customize->add_control('botr_send_grid_key', array(
    'type' => 'text',
    'priority' => 10,
    'section' => 'botr_settings_section',
    'label' => 'SendGrid API KEY',
    'description' => 'Your SendGrid API Key.',
    'input_attrs' => array(
      'style' => 'border: 1px solid #900',
      'placeholder' => '',
    )
  ));

}
add_action('customize_register', 'botr_customize_register');





/**
* Discard embed and emoji support
*/
function botr_deregister_defaults(){
  wp_deregister_script('wp-embed');
  wp_deregister_script('wp-emoji-release');
}
add_action('wp_footer', 'botr_deregister_defaults');
require('disable-emoji.php');





/**
* Overriding default site icon
*/
function botr_site_icon() {
  if (!has_site_icon() && !is_customize_preview()) { return; }

  $meta_tags = array();
  $icon_32 = get_site_icon_url(32);

  if (empty($icon_32)&& is_customize_preview()){
    $icon_32 = '/favicon.ico'; // Serve default favicon URL in customizer so element can be updated for preview.
  }

  if ($icon_32){
    $meta_tags[] = sprintf('<link rel="icon" href="%s" sizes="32x32" />', esc_url($icon_32));
  }

  $icon_192 = get_site_icon_url(192 );
  if ($icon_192){
    $meta_tags[] = sprintf('<link rel="icon" href="%s" sizes="192x192" />', esc_url($icon_192));
  }

  $icon_180 = get_site_icon_url(180 );
  if ($icon_180){
    $meta_tags[] = sprintf('<link rel="apple-touch-icon" href="%s" />', esc_url($icon_180));
  }

  $icon_270 = get_site_icon_url(270 );
  if ($icon_270){
    $meta_tags[] = sprintf('<meta name="msapplication-TileImage" content="%s" />', esc_url($icon_270));
  }

  $meta_tags = apply_filters('site_icon_meta_tags', $meta_tags );
  $meta_tags = array_filter($meta_tags );

  foreach ($meta_tags as $meta_tag){
    echo "$meta_tag\n";
  }
}
remove_action ('wp_head', 'wp_site_icon', 99);
add_action ('wp_head', 'botr_site_icon', 99);
