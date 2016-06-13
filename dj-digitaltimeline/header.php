<?php
/**
 * The Header template for the DJ Digital Timeline.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package DJ Digital Timeline
 */
  $thisID = $post->ID;
  $hID = get_field('tl_select', $thisID)[0];

?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html class="ie ie9" <?php language_attributes(); ?>>
<![endif]-->

<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width">

  <meta name="breakpoint" content="mobile" media="(max-width: 719px)">
  <meta name="breakpoint" content="tablet" media="(min-width: 720px) and (max-width: 1199px)">
  <meta name="breakpoint" content="desktop" media="(min-width: 1200px) and (max-width: 1479px)">
  <meta name="breakpoint" content="widescreen" media="(min-width: 1480px)">

  <!-- <meta name="breakpoint" content="widescreen" media="(min-width: 1280px)">
  <meta name="breakpoint" content="retina" media="only screen and (-webkit-min-device-pixel-ratio : 2)"> -->

  <meta name="page.site" content="Dow Jones - Digital Timeline">
  <meta name="page.content.type" content="Marketing">
  <meta name="page.section" content="Customer Resources">
  <meta name="page.region" content="na,us">

  <title><?php echo get_field('title', $hID);?></title>
  <link rel="profile" href="#">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/reset.css" type="text/css">
  <link rel="stylesheet" href="http://fonts.wsj.net/HCo_Whitney/font_HCo_Whitney.css">
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" type="text/css">
  
  <!--[if lt IE 9]>
  <script src="<?php echo get_template_directory_uri(); ?>/scripts/html5.js"></script>
  <![endif]-->
  <?php wp_head(); ?>
</head>
<?php
  $getStartDate = substr(get_field('fy_starts_on', $hID), 0, 4);
  $include_prevQ = get_field('include_prevQ', $hID);
  $include_nextQ = get_field('include_nextQ', $hID);

  $CurY = (int)$getStartDate + 1;
  if ($include_prevQ) $PrevY = $CurY - 1;
  if ($include_nextQ) $NextY = $CurY + 1;
?>
<body id="top" <?php body_class(); ?>>
  <header id="header">
      <h1><a href="<?php echo get_permalink($hID); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php echo get_field('title', $hID);?></a></h1>
      <ul class="util-nav intro">
        <?php if ($PrevY) echo '<li><a href="#"><span>FY' . substr($PrevY, 2, 2) . ' </span>Q4</a></li>'; ?>
        <li><a href="#"><span><?php echo "FY" . substr($CurY, 2, 2) . " "; ?></span>Q1</a></li>
        <li><a href="#"><span><?php echo "FY" . substr($CurY, 2, 2) . " "; ?></span>Q2</a></li>
        <li><a href="#"><span><?php echo "FY" . substr($CurY, 2, 2) . " "; ?></span>Q3</a></li>
        <li><a href="#"><span><?php echo "FY" . substr($CurY, 2, 2) . " "; ?></span>Q4</a></li>
        <?php if ($NextY) echo '<li><a href="#"><span>FY' . substr($NextY, 2, 2) . ' </span>Q1</a></li>'; ?>
        <li class="download"><a href="#">Print/Download</a></li>
        <div class="clearBoth"></div>
      </ul>
  </header>