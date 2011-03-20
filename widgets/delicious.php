<?php function widget_sf_delicious($args) { extract($args); ?>

<div id="delicious-feed" class="content-item">
	<div class="content-dets">
		<h3>Latest Links</h3>
		
		<ul class="dets">
			<li class="delicious-link"><a href="http://www.delicious.com/<?php echo get_option(THEME_PREFIX . "delicious_name"); ?>" title="Delicious.com" target="_blank">View More</a></li>
		</ul>
	</div>
	
	<div class="content-body delicious-container">
		<?php // Get RSS Feed(s)
		include_once(ABSPATH . WPINC . '/feed.php');
		
		// Get a SimplePie feed object from the specified feed source.
		$delicious_name = get_option(THEME_PREFIX . "delicious_name");
		$rss = fetch_feed('feed://feeds.delicious.com/rss/' . $delicious_name . '');
		
		// Figure out how many total items there are.
		$delicious_num = get_option(THEME_PREFIX . "delicious_num");
		$maxitems = $rss->get_item_quantity($delicious_num); 
		
		// Build an array of all the items, starting with element 0 (first element).
		$rss_items = $rss->get_items(0, $maxitems); 
		?>
		
		<ul id="delicious">
		    <?php if ($maxitems == 0) echo '<li>No items.</li>';
		    else
		    // Loop through each feed item and display each item as a hyperlink.
		    foreach ( $rss_items as $item ) : ?>
		    <li>
		        <h2><a href="<?php echo $item->get_permalink(); ?>" title="<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>" target="_blank"><?php echo $item->get_title(); ?></a></h2>
		        <p><?php echo $item->get_description(); ?></p>
		    </li>
		    <?php endforeach; ?>
		</ul>
		
		<div id="delicious-last-item"><!-- nothing to see here --></div>
	</div>
</div> <!-- delicious-feed -->

<?php } register_sidebar_widget('Seven Five - Delicious', 'widget_sf_delicious');?>