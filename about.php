<?php
/*
Template Name: About (linklist/subpages/textbox)
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

  // box1 (bookmark category list)
  $box1_slug = $meta['box1_slug'];

  if ($box1_slug) {
    $box1_title = $meta['box1_title'];
    if($box1_title == '') {
      $wpdb->hide_errors();
      $box1_title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `$wpdb->terms` WHERE `slug` = '$box1_slug'"));
      $wpdb->flush();
    }
    $box1_limit = ($meta['box1_limit']) ? $meta['box1_limit'] : -1;
    $box1_list = get_bookmarks('limit='.$box1_limit.'&orderby=rating&order=ASC&category_name='.$box1_slug);

    echo '<div class="box"><h2>'.$box1_title.'</h2>';
    foreach ($box1_list as $link) {
      $href = ($link->link_url == 'http://') ? '' : ' href="'.$link->link_url.'"';

      echo '<div class="boxlink">';


      if ($href != '') { echo '<a'.$href.' title="'.$link->link_description.'" target="'.$link->link_target.'">'; }

      if ($link->link_image != '') {
        echo '<img src="'.$link->link_image.'" alt="'.$link->link_name.'"/>'.$link->link_name;
      }
      else {
        echo $link->link_name;
      }

      if ($href != '') { echo '</a>'; }


      echo '</div>';
    }
    echo '</div>';
  }


  echo '<div class="boxnest">'; // right box frame start


  // box2 (subpages list)
  $box2_title = ($meta['box2_title']) ? $meta['box2_title'] : '&nbsp;';

  $pages = get_pages('hierarchical=0&sort_column=menu_order&sort_order=desc&child_of='.$post->ID);

  if (0 < count($pages)) {
    echo '<div class="box"><h2>'.$box2_title.'</h2>'
         .'<div>';
    foreach($pages as $post)
    {
      setup_postdata($post);

      $title = trim(get_post_meta($post->ID, $key = 'title', $single = true));

      echo '<a href="';
          the_permalink();
          echo '">';
        if ($title == '') { the_title(); }
        else { echo $title; }
      echo '</a><br/>';
    }
    echo '</div>'
         .'</div>';
  }

  // box3 (textbox)
  $box3_title = $meta['box3_title'];
  $box3_text = $meta['box3_text'];

  if ($box3_title != '' || $box3_text != '') {
    echo '<div class="box"><h3>'.$box3_title.'</h3>'
         .'<div>'
         .nl2br($box3_text)
         .'</div>'
         .'</div>';
  }


  echo '</div>'; // right box frame end
?>


    </div><!--/content-->


    <div id="footline"> </div>


<?php get_footer(); ?>