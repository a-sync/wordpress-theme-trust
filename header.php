<?php

load_theme_textdomain('trust');//call in air support!

$post_bak = $post; // sosem lehet tudni...
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

    <meta name="generator" content="WordPress <?php //bloginfo('version'); ?>" /> <!-- leave this for stats / or keep it commented for security :-/ -->

    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/print.css" type="text/css" media="print" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php //wp_head(); ?>

  <script type="text/javascript">

  </script>

</head>
<body>


  <div id="container"><!--container-->

    <a name="headline"></a>
<?php
$base_url = 'http://'.array_shift(explode('/', substr(get_bloginfo('url'), 7))); // i wuv u
?>
    <div id="headline"><a href="<?php echo $base_url; ?>/"><?php _e('English', 'trust'); ?></a> &nbsp; <a href="<?php echo $base_url; ?>/hu/"><?php _e('Magyar', 'trust'); ?></a></div>
    
    <div id="menu"><!--menu-->
<?php
  $pages = get_pages('hierarchical=0&sort_column=menu_order&sort_order=desc&meta_key=menu&meta_value=main');

  foreach($pages as $p)
  {
    $style = ($post->ID != $p->ID) ? '' : ' style="color: #cc0000"';

    $title = trim(get_post_meta($p->ID, $key = 'title', $single = true));
    if($title == '') $title = $p->post_title;

    echo '<span><a'.$style.' href="'.get_page_link($p->ID).'">'.$title.'</a></span> ';
  }
?>
    </div><!--/menu-->
    
    <!-- end header -->
