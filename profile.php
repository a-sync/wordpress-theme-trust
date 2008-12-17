<?php
/*
Template Name: Profile (textbox/same parent pages)
*/
?>
<?php get_header(); ?>

<!-- begin loop -->
	<?php
    if (have_posts()) :
      // meta adatok egy szintű kinyerése
      $meta = get_single_metas($post->ID);

      $picture = ($meta['picture']) ? $meta['picture'] : get_bloginfo('url').'/random/pic.php';
  ?>
    <div id="picture"><img src="<?php echo $picture; ?>" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"/></div>

    <div id="content"><!--content-->
      <div class="page"><!--page-->

  <?php
      while (have_posts()) : the_post(); // start the real loop

        echo '<h1>';
          the_title();
        echo '</h1>';
        the_content();
        echo '<br/>';

      endwhile; // end the real loop
  ?>
      </div><!--/page-->

  <?php
    else:
  ?>
    <div id="picture"><img src="random/pic.php" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"/></div>
    <div id="content"><!--content-->
      <div class="page"><!--page-->
        <h1><?php _e('Error 404', 'trust'); ?></h1>
        <p><?php _e('Nothing here.', 'trust'); ?></p>
      </div><!--/page-->
  <?php endif; ?>
<!-- end loop -->

      
<?php
  // box1 (textbox)
  $box1_title = $meta['box1_title'];
  $box1_text = $meta['box1_text'];

  if ($box1_title != '' || $box1_text != '') {
    echo '<div class="box"><h2>'.$box1_title.'</h2>'
         .'<div>'
         .nl2br($box1_text)
         .'</div>'
         .'</div>';
  }


  // box2 (subpages list)
  $box2_title = ($meta['box2_title']) ? $meta['box2_title'] : '&nbsp;';

  $pages = get_pages('hierarchical=0&sort_column=menu_order&sort_order=desc&child_of='.$post->post_parent);

  if (0 < count($pages)) {
    echo '<div class="box"><h2>'.$box2_title.'</h2>'
         .'<div>';

    $post_id = $post->ID;
    foreach($pages as $post)
    {
      setup_postdata($post);
      $style = ($post_id != $post->ID) ? '' : ' style="color: #cc0000"';

      $title = trim(get_post_meta($post->ID, $key = 'title', $single = true));

      echo '<a'.$style.' href="';
          the_permalink();
          echo '">';
        if ($title == '') { the_title(); }
        else { echo $title; }
      echo '</a><br/>';
    }
    echo '</div>'
         .'</div>';
  }

?>


    </div><!--/content-->


    <div id="footline"></div>


<?php get_footer(); ?>