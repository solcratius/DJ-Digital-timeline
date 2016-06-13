<?php 
	// Timeline include code
?>
	<div class="tl-content">
		<div class="content-bg">
			<ul>
			<?php
				echo '<li></li>';
				for ($n = 0; $n < $totalM; $n ++):
					$monthN = (int)$fy_start_m - 1 + $n;//(ltrim($fy_start_m, "0") - 1) + $n;
					$yearN = $fy_start_y;
					if ($monthN >= 12)
					{
						$monthN = $monthN - 12;
						$yearN = $fy_start_y + 1;
						if (!$nextY_C) $nextY_C = $n;
					}
					if ($nextY_C && ($n - $nextY_C) >= 12) $yearN = $fy_start_y + 2;
					$dateObj = DateTime::createFromFormat('!m', $monthN+1);

		    		echo '<li><div class="month-label">' . $dateObj->format('F') . ' ' . $yearN . '</div></li>';
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

		<?php
			echo '<div class="content"><ul><li></li>';
				for ($r = 0; $r < $totalM; $r ++):
		    		echo '<li>';

		    		for ($s = 0; $s < count($m_sort_object[$r]); $s ++):
		    			$eID = $m_sort_object[$r][$s]->ID;
		    			$type_color = get_field('color_label', get_field('event_type', $eID)[0]);
		    			$event_date = get_field('event_date', $eID);
		    			$event_img = get_field('event_img', $eID);
		    			$event_name = get_field('event_name', $eID);
		    			$event_detail = get_field('event_detail', $eID);
		    			$event_subtitle = get_field('event_subtitle', $eID);
		    			$y_pos = "bot1";
		    			if ($s % 4 == 1) $y_pos = "top1";
		    			if ($s % 4 == 2) $y_pos = "bot2";
		    			if ($s % 4 == 3) $y_pos = "top2";

		    			$layout = get_field('layout_option', $eID);
		    			$box_size = get_field('content_size', $eID);
		    			if (!$event_img) $layout = 'hor';

		    			echo '<div class="event-box ' . $y_pos . ' ' . $layout . ' ' . $box_size . '">';
		    			echo '<div class="event-content" style="background:' . $type_color;

		    			if ($event_img) echo '"><div class="img"><img src="' . $event_img . '" /></div>';
		    			else echo '; text-align:center;">';
		    			if ($event_detail) echo '<span class="data date">' . $event_date . '</span><span class="data name">' . $event_name . '</span><span class="data detail">' . $event_detail . '</span><span class="detail-btn"></span>';
		    			echo '<p>' . get_the_title ($eID);
		    			if ($event_subtitle) echo '<span class="subtitle">' . $event_subtitle . '</span>';
		    			echo '</p></div>';

		    			echo '<div class="event-connector" style="border-color:' . $type_color . '"></div><div class="event-pointer" style="background:' . $type_color . '"></div></div>';
		    		endfor;

		    		echo '<div class="clearBoth"></div></li>';
		    	endfor;
		    echo '</ul></div>';
		?>
		
	</div>