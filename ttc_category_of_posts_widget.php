<?php
/*
Plugin Name: TTC Category of Posts Widget
Version: 1.1
Plugin URI: http://herselfswebtools.com/2008/01/wordpress-widget-to-list-all-posts-in-a-single-category-in-your-sidebar.html
Author: Linda MacPhee-Cobb
Author URI: http://timestocome.com
Description: This widget creates a list of posts from a single category in your sidebar 
*/


// all functions go in this function this is like main
function ttc_category_of_posts_init() {

	//gracefully fail if sidebar gets deactivated
	if ( !function_exists('register_sidebar_widget') )
	return;
	
	//output the widget here
	function ttc_category_of_posts($args) 
	{
		//get user preferences
		extract($args);
		$options = get_option ( 'ttc_category_of_posts' );

		//theme preferences
		$title_text = $options['title'];
		
		//db preferences
		global $wpdb;
		$category_id = $options['category'];
		$table_prefix = $wpdb->prefix;
		
		$the_output = NULL;
		
		
		
		// this is where we fetch the posts and create a link list of posts
		/* fetch posts, categories from mysql and sort by category, then by post */
		$results =  (array)$wpdb->get_results("
		select ID, post_title, post_status, post_date, term_id, object_id, {$table_prefix}term_relationships.term_taxonomy_id, {$table_prefix}term_taxonomy.term_taxonomy_id  
		from {$table_prefix}posts, {$table_prefix}term_relationships, {$table_prefix}term_taxonomy 
		where ID = object_id 
		and {$table_prefix}term_relationships.term_taxonomy_id = {$table_prefix}term_taxonomy.term_taxonomy_id 
		and term_id = $category_id
		and taxonomy = 'category'
		and post_type != 'page'
		and post_status = 'publish' and post_date < NOW()
		order by post_title asc;	");
		
		

		//nothing to do if there is nothing here
		if (empty($results)) {
			return NULL;
		}
		

		
		// turn mysql results into html 
		$the_output .= stripslashes($ddle_header); 
		
		foreach ( $results as $posts ){
			$title = $posts->post_title;
			$category = $posts->name;
			$post_number = $posts->ID;
		
			$the_output .= '<li><a href="' .get_permalink($post_number) . '">' . $title . '</a></li>';
		}
  
		
		
		//output to sidebar
		echo $before_widget; 
		echo $before_title;
		echo $title_text;
		echo $after_title;
		

	    
		//now print widget output to webpage
		echo " $the_output ";
		echo "</li>";
		
		//clean up
		
	
	
		return $link;
		
	}

	//user options
	function ttc_category_of_posts_control() 
	{
	
		$options = get_option ( 'ttc_category_of_posts' );
		
		//set initial values if empty else fetch current
		if ( ! is_array($options) ){
			
			$options = array( 'title'=>"Topic Posts", 'thumbnails'=>"5", 'random'=>"0" );
		
		}else{	
		
			//fetch options
			$category = $options['category'];									// number of the category to display
			$title = $options['title'];											// title to show in sidebar 
		}
		
		
		//clean up and post
		if ( $_POST['ttc_category_of_posts-submit'] ) {
		
			//title
			$options['title'] = strip_tags(stripslashes($_POST['ttc_category_of_posts-title']));
			
			//number of thumbnails to display
			$options['category'] = (int) $_POST['ttc_category_of_posts-category'];
			
			//save user selections
			update_option('ttc_category_of_posts', $options);
		
		}
		
	
		
		// This is the form where we collect the user preferences
		// Notice that we don't need a complete form. This will be embedded into the existing form.
		echo '<p style="text-align:right;"><label for="ttc_category_of_posts-title">' . __('Title:') . ' <input style="width: 200px;" id="ttc_category_of_posts-title" name="ttc_category_of_posts-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="ttc_category_of_posts-category">' . __('Number of the category') . ' <input style="width: 20px;" id="ttc_category_of_posts-category" name="ttc_category_of_posts-category" type="text" value="'.$category.'" /></label></p>';
		
		//Input our form info if user presses save button
		echo '<input type="hidden" id="ttc_category_of_posts-submit" name="ttc_category_of_posts-submit" value="1" />';
	}

	//register widget so it is available to user in widget page
	register_widget_control(array('TTC Category of Posts', 'widgets'), 'ttc_category_of_posts_control', 300, 150);

	//register control panel so use can see it and use it
	register_sidebar_widget(array('TTC Category of Posts','widgets'), 'ttc_category_of_posts');
}

// go to main
add_action('widgets_init', 'ttc_category_of_posts_init');
?>
