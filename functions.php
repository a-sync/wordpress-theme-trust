<?php
function convert_meta_form($form_inputs) {
  $valid_inputs = array();
  $form_inputs_a = explode("\n", $form_inputs);
  

  foreach ($form_inputs_a as $n => $input) {
    $input_a = explode('|', $input);
    $input_type = rtrim(array_shift($input_a), "\t\n\r\0\x0B");
    $input_a[0] = rtrim($input_a[0], "\t\n\r\0\x0B");

    if (strtolower($input_type) == 'text'
     || strtolower($input_type) == 'hidden'
     || strtolower($input_type) == 'textarea'
     || strtolower($input_type) == 'checkbox'
     || strtolower($input_type) == '') {
      $valid_inputs[$n]['fid'] = $n;
      $valid_inputs[$n]['type'] = strtolower($input_type);

      if (substr($input_a[0], -1) == '*') {
        $valid_inputs[$n]['name'] = substr($input_a[0], 0, -1);
        $valid_inputs[$n]['req'] = 1;
      }
      else {
        $valid_inputs[$n]['name'] = $input_a[0];
        $valid_inputs[$n]['req'] = 0;
      }
    }
    elseif (substr(strtolower($input_type), 0, 7) == 'select:') {
      $valid_inputs[$n]['fid'] = $n;
      $valid_inputs[$n]['type'] = 'select';
      $valid_inputs[$n]['name'] = $input_a[0];

      $options = explode(':', substr($input_type, 7));

      foreach ($options as $i => $option) {
        if (trim($option) != '') {
          $valid_inputs[$n]['options'][$i] = htmlspecialchars($option);
        }
      }
    }
  }

  return $valid_inputs;
}

function create_meta_form($valid_inputs, $box_limit = 3) {

  foreach ($valid_inputs as $input) {

    echo '<div class="meta_form_line">'.$input['name'];

    if ($input['type'] == 'text') {
      echo '<br/><input id="meta_fid-'.$input['fid'].'" name="meta_fid-'.$input['fid']
      .'" class="meta_form_'.$input['type'].'" type="'.$input['type'].'"/><br/>';
    }
    elseif ($input['type'] == 'checkbox') {
      echo ' <input id="meta_fid-'.$input['fid'].'" name="meta_fid-'.$input['fid']
      .'" class="meta_form_'.$input['type'].'" type="'.$input['type'].'" value="'.__('Checked', 'trust').'"/><br/>';
    }
    elseif ($input['type'] == 'hidden') {
      echo '<input id="meta_fid-'.$input['fid'].'" name="meta_fid-'.$input['fid']
      .'" class="meta_form_'.$input['type'].'" type="'.$input['type'].'" value="'.$input['name'].'"/><br/>';
    }
    elseif ($input['type'] == 'textarea') {
      echo '<br/><textarea id="meta_fid-'.$input['fid'].'" name="meta_fid-'.$input['fid']
      .'" class="meta_form_'.$input['type'].'" rows="2" cols="28"></textarea><br/>';
    }
    elseif ($input['type'] == 'select') {
      echo ' <select id="meta_fid-'.$input['fid'].'" name="meta_fid-'.$input['fid']
      .'" class="meta_form_'.$input['type'].'">';

      foreach ($input['options'] as $k => $option) {
        echo '<option value="'.$k.'">'.$option.'</option>';
      }

      echo '</select><br/>';
    }

    echo '</div>';
  }

  echo '<input value="'.__('Send', 'trust').'" id="meta_fid-submit" class="meta_form_submit" type="submit" name="meta_form_submit"/>';

  return true;
}

function send_meta_form_mail($cim, $msg_array, $targy = '', $from = '', $html = 0) {
  if(valid_sent_email($cim) && is_array($msg_array) && count($msg_array) > 0) {

    //valós email cím van-e megadva
    $from = mysql_real_escape_string(trim($from));
    if(!valid_sent_email($from)) {
      $from = 'Trust Communications <contact@trustcomms.com>'; //fejlécbe kód injektálást elkerülendő
    }


    //fejléc küldővel, reply mezővel, karakterkódolással
    $fejlec =
       'From: '.$from."\r\n"
      .'Reply-To: '.$from."\r\n"
      .'X-Mailer: PHP/5'."\r\n";//.phpversion()

    $n = '<br />';
    if ($html == 1) {
      $boundary_hash = substr(md5(microtime()), mt_rand(2, 20), 8);

      $fejlec .= 'Content-Type: multipart/alternative; boundary="PHP-alt-'.$boundary_hash.'"'."\r\n";
    }
    elseif ($html == 2) {
      $fejlec .= 'MIME-Version: 1.0'."\r\n"
                 .'Content-Type: text/html; charset='.strtolower(get_bloginfo('charset'))."\r\n"
                 .'Content-Transfer-Encoding: base64'."\r\n";
    }
    else {
      $fejlec .= 'Content-Type: text/plain; charset='.strtolower(get_bloginfo('charset'))."\r\n";
      $n = "\r\n";
    }
    $fejlec .= "\r\n";


    //üzenet
    $uzenet = '';
    foreach ($msg_array as $line) {
      $uzenet .= $line[0].$n.$line[1].$n.$n;
    }
    $uzenet .= "\r\n";


    if ($html == 1) {
      $message_html = $uzenet;
      $message_text = strip_tags(str_replace(array('<br/>', '<br />', '<br>'), "\n", $message_html));

      $uzenet = '--PHP-alt-'.$boundary_hash."\r\n"
      .'Content-Type: text/plain; charset='.strtolower(get_bloginfo('charset'))."\r\n"
      .'Content-Transfer-Encoding: 7bit'."\r\n"
      ."\r\n"
      .$message_text."\r\n"
      ."\r\n"
      .'--PHP-alt-'.$boundary_hash."\r\n"
      .'Content-Type: text/html; charset='.strtolower(get_bloginfo('charset'))."\r\n"
      .'Content-Transfer-Encoding: 7bit'."\r\n"
      ."\r\n"
      .$message_html."\r\n"
      ."\r\n"
      .'--PHP-alt-'.$boundary_hash.'--';
    }
    elseif ($html == 2) {
      $uzenet = base64_encode($uzenet);
    }
    elseif (is_numeric($html) && $html >= 10) {
      $uzenet = wordwrap($uzenet, $html);
    }


    // küldés
    if(!@mail(mysql_real_escape_string($cim), mysql_real_escape_string($targy), $uzenet, $fejlec)) {
        //hiba log
        //if(!file_exists('email.error.log.php')) {
        //  @touch('email.error.log.php');
        //  file_put_contents('email.error.log.php', '< ?php if(!defined("_LOG_")) { die("Failboat!"); } ? >'."\r\n");
        //}
        //@file_put_contents('email.error.log.php', $cim.' (Targy: '.$targy.')<br/><pre>'
        //.$fejlec.'</pre><br/><pre>'.$uzenet.'</pre><br/><br/>', FILE_APPEND);

        return false;
    }
    else { return true; }
  }
  else { return false; }
}

function get_single_metas($post_id = false) {
  $meta = array();

  if (is_numeric($post_id)) {
    $post_meta = get_post_custom($post_id);

    foreach ($post_meta as $name => $value) {
      $meta[$name] = $value[0];
    }
  }

  return $meta;
}

function valid_sent_email($email) {
  return preg_match('/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/', $email);
}

function get_box_catcontents($box1_slug, $box1_title = '', $box1_limit = -1) {
  $return = array();

  if ($box1_slug) {

    if($box1_title == '') {
      $box1_cat = get_category_by_slug($box1_slug);
      $box1_title = $box1_cat->cat_name;
    }

    $box1_limit = ($box1_limit) ? $box1_limit : -1;
    $box1_list = get_posts('numberposts='.$box1_limit.'&order=DESC&orderby=date&category_name='.$box1_slug);

    $return['titles_html'] .= '<div class="box">'
                           .'<h2>'.$box1_title.'</h2>'
                           .'<div>';
    $nl = '';
    foreach ($box1_list as $post) {
      setup_postdata($post);

      $return['contents'][] = array('id' => $post->ID, 'title' => $post->post_title, 'content' => get_the_content());

      $return['titles_html'] .= $nl.'<a onclick="return show('.$post->ID.', \'fix\');" onmouseover="show('.$post->ID.', true);" onmouseout="show('.$post->ID.', false);" href="#footline">'
                             .htmlspecialchars($post->post_title)
                             .' </a>';

      $nl = '<br/>';
    }
    $return['titles_html'] .= '</div>'
                           .'</div>';
  }

  //echo '<pre>'.print_r($return, true).'</pre>';// debug
  return $return;
}


?>