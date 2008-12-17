<?php
/*
Template Name: News (catlist)
*/
?>

<?php get_header(); ?>

<script type="text/javascript">
<!--
  function paperpls()
  {
    window.print();
    return false;
  }

  function toggleform(id, link)
  {
    var e = document.getElementById(id);

    e.style.visibility = (e.style.visibility == 'hidden') ? 'visible' : 'hidden';

    return false; // nincs ugrás
  }
-->
</script>

  <?php
    // meta adatok egy szintű kinyerése
    $meta = get_single_metas($post->ID);

    $box2_title = ($meta['box2_title'] == '') ? __('All press releases', 'trust') : $meta['box2_title'];

    // kategória infók kigyűjtése
    setup_postdata($post);
    $post_categories = get_the_category();
    
    $cat_ids = array();
    foreach ($post_categories as $cat_id) { $cat_ids[$cat_id->cat_ID] = $cat_id->cat_ID; }
    
    $current_post_id = $post->ID;

    // box1 (category list)
    $box1_slug = $meta['box1_slug'];
    $box1_cat = $post->post_category;
    $box1_title = ($meta['box1_title'] == '') ? '&nbsp;' : $meta['box1_title'];
    $box1_date = ($meta['box1_date'] == '') ? __('d/m/y', 'trust') : $meta['box1_date'];
    $box1_list = false;
    $art_data = array();

    if (is_search()) {
      if (have_posts()) {
        //$box2_title = 'Search results';
        if (is_numeric($_GET['c'])) { $search_cats = explode(',', str_replace(' ', '', mysql_real_escape_string($_GET['c']))); }
        else { $search_cats = false; }

        $tmp_post = $post;
        while (have_posts()) {
          the_post();
          $cats = get_the_category();

          $i = 0;
          if ($search_cats != false) {
            foreach ($cats as $cat) {
              if (in_array($cat->cat_ID, $search_cats)) { $i++; }
            }
          }
          
          if ($i > 0) {
            if ($post->post_type == 'post') {
              $box1_list[] = $post;
            }
          }

        }
        $post = $tmp_post;
        unset($tmp_post);
      }
      else { $box1_list = false; }
    }
    elseif ($box1_list == false && $box1_slug) {
      $box1_list = get_posts('numberposts=-1&order=DESC&orderby=date&category_name='.$box1_slug);
    }
    else {
      $box1_slug = '';
      $sep = '';
      foreach ($post_categories as $cat) { 
          $box1_slug .= $sep.$cat->cat_ID;
          $sep = ',';
      }

      $box1_list = get_posts('numberposts=-1&order=DESC&orderby=date&category='.$box1_slug);
    }

  ?>

    <div id="news-content"><!--news-content-->

      <div class="news_boxes"><!--news_boxes-->
      <!-- lets do some looping -->
        <div class="news_box_left"><!--news_box_left-->
          <div class="news_box_left_title"><?php echo $box1_title; ?></div>
          <div class="news_box_left_content"><!--news_box_left_content-->

<?php
  if (!is_search() && !is_page() && have_posts()) {
    while (have_posts()) {
      the_post();

      $post_categories = get_the_category();
      foreach ($post_categories as $cat_id) { $cat_ids[$cat_id->cat_ID] = $cat_id->cat_ID; }

      $art_data['url'] = get_permalink();
      $art_data['title'] = the_title_attribute('echo=0');
      $art_data['content'] = $post->post_content;

      echo '<h1 class="news_content_title">';
        the_title();
      echo '</h1>';
      the_content();
    }
  }
  elseif ($box1_list) {//nem search (!is_search)
    $post = $box1_list[0];
    setup_postdata($post);

    $art_data['url'] = get_permalink();
    $art_data['title'] = the_title_attribute('echo=0');
    $art_data['content'] = $post->post_content;

    $current_post_id = $post->ID;

    echo '<h1 class="news_content_title">';
      the_title();
    echo '</h1>';
    the_content();
  }
  else {
    echo '<h3 class="news_content_title">'.__('No posts found', 'trust').'</h3>';
  }
  
?>

          </div><!--/news_box_left_content-->
        </div><!--/news_box_left-->
      <!-- lets do some more looping -->
        <div class="news_box_right"><!--news_box_right-->
          <div class="news_box_right_title"><?php echo $box2_title; ?></div>
          <div class="news_box_right_content"><!--news_box_right_content-->

<?php
  if ($box1_list) {//keresés esetén listázni a találatokat
    $the_date = '';

    foreach ($box1_list as $post) {
      setup_postdata($post);

      $post_categories = get_the_category();
      foreach ($post_categories as $cat_id) { $cat_ids[$cat_id->cat_ID] = $cat_id->cat_ID; }

      $style = ($current_post_id != $post->ID) ? '' : ' style="color: #cc0000"';

      echo ' <a'.$style.' href="';
          the_permalink();
          echo '">';
        echo get_the_time($box1_date).' ';
        the_title();
      echo '</a>';
/*
      if (is_search()) {
        $cats = get_the_category();
        $cat_names = array();

        foreach ($cats as $cat) {
          $cat_names[] = $cat->cat_name;
        }

        if (0 < count($cat_names)) {
          echo ' <i>('.implode(', ', $cat_names).')</i>';
        }
      }
*/
      echo '<br/><br/>';
    }
  }
?>

          </div><!--/news_box_right_content-->
        </div><!--/news_box_right-->
      <!-- lets end the looping -->
      </div><!--news_boxes-->

      <div class="news_page">
        <div class="news_page_download">

          <a title="<?php _e('Print this article!', 'trust'); ?>" href="#" onclick="return paperpls();"><?php _e('Print', 'trust'); ?></a> &nbsp; <a title="<?php _e('Send this article to a friend!', 'trust'); ?>" onclick="return toggleform('meta_form-0', this);" href="#footline"><?php _e('Mail to a friend', 'trust'); ?></a>
        </div>
        <div class="news_page_search">
        <?php
          //ugyanezen a lapon keressen
          echo '<!--bloginfo(url) '; bloginfo('url'); echo '-->';
        ?>
          <form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
            <input type="hidden" name="c" value="<?php echo htmlspecialchars(implode(',', $cat_ids)); ?>"/>
            <label for="s"><?php _e('Search:', 'trust'); ?></label> <input id="s" type="text" name="s" value="<?php the_search_query(); ?>"/>
            <input title="<?php _e('Search!', 'trust'); ?>" id="search_submit" type="submit" value="<?php _e('go', 'trust'); ?>"/> 
          </form>
        </div>

  <?php
    $_error = '';

    if ($art_data['url'] != '' && $art_data['title'] != '') {
      if ($_POST['meta_form_submit'] != '') {
        $mail_data = array();

        if (valid_sent_email($_POST['meta_fid-0'])) {
          $form_email = mysql_real_escape_string($_POST['meta_fid-0']);
        }
        else { $_error .= '<br/><span class="error">'.__('The email of your friend is invalid!', 'trust').'</span>'; }

        $mail_data[] = array(' ', __('A friend of yours sent this link for you. Please check it out.', 'trust'));
        $mail_data[] = array(__('URL: ', 'trust'), htmlspecialchars($art_data['url']));

/*
        if ($_POST['meta_fid-999'] != '') {
          $mail_data[] = array(__('Article: ', 'trust'), '<br/>'.$art_data['content']);
        }
*/

        $form_subject = $art_data['title'];


        $mail_data[$input['fid']] = array($input['name'], $data);

        if ($_error == '') {
          if(send_meta_form_mail($form_email, $mail_data, $form_subject, '', 0)) {
            $_error = '<br/><span class="success">'.__('Message sent!', 'trust').'</span>';
          }
          else {
            $_error = '<br/><span class="error">'.__('An error occurred while sending your email.', 'trust')
                     .'<br/>'.__('Please try again later.', 'trust').'</span>';
          }
        }
      }
    }

    echo '<form'. (($_error != '') ? '' : ' style="visibility: hidden;"') .' id="meta_form-0" class="meta_form" method="post" action="#footline">'; // form start

    if ($art_data['url'] != '' && $art_data['title'] != '') {
      echo __('Friends email:', 'trust').' <input type="text" name="meta_fid-0" id="meta_fid-0" class="meta_form_text"/> <input class="meta_form_submit" type="submit" name="meta_form_submit" value="'.__('Send', 'trust').'"/>';
    }
    else { $_error .= '<br/><span class="error">'.__('No article to send, sorry!', 'trust').'</span>'; }

    if($_error != '') { echo $_error; }

    echo '</form>'; // form end

  ?>

        <div class="news_page_think"><?php _e('Please consider your environmental responsibility - think before you print!', 'trust'); ?></div>
      </div>

    </div><!--/news-content-->


    <div id="footline"></div>


<?php get_footer(); ?>