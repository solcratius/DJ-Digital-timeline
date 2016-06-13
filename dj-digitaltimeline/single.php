<?php
	// session_start();
/**
 * The template for displaying home pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package DJ Digital Timeline
 */

	$id = $post->ID;
	$myPass = get_field('login_password', $id);
	$passOk = true;
	$cookie = "DJ-TIMELINE-LOGIN";

	$CPT = $post->post_type;
	$qString = $_SERVER['QUERY_STRING'];
	$catID = array();

	if (get_field('password_protected', $id))
	{
		$passOk = false;

		if (!isset($_COOKIE[$cookie]))
		{
			if(isset($_POST['pass']))
			{
				if ($_POST['pass'] == $myPass)
				{
					$passOk = true;
					setcookie($cookie, $myPass, time() + (86400 * 30), '/');
				}
			}
		}
		else
		{
			if ($_COOKIE[$cookie] == $myPass)
			{
				$passOk = true;
			}
			else
			{
				if(isset($_POST['pass']))
				{
					if ($_POST['pass'] == $myPass)
					{
						$passOk = true;
						setcookie($cookie, $myPass, time() + (86400 * 30), '/');
					}
				}
			}
		}
	}

	if (!$passOk)
	{
		echo '<div id="secureBox"><span>ENTER YOUR PASSWORD</span><form method="post"><input name="pass" type="password" size="25" /><input name="submit" type="submit" value="ENTER" /></form></div>';
	}

	if ($CPT == "timeline" && $passOk)
	{
		if ($qString) $cat = $_GET["cType"];

		if ($cat)
		{
			foreach ($cat as $c):
				array_push($catID, substr($c, 2));
			endforeach;
		}
		else
		{
			$typeList_OBJ = get_field('type_list', $id);

			foreach ($typeList_OBJ as $tlObj):
				array_push($catID, $tlObj);
			endforeach;
		}

		$meta_query = array('relation' => 'OR');

		foreach ($catID as $cID) {
		    $meta_query[] = array(
		        'key' => 'event_type',
		        'value'     => $cID,
		        'compare'   => 'LIKE',
		    );
		}

		$event_objects = get_posts(array(
			'post_type' => 'event',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			// 'sort_column' => 'event_date',
			// 'sort_order' => 'desc',
			'meta_key' => 'event_date',
			'orderby' => 'meta_value',
			'order' => 'asc',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'tl_group',
					'value' => '"' . $id . '"',
					'compare' => 'LIKE'
				),
				$meta_query
			)
		));

		$totalM = 12;
		$fy_start = get_field('fy_starts_on', $id);
		$include_prevQ = get_field('include_prevQ', $id);
		$include_nextQ = get_field('include_nextQ', $id);

		$fy_start_m = substr($fy_start, 4, 2);
		$fy_start_y = substr($fy_start, 0, 4);

		if ($include_prevQ)
		{
			$fy_start_m = ltrim($fy_start_m, "0") - 1 - 3;
			if ($fy_start_m < 0)
			{
				$fy_start_m = $fy_start_m + 12;
				$fy_start_y = (int)$fy_start_y - 1;
			}
			$fy_start_m = sprintf("%02d", $fy_start_m + 1);

			$totalM = $totalM + 3;
		}

		$fy_tmp_y = substr($fy_start, 0, 4) + 1;
		$fy_tmp_m = substr($fy_start, 4, 2);
		$fy_tmp_d = substr($fy_start, 6, 2);

		$fy_end = $fy_tmp_y . $fy_tmp_m . $fy_tmp_d;

		if ($include_nextQ)
		{
			$fy_tmp_m = ltrim($fy_tmp_m, "0") - 1 + 3;
			if ($fy_tmp_m > 11)
			{
				$fy_tmp_m = $fy_tmp_m - 12;
				$fy_tmp_y = (int)$fy_tmp_y + 1;
			}
			$fy_tmp_m = sprintf("%02d", $fy_tmp_m + 1);
			$fy_end = $fy_tmp_y . $fy_tmp_m . $fy_tmp_d;

			$totalM = $totalM + 3;
		}

		if ($include_prevQ) $fy_start = $fy_start_y . $fy_start_m . substr($fy_start, 6, 2);

	    $m_order = array();
		$m_sort_object = array();

		$m_order_start = ltrim($fy_start_m, "0") - 1;
		$y_order_start = (int)$fy_start_y;
		$y_loop = 0;
		$m_order_n;

	    if ($event_objects):
	    	for ($i = 0; $i < $totalM; $i ++):
	    		$m_order_n = $m_order_start + $i;

	    		if ($m_order_n > 11 && $m_order_n < 24)
	    		{
	    			$m_order_n = $m_order_n - 12;
	    			if ($y_loop <= 0) $y_loop = 1;
	    		}

	    		if ($m_order_n > 23)
	    		{
	    			$m_order_n = $m_order_n - 24;//12;
	    			if ($y_loop <= 1) $y_loop = 2;
	    		}
	    		
	    		$finalVal = sprintf("%02d", $m_order_n + 1) . ($y_order_start + $y_loop);

	    		array_push($m_order, $finalVal);
	    		// echo 'YO-' . $i . ':' . $m_order[$i] . '<br />';
	    	endfor;

	    	$data_i = 0;

	    	foreach ($m_order as $l):
	    		$temp_object = array();

	    		foreach ($event_objects as $m):
	    			setup_postdata($m);
	    			$data = get_field('event_date', $m->ID);

	    			if ($data >= $fy_start && $data < $fy_end)
	    			{
	    				$tempM = substr($data, 4, 2) . substr($data, 0, 4);
	    				if ($tempM == $l) array_push($temp_object, $m);
	    				// echo $tempM . ", " . $data_i . "<br />";
	    			}
	    			
	    		endforeach;

	    		array_push($m_sort_object, $temp_object);
	    		$data_i ++;

	    		wp_reset_postdata();
	    	endforeach;

		endif;
	}
 // print_r(array_values($m_order));
	get_header();
?>
<?php
	if ($CPT == "timeline")
	{
		if ($passOk)
		{
			echo '<div class="main-content"><span class="data">' . get_permalink($id) . '</span>';
			include 'timeline.php';
			echo '</div>';
		}
	}

	get_footer();
?>