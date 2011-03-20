<?php
/*
Template Name: Photo_Archives
*/
get_header(); ?>

<div id="content">
	<div class="content-item">
		<div class="content-dets">
			<?php the_post(); ?>
			<?php 
					global $wp_query;
					$current_cat = $wp_query->query_vars['cat'];
					$current_page = $wp_query->query_vars['page'];
			?>				
			<ul class="dets">
				<?php 
					$categories = get_categories('child_of=4&hide_empty=1');
					foreach ($categories as $category)
					{
						if ($category->parent == 4)
						{
							$option = '<h2>';
							$option .= $category->cat_name;
							$option .= '</h2>';
						}else{
						$option = '<li>';
						$option .= '<a href="';
						//$option .=  $wp_query->query;
						$option .= '?cat=' . $category->cat_ID . '">';
						$option .= $category->cat_name . ' (' . $category->count . ')';
						if ($category->cat_ID == $current_cat)
						{
							$option .= ' *';
						}
						$option .= '</a>';
						$option .= '</li>';
						}
						echo $option;
					}
				?>
			</ul>
		
		</div><!-- #content-dets -->
		<div class="content-body">
			
	
					
					
			<?php
					$images_per_page = 20;
					if (empty($current_cat))
					{	
						$current_cat = 4;
						$current_page = 1;
					?>
						<h2>All</h2>
					<?php
						echo wp_image_archives('term_id=4&date_format=m-d-Y&first_image_mode=on&column=4&item=20&image_order=DESC&order_by=date&order=DESC&date_show=on&limit=0,20');
						$total_images = wp_image_archives('term_id=4&date_format=m-d-Y&first_image_mode=on&column=4&item=20&image_order=DESC&order_by=date&order=DESC&date_show=on&limit=0,20&count_only=on');
					}
					else
					{
					?>
						<h2><?php echo get_cat_name($current_cat);?></h2>
					<?php
						//echo $current_page;
						$limit_top = 20;
						$limit_bottom = 0;
						if($current_page>0)
						{
							$limit_top = $images_per_page * $current_page;
							$limit_bottom = $limit_top - $images_per_page;
							//echo 'Items Per Page: ' .$images_per_page;
							//echo ' Top: ' .$limit_top;
							//echo ' Bottom: ' .$limit_bottom;
						}
						//echo 'cat='.$current_cat;
						//echo ' page='.$current_page;
						echo wp_image_archives('term_id=' . $current_cat . '&date_format=m-d-Y&first_image_mode=on&column=4&item=20&image_order=DESC&order_by=date&order=DESC&date_show=on&limit=' . $limit_bottom .',' . $images_per_page);
						$total_images = wp_image_archives('term_id=' . $current_cat . '&date_format=m-d-Y&first_image_mode=on&column=4&item=20&image_order=DESC&order_by=date&order=DESC&date_show=on&limit=0,20&count_only=on');
					}					
 			?>
	</div>
</div><!-- #content -->
<?php sf_image_pagenavi($total_images, $images_per_page, $current_cat, $current_page); ?>
<?php get_footer(); ?>