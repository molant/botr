<header>
  <?php

  // Prints the logo defined in Theme Settings -> Site Identity
  if (has_custom_logo()) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = esc_url(wp_get_attachment_image_src($custom_logo_id , 'full')[0]);

    echo '<img src="'.$logo_url.'" width="205" height="56" alt="'.get_bloginfo('name').'" />';
  }

  ?>
</header>

<main>

  <?php the_content(); ?>

</main>
