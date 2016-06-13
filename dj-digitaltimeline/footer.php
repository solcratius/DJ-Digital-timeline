<?php
/**
 * The Footer template for the DJ Digital Timeline.
 *
 * Displays all of the footer-content section to end tag of html.
 *
 * @package DJ Digital Timeline
 */
	$thisID = $post->ID;
	$fID = get_field('tl_select', $thisID)[0];
	$siteTitle = get_field('title', $fID);
?>
	<footer class="footer-content short">
		<div class="event-type">
			<ul>
				<?php
					$types = get_field('type_list', $fID);
					if ($types):
						foreach($types as $type):
							setup_postdata($type);
							echo '<li><a class="t-' . $type . '" href="#"><div class="dot" style="background:';
			            	echo get_field('color_label', $type) . ';"></div>';
			            	echo get_the_title( $type ) . '</a></li>';
						endforeach;

						wp_reset_postdata();
					endif;
			    ?>

				<div class="clearBoth"></div>
			</ul>
		</div>

		<div class="title">
			<?php
				echo '<a href="' . get_permalink ($thisID) . '" title="';
				echo esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home"><div class="logo"><img class="lg" src="';
				echo get_template_directory_uri() . '/images/dj-logo.svg" alt="DOW JONES" />';
				echo '</div><span>' . $siteTitle . '</span></a>';
			?>
		</div>
	</footer>
	<div class="detail-content">
		<div class="bg"> </div>
		<div class="wrapper">
			<p class="date"></p>
			<img src="" />
			<h2></h2>
			<p class="txt"></p>
		</div>
		<a href="#" class="arrow-btn left">LEFT</a>
		<a href="#" class="arrow-btn right">RIGHT</a>
		<a href="#" class="close-btn">CLOSE</a>
	</div>
	<div class="download-content"></div>
	<div id="printable"></div>
	<?php wp_footer(); ?>
</body>
</html>