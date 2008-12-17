<?php
/*
Template Name: Contact (form/textbox)
*/
?>
<?php get_header(); ?>

<!-- begin loop -->
	<?php
    if (have_posts()) {
      // meta adatok egy szintű kinyerése
      $meta = get_single_metas($post->ID);

      $picture = ($meta['picture']) ? $meta['picture'] : get_bloginfo('url').'/random/pic.php';
  ?>
    
    <?php if ($meta['maps_link']) { ?>
      <div id="maps">
        <iframe width="842" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $meta['maps_link']; ?>"></iframe>
      </div>
    <?php } else { ?>
      <div id="picture">
        <img src="<?php echo $picture; ?>" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"/>
      </div>
    <?php } ?>

    <div id="content"><!--content-->



  <?php
      while (have_posts()) { the_post(); // start the real loop

        echo '<div class="page"><!--page--><h1>';
          the_title();
        echo '</h1><div>';
          the_content();
        echo '</div></div><!--/page-->';

      } // end the real loop
  ?>

      <div class="formbox"><!--formbox-->

<?php
  // form
  $form_title = $meta['form_title'];
  $form_email = $meta['form_email'];
  $form_subject = ($meta['form_subject']) ? $meta['form_subject'] : 'Contact';
  $form_inputs = $meta['form_inputs'];

  if (!valid_sent_email($form_email)) {
    echo '<!-- invalid email given in form_email meta -->';
  }
  elseif ($form_title == '') {
    echo '<!-- no title given in form_title meta -->';
  }
  else {
    $valid_inputs = convert_meta_form($form_inputs);

    if (1 > count($valid_inputs)) {
      echo '<!-- no valid input codes given in form_inputs meta -->';
    }
    else {
      $_error = '';

      if ($_POST['meta_form_submit'] != '') {
        $mail_data = array();

        foreach ($valid_inputs as $input) {
          $data = $_POST['meta_fid-'.$input['fid']];

          if (trim($data) == '' && $input['req'] == 1) {
            $_error = '<br/><span class="error">'.__('Please fill out all the fields!', 'trust').'</span>';
          }

          $mail_data[$input['fid']] = array($input['name'], $data);
        }

        if ($_error == '') {
          if (send_meta_form_mail($form_email, $mail_data, $form_subject)) {
            $_error = '<br/><span class="success">'.__('Message sent!', 'trust').'</span>';
          }
          else {
            $_error = '<br/><span class="error">'.__('An error occurred while sending your email.', 'trust')
                     .'<br/>'.__('Please try again later or use our email address to contact us.', 'trust').'</span>';
          }
        }
      }

      //echo '<a onclick="return toggleform(\'meta_form-1\');" href="#footline">'.$form_title.'</a>';
      echo '<h2>'.$form_title.'</h2>';
      echo '<form id="meta_form-1" class="meta_form" method="post" action="#footline">'; // form start

      create_meta_form($valid_inputs);

      if ($_error != '') { echo $_error; }

      echo '</form>'; // form end
    }
  }
?>
      </div><!--/formbox-->

  <?php
      // box1 (textbox)
      $box1_title = $meta['box1_title'];
      $box1_text = $meta['box1_text'];

      if ($box1_title != '' || $box1_text != '') {
        echo '<div class="box"><h2>'.$box1_title.'</h2>'
             .'<div>'
             .$box1_text
             .'</div>'
             .'</div>';
      }

    } else {
  ?>
    <div id="picture"><img src="random/pic.php" alt="<?php _e('Trust Communications and Consultancy Kft', 'trust'); ?>"/></div>
    <div id="content"><!--content-->
      <div class="page"><!--page-->
        <h1><?php _e('Error 404', 'trust'); ?></h1>
        <p><?php _e('Nothing here.', 'trust'); ?></p>
      </div><!--/page-->
  <?php } ?>
<!-- end loop -->

    </div><!--/content-->

    <div id="footline"></div>


<?php get_footer(); ?>