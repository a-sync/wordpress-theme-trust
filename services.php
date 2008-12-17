<?php
/*
Template Name: Services (catlist/catlist)
*/
?>
<?php get_header(); ?>

<script type="text/javascript">
<!--
  var fixed = false;
  var last_fixed = false;
  function show (pid, set)
  {
    var e = document.getElementById('post-' + pid);

    if (set == 'fix')
    {
      if(last_fixed) document.getElementById('post-' + last_fixed).style.visibility = 'hidden';
      fixed = pid;
      last_fixed = pid;

      return false; // ne ugorjon href alapján
    }
    else if (set == true)
    {
      if(last_fixed)
      {
        document.getElementById('post-' + last_fixed).style.visibility = 'hidden';
        fixed = false;
        last_fixed = false;
      }

      e.style.visibility = 'visible';
    }
    else
    {
      if (fixed) fixed = false;
      else
      {
        if(last_fixed)
        {
          document.getElementById('post-' + last_fixed).style.visibility = 'hidden';
          fixed = false;
          last_fixed = false;
        }

        e.style.visibility = 'hidden';
      }
    }
  }
-->
</script>

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

      endwhile; // end the real loop

      // box data setup
      $box1_array = get_box_catcontents($meta['box1_slug'], $meta['box1_title'], $meta['box1_limit']);
      $box2_array = get_box_catcontents($meta['box2_slug'], $meta['box2_title'], $meta['box2_limit']);

      $contents = array_merge($box1_array['contents'], $box2_array['contents']);

      foreach ($contents as $pcontent) {
        echo '<div class="quickpost" id="post-'.$pcontent['id'].'">'.$pcontent['content'].'</div>';
      }
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
  echo $box1_array['titles_html'];
  echo $box2_array['titles_html'];
?>

    </div><!--/content-->

    <div id="footline"></div>


<?php get_footer(); ?>