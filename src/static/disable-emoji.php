<?php

// Based on: https://kinsta.com/knowledgebase/disable-emojis-wordpress/#disable-emojis-code

/**
* Disable the emoji's
*/
function disable_emojis() {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
  add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'disable_emojis');


function disable_emojis_tinymce($plugins) {
  return is_array($plugins) ? array_diff($plugins, array('wpemoji')) : array();
}

function disable_emojis_remove_dns_prefetch($hints, $relation_type) {
  return ($relation_type === 'dns-prefetch') ? array_diff(wp_dependencies_unique_hosts(), $hints) : $hints;
}
