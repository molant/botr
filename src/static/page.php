<?php

// Acts like index.php
// This theme is only for pages
get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();

    // Loads and prints content-page.php
    get_template_part('content-page', get_post_format());

  }
}

get_footer();
