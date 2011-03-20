<?php
	
	// Theme Constants
	define("THEME_PREFIX", "sevenfive_");
	
	// Theme Location
	define('THEME', get_bloginfo('template_url'), true);
	
	// Add RSS Feed Links
	add_theme_support( 'automatic-feed-links' );
	
	// Custom Menus
	register_nav_menu('main_menu', __('Main Menu'));
	
	// unregister all default WP Widgets
	function unregister_default_wp_widgets() {
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_Links');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Search');
		unregister_widget('WP_Widget_Categories');
		unregister_widget('WP_Widget_Recent_Posts');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_Tag_Cloud');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Text');
		unregister_widget('WP_Widget_Akismet');
	}
 
	add_action('widgets_init', 'unregister_default_wp_widgets', 1);
	
	// Feed Refresh Rate
	add_filter( 'wp_feed_cache_transient_lifetime', create_function('$a', 'return 1800;') );
	
	// Load Required Theme Scripts
	include("scripts/lastfm-records/last.fm.records.php");
	
	// Include Custom Theme Widgets
	include("widgets/posts.php");
	include("widgets/flickr.php");
	include("widgets/twitter.php");
	include("widgets/delicious.php");
	include("widgets/youtube.php");
	include("widgets/vimeo.php");
	include("widgets/feed.php");
	include("widgets/lastfm.php");
	include("widgets/photo_posts.php");
	
	// The Admin Page
	add_action('admin_menu', "sf_sevenfive_admin_init");
	
	// Register Admin
	function sf_sevenfive_admin_init()
	{
		$page = add_theme_page( "Seven Five Options", "Theme Options", 8, 'sf_sevenfive_admin_menu', 'sf_sevenfive_admin');
	
		// Custom Image Uploaders
		sf_add_img_upload_filter(THEME_PREFIX.'background_img', 'sf_handle_bg_upload');
		sf_add_img_upload_filter(THEME_PREFIX.'logo_img', 'sf_handle_logo_upload');
		sf_add_img_upload_filter(THEME_PREFIX.'favicon', 'sf_handle_favicon_upload');
	}
	
	// Image Upload Helper Function
	function sf_add_img_upload_filter($option_name, $handler) {
	  add_filter('pre_update_option_'.$option_name, $handler, 10, 2);
	}
	
	// Image Upload Handler Functions
	function sf_handle_bg_upload($new_value, $old_value) {
	  return sf_handle_img_upload(
	    $new_value, 
	    $old_value, 
	    THEME_PREFIX.'background_img_upload', 
	    THEME_PREFIX.'delete_bg_img');
	}
	
	function sf_handle_logo_upload($new_value, $old_value) {
	  return sf_handle_img_upload(
	    $new_value, 
	    $old_value, 
	    THEME_PREFIX.'logo_img_upload', 
	    THEME_PREFIX.'delete_logo_img');
	}
	
	function sf_handle_favicon_upload($new_value, $old_value) {
	  return sf_handle_img_upload(
	    $new_value, 
	    $old_value, 
	    THEME_PREFIX.'favicon_upload', 
	    THEME_PREFIX.'delete_favicon');
	}
	
	// Generic Image Upload Handler
	function sf_handle_img_upload($new_value, $old_value, $file_index, $delete_field) {
	  if ( isset($_POST[$delete_field]) && $_POST[$delete_field]=='true' )
	    return '';
	
	  if ( empty($_FILES) || !isset($_FILES[$file_index]) || 0==$_FILES[$file_index]['size'] )
	    return $old_value;
	
	  $overrides = array('test_form' => false);
	  $file = wp_handle_upload($_FILES[$file_index], $overrides);
	
	  if ( isset($file['error']) )
	    wp_die( $file['error'] );
	
	  $url = $file['url'];
	  $type = $file['type'];
	  $file = $file['file'];
	  $filename = basename($file);
	
	  // Construct The Object Array
	  $object = array(
			  'post_title' => $filename,
			  'post_content' => $url,
			  'post_mime_type' => $type,
			  'guid' => $url
			  );
	
	  // Save The Data
	  $id = wp_insert_attachment($object, $file);
	
	  // Add The Meta
	  wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	
	  do_action('wp_create_file_in_uploads', $file, $id); // For replication
	  return esc_url($url);
	}

	function sf_sevenfive_admin() {
	
		$option_fields = array();
	
		if ( $_GET['updated'] ) echo '<div id="message" class="updated fade"><p>Seven Five Theme Options Saved.</p></div>';
		echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/functions.css" type="text/css" media="all" />';
		
		// Accordion Script
		echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/scripts/accordion/style.css" type="text/css" media="all" />';
		echo '<script src="'.get_bloginfo('template_url').'/scripts/accordion/jquery.ui.js" type="text/javascript"></script>';
		echo '<script src="'.get_bloginfo('template_url').'/scripts/accordion/jquery.accordion.js" type="text/javascript"></script>';
		
		// Color Picker Script
		echo '<link rel="stylesheet" href="'.get_bloginfo('template_url').'/scripts/colorpicker/style.css" type="text/css" media="all" />';
		echo '<script src="'.get_bloginfo('template_url').'/scripts/colorpicker/jquery.colorpicker.js" type="text/javascript"></script>';
		echo '<script src="'.get_bloginfo('template_url').'/scripts/colorpicker/jquery.eye.js" type="text/javascript"></script>';
		
		// Styling File Form Elements
		echo '<script src="'.get_bloginfo('template_url').'/scripts/si.files.js" type="text/javascript"></script>';

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>

    <h2>Seven Five Theme Options</h2>
    <div class="metabox-holder">
    	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php wp_nonce_field('update-options'); ?>
    
        <div id="theme-options">
	        <div id="accordion" class="postbox-container">
	            <?php 
	            	include("options/theme-support.php");
	            	include("options/custom-logo.php");
	            	include("options/custom-menus.php");
	            	include("options/custom-favicon.php");
	            	include("options/custom-styles.php");
	            	include("options/featured-content.php");
	            	include("options/featured-photo-content.php");
	            	include("options/social-options.php");
	            	include("options/footer-text.php");
	            	include("options/analytics-code.php");
	            	include("options/no-ie.php");
	        	?>
	        </div> <!-- postbox-container -->
        </div> <!-- theme-options -->
        
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="<?php echo implode(",", $option_fields); ?>" />
        </form>
        
        <script type="text/javascript" language="javascript">SI.Files.stylizeAll();</script>
    </div> <!-- metabox-holder -->
</div> <!-- wrap -->

<?php
}

	// Custom Styles Function
	add_action( 'parse_request', 'sf_custom_css' );
	function sf_custom_css($wp) {
	    if (
	        !empty( $_GET['sf-custom-content'] )
	        && $_GET['sf-custom-content'] == 'css'
	    ) {
	        header( 'Content-Type: text/css' );
	        require dirname( __FILE__ ) . '/style-custom.php';
	        exit;
	    }
	}

	// Custom Pagination Function
	function sf_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) 
	{
		global $request, $posts_per_page, $wpdb, $paged;
		if(empty($prelabel)) {   
			$prelabel = '';
		} 
		if(empty($nxtlabel)) {
			$nxtlabel = '';
		} 
		$half_pages_to_show = round($pages_to_show/2);
		if (!is_single()) {
			if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);  } else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);  }
			$fromwhere = $matches[1];
			$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
			$max_page = ceil($numposts /$posts_per_page);
			if(empty($paged)) {
				$paged = 1;
			}
			if($max_page > 1 || $always_show) {
				echo "$before <div class='content-item pagination'><div class='content-dets'><h3>Pages:</h3></div><div class='content-body'>";
			   	if ($paged >= ($pages_to_show-1)) {
					echo '';  
				}
				previous_posts_link($prelabel);
				for($i = $paged - $half_pages_to_show; $i <= $paged + $half_pages_to_show; $i++) {   
					if ($i >= 1 && $i <= $max_page) {   
						if($i == $paged) {
							echo "$i";
						} 
						else {
							echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';   
						}
					}
				}
				next_posts_link($nxtlabel, $max_page);
				if (($paged+$half_pages_to_show) < ($max_page)) {
					echo '';   
				}
				echo "</div></div> $after";
			}
		}
	}
	
	// Custom Pagination For Image Archives
	function sf_image_pagenavi($total_count, $per_page_count, $cat_id, $on_page=1) {
		//echo "total count: ".$total_count;
		//echo " page count: " .$per_page_count;
		$page = $total_count/ $per_page_count;
		if( $page != (int)$page ) $page = (int)$page + 1;
		if( $page <= 1 ) return;
		// $i is the number of the whole images per a category.
		$i = 0;
		
		//for( $current_page=1 ; $current_page <= $page; $current_page ++ )
		//{
		//	$output .= "<h3><a href='?cat=" . $cat_id . "&page=". $current_page . "'>" . " ( $current_page / $page )</a></h3>\n";
		//}
		
		
		
		$output .= "<div class='content-item pagination'><div class='content-dets'><h3>Pages:</h3></div><div class='content-body' {margin-left:0px;}>";   
		
		for( $current_page=1 ; $current_page <= $page; $current_page ++ )
		{
			$output .= ' ';
			if ($current_page == $on_page)
			{
				$output .= $current_page;
			}else
			{
				$output .= " <a href='?cat=" . $cat_id . "&page=". $current_page . "'>" . "$current_page</a>";	
			}
			$output .= ' ';
		}
		$output .= "</div></div>";
		echo $output;
	}

	// Sidebar Widgets
	if ( function_exists('register_sidebar') )
	register_sidebar(array('name'=>'Home Page',
		'before_widget' => '<div id="%1$s" class="widget content-item %2$s">',
		'after_widget' => '</div></div>',
		'before_title' => '<div class="content-dets"><h3>',
		'after_title' => '</h3></div><div class="content-body">',
	));


	// Query Filters
	add_filter('query_vars', 'cat_queryvars' );
	add_filter('query_vars', 'page_queryvars');
	function cat_queryvars( $qvars )
	{
	  $qvars[] = 'cat';
	  return $qvars;
	}
	
	function page_queryvars( $qvars)
	{
		$qvars[] = 'page';
		return $qvars;
	}

/*
 Plugin Name: Image Archives
 Plugin URI: http://everything.ismusic.in/2010/12/image-archives/
 Description: Image Archives is a wordpress plugin that displays images from your published posts with a permalink back to the post that the image is connected to. It can also be used as a complete visual archive or gallery archive with several customizable settings.
 Version: 0.63
 Author: Nomeu
 Author URI: http://everything.ismusic.in/
 */
 
/*  Copyright 2010 Nomeu (email : nomeu@ismusic.in)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class image_archives {
	
	public $v_first_image_mode;
	public $v_image_order_by;
	public $v_image_order;
	public $v_term_id;
	public $v_order_by;
	public $v_order;
	public $v_str;
	public $v_limit;
	public $v_img_size;
	public $v_item;
	public $v_column;
	public $v_design;
	public $v_date_format;
	public $v_date_show;
	public $v_title_show;
	public $v_cache;
	public $v_section_name;
	public $v_section_sort;
	public $v_section_result_number_show;
	public $v_count_only;
	
	
	// shortcode function
	// shortcode は return を用いても良いが、template tag では使えない。
	function image_archives_shortcode ( $atts, $content = null ) {
		
		extract( shortcode_atts( array(
			'first_image_mode'	=>	'off',
			'image_order_by'	=>	'date',
			'image_order'		=>	'ASC',
			'term_id'			=>	'1',
			'order_by'			=>	'title',
			'order'				=>	'ASC',
			'str'				=>	'%',
			'limit'				=>	'0,50',
			'size'				=>	'medium',
			'design'			=>	'2',
			'item'				=>	'9',
			'column'			=>	'3',
			'date_format'		=>	'Y-m-d',
			'date_show'			=>	'off',
			'title_show'		=>	'on',
			'cache'				=>	'off',
			'section_name'		=>	'Section',
			'section_sort'		=>	'number',
			'section_result_number_show'	=>	'on',
			'count_only'		=> 'off'
		), $atts ) );
		
		
		//Substitution
		
		//first_image_mode
			if( ($first_image_mode == 'on') || ($first_image_mode == 'off') ) $this->v_first_image_mode = $first_image_mode;
			else return "shortcode atts error. first_image_mode is required to be 'on' or 'off'.";
		//image_order_by
			if( $image_order_by == 'title' ) $this->v_image_order_by = 'p1.post_date';
			elseif( $image_order_by == 'date' ) $this->v_image_order_by = 'p1.post_date';
			else return "shortcode atts error. image_order_by is required to be 'title' or 'date'.";
		//image_order
			if( ($image_order == 'ASC') || ($image_order == 'DESC') ) $this->v_image_order = $image_order;
			else return "shortcode atts error. image_order is required to be 'ASC' or 'DESC'.";
		//term_id
			$this->v_term_id = $term_id;
		//order_by
			if( $order_by == 'title' ) $this->v_order_by = 'post_title';
			elseif( $order_by == 'date' ) $this->v_order_by = 'post_date';
			else return "shortcode atts error. order_by is required to be 'title' or 'date'.";
		//order
			if( ($order == 'ASC') || ($order == 'DESC') ) $this->v_order = $order;
			else return "shortcode atts error. order is required to be 'ASC' or 'DESC'.";
		//string
			$this->v_str = $str;
		//limit
			$this->v_limit = $limit;
		//img_size
			if( ($size == 'thumbnail') || ($size == 'medium') || ($size == 'large') || ($size == 'full') ) $this->v_img_size = $size;
			else return "shortcode atts error. size is required to be 'thumbnail' or 'medium' or 'large' or 'full'.";
		//design
			$this->v_design = intval( $design );
		//item
			$this->v_item = intval( $item );
		//column
			$column = intval( $column );
			if( $column < 1 ) return "the number of 'column' is required to be larger than 0.";
			elseif( $column > 100 ) return "the number of 'column' is too big.";
			$this->v_column = $column;
		//date format
			$this->v_date_format = $date_format;
		//date show
			if( ($date_show == 'on') || ($date_show == 'off') ) $this->v_date_show = $date_show;
			else return "date_show is required to be 'on' or 'off'.";
		//title show
			if( ($title_show == 'on') || ($title_show == 'off') ) $this->v_title_show = $title_show;
			else return "title_show is required to be 'on' or 'off'.";
		//cache
			if( ($cache == 'on') || ($cache == 'off') ) $this->v_cache = $cache;
			else return "cache is required to be 'on' or 'off'.";
		//section_name
			$this->v_section_name = htmlspecialchars($section_name);
		//section_sort
			if( ($section_sort == 'category') || ($section_sort == 'number') ) $this->v_section_sort = $section_sort;
			else return "section_sort is required to be 'category' or 'number'";
		//section_result_number_show
			if( ($section_result_number_show == 'on') || ($section_result_number_show == 'off') ) $this->v_section_result_number_show = $section_result_number_show;
			else return "section_result_number_show is required to be 'on' or 'off'";
		//count_only
			if( ($count_only == 'on') || ($count_only == 'off') ) $this->v_count_only = $count_only;
			else return "count_only is required to be 'on' or 'off'";
			
		return $this->image_archives_core();
		
	}
	
	
	
	//template tag function
	// cannot use "return".
	function image_archives_template_tag ( $args = '' ) {
		
		$default = array(
			'first_image_mode'	=>	'off',
			'image_order_by'	=>	'date',
			'image_order'		=>	'ASC',
			'term_id'			=>	'1',
			'order_by'			=>	'title',
			'order'				=>	'ASC',
			'str'				=>	'%',
			'limit'				=>	'0,50',
			'size'				=>	'medium',
			'design'			=>	'2',
			'item'				=>	'9',
			'column'			=>	'3',
			'date_format'		=>	'Y-m-d',
			'date_show'			=>	'off',
			'title_show'		=>	'on',
			'cache'				=>	'off',
			'section_name'		=>	'Section',
			'section_sort'		=>	'number',
			'section_result_number_show'	=>	'on',
			'count_only'		=>	'off'
		);
		
		$args = wp_parse_args($args, $default);
		
		extract( $args, EXTR_SKIP );
		
		
		//Substitution
		
		//first_image_mode
			if( ($first_image_mode == 'on') || ($first_image_mode == 'off') ) $this->v_first_image_mode = $first_image_mode;
			else { echo "shortcode atts error. first_image_mode is required to be 'on' or 'off'."; return; }
		//image_order_by
			if( $image_order_by == 'title' ) $this->v_image_order_by = 'p1.post_date';
			elseif( $image_order_by == 'date' ) $this->v_image_order_by = 'p1.post_date';
			else { echo "shortcode atts error. image_order_by is required to be 'title' or 'date'."; return; }
		//image_order
			if( ($image_order == 'ASC') || ($image_order == 'DESC') ) $this->v_image_order = $image_order;
			else { echo "shortcode atts error. image_order is required to be 'ASC' or 'DESC'."; return; }
		//term_id
			$this->v_term_id = $term_id;
		//order_by
			if( $order_by == 'title' ) $this->v_order_by = 'post_title';
			elseif( $order_by == 'date' ) $this->v_order_by = 'post_date';
			else { echo "shortcode atts error. order_by is required to be 'title' or 'date'."; return; }
		//order
			if( ($order == 'ASC') || ($order == 'DESC') ) $this->v_order = $order;
			else { echo "shortcode atts error. order is required to be 'ASC' or 'DESC'."; return; }
		//string
			$this->v_str = $str;
		//limit
			$this->v_limit = $limit;
		//img_size
			if( ($size == 'thumbnail') || ($size == 'medium') || ($size == 'large') || ($size == 'full') ) $this->v_img_size = $size;
			else { echo "shortcode atts error. size is required to be 'thumbnail' or 'medium' or 'large' or 'full'."; return; }
		//design
			$this->v_design = intval( $design );
		//item
			$this->v_item = intval( $item );
		//column
			$column = intval( $column );
			if( $column < 1 ) { echo "the number of 'column' is required to be larger than 0."; return; }
			elseif( $column > 100 ) { echo "the number of 'column' is too big."; return; }
			$this->v_column = $column;
		//date format
			$this->v_date_format = $date_format;
		//date show
			if( ($date_show == 'on') || ($date_show == 'off') ) $this->v_date_show = $date_show;
			else { echo "date_show is required to be 'on' or 'off'."; return; }
		//title show
			if( ($title_show == 'on') || ($title_show == 'off') ) $this->v_title_show = $title_show;
			else { echo "title_show is required to be 'on' or 'off'."; return; }
		//cache
			if( ($cache == 'on') || ($cache == 'off') ) $this->v_cache = $cache;
			else { echo "cache is required to be 'on' or 'off'."; return; }
		//section_name
			$this->v_section_name = htmlspecialchars($section_name);
		//section_sort
			if( ($section_sort == 'category') || ($section_sort == 'number') ) $this->v_section_sort = $section_sort;
			else { echo "section_sort is required to be 'category' or 'number'"; return; }
		//section_result_number_show
			if( ($section_result_number_show == 'on') || ($section_result_number_show == 'off') ) $this->v_section_result_number_show = $section_result_number_show;
			else { echo "section_result_number_show is required to be 'on' or 'off'"; return; }
		//count_only
			if( ($count_only == 'on') || ($count_only == 'off') ) $this->v_count_only = $count_only;
			else { echo "count_only is required to be 'on' or 'off'"; return; }
			
		//important
		return $this->image_archives_core();
		
	}
	
	
	
	function image_archives_settings_write () {
		
		$file = WP_PLUGIN_DIR . '/image-archives/settings.ini';
		
		$str =	 "first_image_mode = \"".$this->v_first_image_mode	."\"\n"
				."image_order_by = \""	.$this->v_image_order_by	."\"\n"
				."image_order = \""		.$this->v_image_order		."\"\n"
				."term_id = \""			.$this->v_term_id			."\"\n"
				."order_by = \""		.$this->v_order_by			."\"\n"
				."order = \""			.$this->v_order				."\"\n"
				."str = \""				.$this->v_str				."\"\n"
				."limit = \""			.$this->v_limit				."\"\n"
				."img_size = \""		.$this->v_img_size			."\"\n"
				."item = \""			.$this->v_item				."\"\n"
				."column = \""			.$this->v_column			."\"\n"
				."design = \""			.$this->v_design			."\"\n"
				."date_format = \""		.$this->v_date_format		."\"\n"
				."date_show = \""		.$this->v_date_show			."\"\n"
				."title_show = \""		.$this->v_title_show		."\"\n"
				."cache = \""			.$this->v_cache				."\"\n"
				."section_name = \""	.$this->v_section_name		."\"\n"
				."section_sort = \""	.$this->v_section_sort		."\"\n"
				."section_result_number_show = \""	.$this->v_section_result_number_show	."\"\n"
				."count_only = \""		.$this->v_count_only		."\"\n";
		
		if( $fp = fopen( $file, 'w' ) ) {
			flock( $fp, LOCK_EX );
				fwrite( $fp, $str );
			flock( $fp, LOCK_UN );
			fclose($fp);
		} else {
			echo 'the settings file cannot be opened or be created.';
		}
		
	}
	
	
	
	function image_archives_settings_read () {
		
		$file = WP_PLUGIN_DIR . '/image-archives/settings.ini';
		
		if( file_exists($file) ) {
		
			$ini = parse_ini_file($file);
		
			$this->v_first_image_mode	= $ini["first_image_mode"];
			$this->v_image_order_by		= $ini["image_order_by"];
			$this->v_image_order		= $ini["image_order"];
			$this->v_term_id			= $ini["term_id"];
			$this->v_order_by			= $ini["order_by"];
			$this->v_order				= $ini["order"];
			$this->v_str				= $ini["str"];
			$this->v_limit				= $ini["limit"];
			$this->v_img_size			= $ini["img_size"];
			$this->v_item				= $ini["item"];
			$this->v_column				= $ini["column"];
			$this->v_design				= $ini["design"];
			$this->v_date_format		= $ini["date_format"];
			$this->v_date_show			= $ini["date_show"];
			$this->v_title_show			= $ini["title_show"];
			$this->v_cache				= $ini["cache"];
			$this->v_section_name		= $ini["section_name"];
			$this->v_section_sort		= $ini["section_sort"];
			$this->v_section_result_number_show		= $ini["section_result_number_show"];
			$this->v_count_oonly		= $ini["count_only"];
			
			return true;
		
		} else {
			return false;
		}
		
	}
	
	
	
	function image_archives_query( &$row_count = 0 ) {
		
		global $wpdb;
		
		if( $this->v_first_image_mode == 'off' ) {
			
			$query	= "SELECT SQL_CALC_FOUND_ROWS DISTINCT p1.ID AS image_post_id, p1.post_parent AS parent_article_id, $wpdb->posts.post_title, $wpdb->posts.post_date"
					. " FROM $wpdb->posts AS p1"
					. " INNER JOIN $wpdb->term_relationships ON ( $wpdb->term_relationships.object_id = p1.post_parent )"
					. " INNER JOIN $wpdb->posts ON ( $wpdb->posts.ID = p1.post_parent )"
					. " INNER JOIN $wpdb->term_taxonomy ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id )"
					. " WHERE p1.post_mime_type LIKE 'image%'"
				//	. " AND p1.post_type = 'attachment'"
					. " AND p1.post_status = 'inherit'"
					. " AND $wpdb->posts.post_status = 'publish'"
					. " AND $wpdb->term_taxonomy.term_id IN (". $wpdb->escape( $this->v_term_id ) .")"
					. " AND p1.post_title LIKE '". $wpdb->escape( $this->v_str ) ."'"
					. " ORDER BY ". $wpdb->escape( $this->v_order_by ) ." ". $wpdb->escape( $this->v_order )
					. " LIMIT ". $wpdb->escape( $this->v_limit );
			
			/* Query Test
				SELECT *
				FROM wp_posts AS p1
				INNER JOIN wp_term_relationships ON ( wp_term_relationships.object_id = p1.post_parent )
				INNER JOIN wp_posts ON ( wp_posts.ID = p1.post_parent )
				INNER JOIN wp_term_taxonomy ON ( wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id )
				WHERE p1.post_mime_type LIKE 'image%'
				AND p1.post_title LIKE 'deadspace2_%'
				AND p1.post_type = 'attachment'
				AND p1.post_status = 'inherit'
				AND wp_posts.post_status = 'publish'
				AND wp_term_taxonomy.term_id IN ( 155 )
				AND p1.post_title LIKE '%_logo'
				LIMIT 0 , 30
			*/
			
			$query_array = $wpdb->get_results($query, ARRAY_A);
			// query_array[ROW][ image_post_id / parent_article_id / post_title / post_date ]
			
		} elseif( $this->v_first_image_mode == 'on' ) {
			
			$query2	= "SELECT SQL_CALC_FOUND_ROWS * FROM (SELECT p1.ID AS image_post_id, p1.post_title AS image_post_title, p1.post_parent AS parent_article_id, $wpdb->posts.post_title, $wpdb->posts.post_date, $wpdb->posts.comment_count"
					. " FROM $wpdb->posts AS p1"
					. " INNER JOIN $wpdb->term_relationships ON ($wpdb->term_relationships.object_id = p1.post_parent)"
					. " INNER JOIN $wpdb->posts ON ($wpdb->posts.ID = p1.post_parent)"
					. " INNER JOIN $wpdb->term_taxonomy ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id )"
					//. " WHERE p1.post_mime_type LIKE 'image%'"
					//. " AND p1.post_type = 'attachment'"
					//. " AND p1.post_status = 'inherit'"
					. " WHERE p1.post_status = 'inherit'"
					. " AND $wpdb->posts.post_status = 'publish'"
					. " AND $wpdb->term_taxonomy.term_id IN (". $wpdb->escape( $this->v_term_id ) .")"
					. " AND p1.post_title LIKE '". $wpdb->escape( $this->v_str ) ."'"
					. " ORDER BY ". $wpdb->escape( $this->v_image_order_by ) ." ". $wpdb->escape( $this->v_image_order ) .") AS m1"
					. " GROUP BY parent_article_id"
					. " ORDER BY ". $wpdb->escape( $this->v_order_by ) ." ". $wpdb->escape( $this->v_order )
					. " LIMIT ". $wpdb->escape( $this->v_limit );
			// output query 
			//echo $query2;
			$query2_array = $wpdb->get_results($query2, ARRAY_A);
			// query2_array[ROW][ image_post_id / parent_article_id / post_title / post_date ]
			
			//var_dump($query2_array);
		}
		// Wordpress function, but only gets the returned rows, the SQL command gets all rows for paging
		//$row_count = $wpdb->num_rows;
		//echo $row_count;
		// get the number of rows without limit and without Wordpress's function.
		$query_count = "SELECT FOUND_ROWS();";
		$row_count = $wpdb->get_var($query_count);
		//echo $row_count;

		if( is_array($query_array) || is_array($query2_array) ) {
			if( $this->v_first_image_mode == 'off' ) return $query_array;
			if( $this->v_count_only == 'on' && $this->v_first_image_mode == 'on') return $row_count;
			if( $this->v_first_image_mode == 'on' ) return $query2_array;
		} else {
			return false;
		}
		
	}
	
	
	
	function image_archives_core () {
		
		if( $this->v_count_only == 'on' ) {
			
			return $this->image_total_count();	
		}
		
		
		if( $this->v_cache == 'on' ) {
			
			$this->image_archives_settings_write();
			$this->image_archives_cache_file ( $c_dir, $c_file );
			
			if( file_exists($c_file) ) {
				
				$content = file_get_contents($c_file);
				return $content;
				
			} else {
				
				$this->image_archives_cache_create();
				$content = file_get_contents($c_file);
				return $content;
				
			}
			
		} else {
		
			return $this->image_archives_output();
		
		}
		
	}
	
	
	
	function image_archives_cache_file ( &$cache_dir, &$cache_file ) {
		
		$cache_dir = WP_PLUGIN_DIR . '/image-archives/cache';
		
		$str =	  $this->v_first_image_mode
				. $this->v_image_order_by
				. $this->v_image_order
				. $this->v_term_id
				. $this->v_order_by
				. $this->v_order
				. $this->v_str
				. $this->v_limit
				. $this->v_img_size
				. $this->v_item
				. $this->v_column
				. $this->v_design
				. $this->v_date_format
				. $this->v_date_show
				. $this->v_title_show
				. $this->v_cache
				. $this->v_section_name
				. $this->v_section_sort
				. $this->v_section_result_number_show;
		
		$md5 = md5($str);
		
		$cache_file = $cache_dir . '/ia-' . $md5;
		
	}
	
	
	
	function image_archives_cache_create () {
		
		$this->image_archives_cache_file ( $c_dir, $c_file );
		
		// create cache dir
		if( !is_dir( $c_dir ) ) {
			if( !mkdir( $c_dir , 0755 ) ) echo 'failed to creat cache dir.';
		}
		
		if( $fp = fopen( $c_file, 'w' ) ) {
			flock( $fp, LOCK_EX );
				fwrite( $fp, $this->image_archives_output() );
			flock( $fp, LOCK_UN );
			fclose( $fp );
		} else {
			echo 'a cache file cannot be opened or be created.';
		}
		
	}
	
	
	
	function image_archives_cache_update () {
		
		/*	image_archives_cache_update() が add_action によって直接呼び出される時、
		*	$this->v_* に何も値が入っていない。Wordpress のデータベースに値を保存する手段もあるが
		*	それをしてしまうと Image Archives を同じページで二度呼べなくなる。この解決方法として
		*	settings.ini を作り、それを読み出す様にした。
		*	PHPをセーフモードで使っているとこのプラグインが使えないだろう。
		*/
		
		if( $this->image_archives_settings_read() == false ) return;
		
		$this->image_archives_cache_file ( $c_dir, $c_file );
		
		if( $this->v_cache == 'on' && file_exists($c_file) ) $this->image_archives_cache_create();
		
	}
	
	function image_total_count() {
		
		//send query
		$arr = $this->image_archives_query( $count );
		if (!$arr ) return "Query Error. Searching your database was done, but any images were not found. Your 'str'(search strings) may be wrong or your input 'term_id' doesn't exist, or 'limit' may be wrong.";
	
		return $count;
	
	
	}
	
	function image_archives_output () {
		
		//send query
		$arr = $this->image_archives_query( $count );
		if( !$arr ) return "Query Error. Searching your database was done, but any images were not found. Your 'str'(search strings) may be wrong or your input 'term_id' doesn't exist, or 'limit' may be wrong.";

	
		// OUTPUT
				
		$output = "<table class='img_arc'><tbody>\n";

		for( $i=0; $arr[$i] != NULL; $i++ ) {
			$my_post = & get_post( $arr[$i][parent_article_id]);
			$my_content = $my_post->post_content;
			
			$ojjd = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $my_post->post_content, $matches);
			$img_src = $matches [1] [0];
			$img_src = site_url() . "/thumb.php?src=" . $img_src . "&w=160&h=160&zc=1" ;

			//$img_src = wp_get_attachment_image_src( 236, $this->v_img_size );
			//$img_src = '/thumb.php?src=';
			if( $i % $this->v_column == 0 ) $output .= "  <tr>\n";
			$output .= "    <td>\n"
					.  "      <div class='img_arc_img'><a href='". get_permalink($arr[$i][parent_article_id]) ."'><img src='$img_src' alt='". attribute_escape($arr[$i][post_title]) ."' title='". attribute_escape($arr[$i][post_title]) ."' /></a></div>\n";
			if( $this->v_title_show == 'on' ) $output .= "      <div class='img_arc_text'><a href='". get_permalink($arr[$i][parent_article_id]) ."'>". $arr[$i][post_title] ."</a><br>";
			if( $this->v_date_show == 'on' )  $output .= "      Date: ". date( "$this->v_date_format", strtotime($arr[$i][post_date]) ) ."<br>";
			$output .= $arr[$i][comment_count] ." comments</div>\n";
			$output	.= "    </td>\n";
			
			if( $i % $this->v_column == $this->v_column - 1 ) $output .= "  </tr>\n";
		}
		
		// $i はループ終了後 +1 されている
		$i = $i -1;
		if ( $i % $this->v_column != $this->v_column - 1 ) $output .= "  </tr>\n";
		
		$output .= "</tbody></table>\n";
		
		return $output;
		
	}

}


$image_archives = new image_archives();


/******************************************************************************
 * grobal template tag - wp_image_archives()
 *****************************************************************************/

function wp_image_archives ( $args = '' ) {
	global $image_archives;
	return $image_archives->image_archives_template_tag ( $args );
}



/******************************************************************************
 * shortcode - [image_archives]
 *****************************************************************************/

add_shortcode( 'image_archives', array( $image_archives, 'image_archives_shortcode' ) );



/******************************************************************************
 * add_action hook
 *****************************************************************************/

add_action( 'wp_insert_post', array( $image_archives, 'image_archives_cache_update' ) );


?>