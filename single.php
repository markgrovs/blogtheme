<?php get_header(); ?>

<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
	<div class="content-item">
		<div class="content-dets">
			<h3>Posted on <?php the_time('M j, Y'); ?></h3>
			
			<ul class="dets">
				<li class="comments-link"><a href="<?php the_permalink() ?>#comments" title=""><?php comments_number('0 Comments','1 Comment','% Comments'); ?></a></li>
			</ul>
		</div>
		
		<div class="content-body">
			<h2 class="post-title"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			
			<div class="entry">
				<?php the_content('Continue Reading'); ?>
			</div>
			<div style="padding-top:2px;">
			<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php the_permalink() ?>" layout="button_count" show_faces="true" width="450" font=""></fb:like>
			</div>
			<div style="padding-top:2px;">
			<?php if (function_exists('stc_tweetbutton')) stc_tweetbutton();?>
			</div>
		</div>
		
	</div> <!-- content-item -->
	<!-- Facebook Comments -->
	<?php if (function_exists('facebook_comments')) facebook_comments(); ?>
	<!-- End Facebook Comments -->

	<!-- Wordpress Comments -->
	<?php comments_template(); ?>
	<!-- End Wordpress Comments -->
	<?php endwhile; else: ?>
    <?php endif; ?>	
</div> <!-- content -->

<?php get_footer(); ?>