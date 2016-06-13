<?php
/**
 * The template for displaying home pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package DJ Digital Timeline
 */
/*
	$id = get_page_by_title( 'FY16', OBJECT, 'timeline' )->ID;
	$term = get_field('event_category', $id) -> name;

	// echo 'JS-ID:' . $term;

	$event_objects = get_posts(array(
		'post_type' => 'event',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		// 'sort_column' => 'event_date',
		// 'sort_order' => 'desc',
		'meta_key' => 'event_date',  
		'orderby' => 'meta_value',
		'order' => 'asc',
		'tax_query' => array(
		    array(
		    	'taxonomy' => 'category',
		        'field' => 'slug',
		        'terms' => $term
		    )
		)
	));

	$fy_start = get_field('fy_starts_on', $id);
	$fy_start_m = ltrim(substr($fy_start, 4, 2), "0") - 1;

	$fy_tmp_y = substr($fy_start, 0, 4) + 1;
	$fy_tmp_m = substr($fy_start, 4, 2);
	$fy_tmp_d = substr($fy_start, 6, 2);

	$fy_end = $fy_tmp_y . $fy_tmp_m . $fy_tmp_d;

	$pre_m = array();
    $post_m = array();
    $m_order = array();
	$m_sort_object = array();

    if ($event_objects):
    	for ($i = 0; $i < 12; $i ++):
    		$dd = sprintf("%02d", $i+1);
    		if ($i < $fy_start_m) array_push($post_m, $dd);
    		else array_push($pre_m, $dd);
    	endfor;

    	foreach ($pre_m as $j):
    		array_push($m_order, $j);
    	endforeach;

    	foreach ($post_m as $k):
    		array_push($m_order, $k);
    	endforeach;

    	foreach ($m_order as $l):

    		$temp_object = array();

    		foreach ($event_objects as $m):
    			setup_postdata($m);

    			$data = get_field('event_date', $m->ID);
    			if ($data >= $fy_start && $data < $fy_end)
    			{
    				$tempM = substr($data, 4, 2);
    				if ($tempM == $l) array_push($temp_object, $m);
    			}

    		endforeach;

    		array_push($m_sort_object, $temp_object);
    		wp_reset_postdata();

    	endforeach;

  //   	foreach ($m_sort_object as $g):
		// 	setup_postdata($g);
			
		// 	echo 'logging:' . get_field('event_display', $g[0]->ID) . ', ';

		// endforeach;
  //   	wp_reset_postdata();
  //   	
		// foreach($event_objects as $event):	 
		// 	setup_postdata($event);           	
		// 	echo get_field('event_display', $event->ID);
		// 	$type = get_field('event_type', $event->ID);
		// endforeach;
		// wp_reset_postdata();
		// 
		// $monthNum  = 3;
		// $monthName = date('F', mktime(0, 0, 0, $monthNum, 10));
	endif;
	

	get_header('home');
?>
		<div class="content-bg">
			<ul>
			<?php
				echo '<li></li>';
				for ($n = 0; $n < 12; $n ++):
					$monthN = $fy_start_m + $n;
					if ($monthN >= 12) $monthN = $monthN - 12;
					$dateObj = DateTime::createFromFormat('!m', $monthN+1);

		    		echo '<li><div class="month-label">' . $dateObj->format('F') . '</div></li>';

		    		// for ($o = 0; $o < count($m_sort_object[$n]); $o ++):
		    		// 	$ebID = $m_sort_object[$n][$o]->ID;
		    		// 	$eventb_img = get_field('event_img', $ebID);
		    		// 	$layoutb = get_field('layout_option', $ebID);
		    		// 	$boxb_size = get_field('content_size', $ebID);
		    		// 	if (!$eventb_img) $layoutb = 'hor';

		    		// 	echo '<div class="event-box ' . $layoutb . ' ' . $boxb_size . '"> </div>';
		    		// endfor;

		    		// echo '<div class="clearBoth"></div></li>';
		    	endfor;
		    ?>
			</ul>
		</div>
		<div class="intro-content">
			<?php
				echo '<span class="data">' . get_field('intro_pause', $id) . '</span>';
				echo '<h3>' . get_field('intro_title', $id) . '</h3>';
				echo '<p>' . get_field('intro_copy', $id) . '</p>';
			?>
		</div>
		<div class="timeline">
			<div class="line"></div><span class="arrow"></span>
		</div>
		<div class="content">
			<ul>
			<?php 
				echo '<li></li>';
				for ($r = 0; $r < 12; $r ++):
		    		echo '<li>';

		    		for ($s = 0; $s < count($m_sort_object[$r]); $s ++):
		    			$eID = $m_sort_object[$r][$s]->ID;
		    			$tID = get_field('event_type', $eID)->ID;
		    			$type_color = get_field('color_label', $tID);
		    			$event_img = get_field('event_img', $eID);
		    			$y_pos = "bot1";
		    			if ($s % 4 == 1) $y_pos = "top1";
		    			if ($s % 4 == 2) $y_pos = "bot2";
		    			if ($s % 4 == 3) $y_pos = "top2";

		    			$layout = get_field('layout_option', $eID);
		    			$box_size = get_field('content_size', $eID);
		    			if (!$event_img) $layout = 'hor';

		    			echo '<div class="event-box ' . $y_pos . ' ' . $layout . ' ' . $box_size . '">';
		    			echo '<span class="data">type-' . $tID . '</span>';
		    			echo '<div class="event-content" style="background:' . $type_color;

		    			if ($event_img) echo '"><div class="img"><img src="' . $event_img . '" /></div>';
		    			else echo '; text-align:center;">';

		    			echo get_field('event_display', $eID) . '</div>';
		    			echo '<div class="event-connector" style="border-right-color:' . $type_color . '"></div><div class="event-pointer" style="background:' . $type_color . '"></div></div>';
		    		endfor;

		    		echo '<div class="clearBoth"></div></li>';
		    	endfor;
		    ?>
			</ul>
		</div>
	</div>

<?php get_footer('home'); ?>
*/