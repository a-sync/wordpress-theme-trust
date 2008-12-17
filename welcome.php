<?php
/*
Template Name: Home (catlist/linklist)
*/
?>
<?php get_header(); ?>

<!-- begin loop -->
	<?php
    if (have_posts()) :
      // meta adatok egy szintű kinyerése
      $meta = get_single_metas($post->ID);

      $picture = ($meta['picture']) ? $meta['picture'] : get_bloginfo('url').'/random/pic.php';

      if ($meta['holiday'] != '') {
        $holiday_text = explode("\n", $meta['holiday']);

        $holiday_time = explode('-', trim(array_shift($holiday_text)));
        $holiday_time['from'] = explode('.', $holiday_time[0]);
        $holiday_time['to'] = explode('.', $holiday_time[1]);
        $holiday_time['from_unix'] = mktime(0, 0, 0, $holiday_time['from'][1], $holiday_time['from'][0], $holiday_time['from'][2]);
        $holiday_time['to_unix'] = mktime(23, 59, 59, $holiday_time['to'][1], $holiday_time['to'][0], $holiday_time['to'][2]);

        $holiday_pic = trim(array_shift($holiday_text));
        $holiday_text = implode("\n", $holiday_text);
        
        $time_now = time();

        $holiday_tags = '';
        $holiday_textbox = '';
//die('<pre>'.print_r(array($time_now, $holiday_time, $holiday_pic, $holiday_text), true).'</pre>');
        if ($holiday_time['from_unix'] < $time_now && $time_now < $holiday_time['to_unix'] && $holiday_pic != '') {
          $holiday_textbox = '<span id="holiday_textbox" style="visibility: hidden; color: #cc0000;">'.$holiday_text.'</span>';
          $holiday_tags = ' onmouseover="holiday_flip(1);" onmouseout="holiday_flip(0);"';
          ?>

<script type="text/javascript">
<!--
  holiday_pic = new Image();
  holiday_pic.src = '<?php echo $holiday_pic; ?>';

  function holiday_flip(a)
  {
    if (a == 1)
    {
      document.getElementById('picture_img').src = '<?php echo $holiday_pic; ?>';
      document.getElementById('holiday_textbox').style.visibility = 'visible';
    }
    else
    {
      document.getElementById('picture_img').src = '<?php echo $picture; ?>';
      document.getElementById('holiday_textbox').style.visibility = 'hidden';
    }
  }
-->
</script>

          <?php
        }
      }
  ?>
    <div id="picture"><img id="picture_img" src="<?php echo $picture; ?>" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"<?php echo $holiday_tags; ?>/></div>

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

      echo $holiday_textbox;
  ?>
      </div><!--/page-->

  <?php
    else:
  ?>
    <div id="picture"><img src="random/pic.php" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"/></div>
    <div id="content"><!--content-->
      <div class="page"><!--page-->
        <h1><?php _e('Error 404', 'trust'); ?></h1>
        <p><?php _e('Print this article!', 'trust'); ?></p>
      </div><!--/page-->
  <?php endif; ?>
<!-- end loop -->

      
<?php

  // box1 (category list)
  $box1_slug = $meta['box1_slug'];

  if ($box1_slug) {
    $box1_title = $meta['box1_title'];
    if($box1_title == '') {
      //$box1_cat = get_the_category('category_nicename='.$box1_slug);
      $box1_cat = get_category_by_slug($box1_slug);
      $box1_title = $box1_cat->cat_name;
    }
    $box1_limit = ($meta['box1_limit']) ? $meta['box1_limit'] : -1;
    $box1_list = get_posts('numberposts='.$box1_limit.'&order=DESC&orderby=date&category_name='.$box1_slug);

    echo '<div class="box"><h2>'.$box1_title.'</h2>'
         .'<div>';
    foreach ($box1_list as $post) {
      setup_postdata($post);

      echo '<a href="';
          the_permalink();
          echo '">';
        the_title();
      echo '</a><br/>';
    }
    echo '</div>'
         .'</div>';
  }


  // box2 (bookmark category list)
  $box2_slug = $meta['box2_slug'];

  if ($box2_slug) {
    $box2_title = $meta['box2_title'];
    if($box2_title == '') {
      $wpdb->hide_errors();
      $box2_title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `$wpdb->terms` WHERE `slug` = '$box2_slug'"));
      $wpdb->flush();
    }
    $box2_limit = ($meta['box2_limit']) ? $meta['box2_limit'] : -1;
    $box2_list = get_bookmarks('limit='.$box2_limit.'&orderby=rating&order=ASC&category_name='.$box2_slug);

    echo '<div class="box"><h2>'.$box2_title.'</h2>';
    foreach ($box2_list as $link) {
      $target = ($link->link_target == '') ? '' : 'target="'.$link->link_target.'"';

      echo '<div class="boxlink">';
      if($link->link_image != '') {
        echo '<a href="'.$link->link_url.'" title="'.$link->link_name.'"'.$target.'>'
            .'<img src="'.$link->link_image.'" alt="'.$link->link_name.': "/>'
            .$link->link_description
            .'</a>';
      }
      else {
        echo $link->link_name.': <a href="'.$link->link_url.'"'.$target.'>'
            .$link->link_description
            .'</a>';
      }
      echo '</div>';
    }
    echo '</div>';
  }


?>


    </div><!--/content-->


    <div id="footline"></div>


<?php get_footer(); ?>