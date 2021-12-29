<?php


function fav() { 
	{ ?><link rel="shortcut icon" href="/S_favicon.png" ><?php }
}
add_action('wp_head', 'fav');

/* Auto adding posts as submenu items to the menu */
add_filter( 'wp_get_nav_menu_items', 'my_theme_doctors_menu_filter', 10, 3 );
function my_theme_doctors_menu_filter( $items, $menu, $args ) {
  $child_items = array(); // here, we will add all items for the single posts
  $menu_order = count($items); // this is required, to make sure it doesn't push out other menu items
  $parent_item_id = 0; // we will use this variable to identify the parent menu item

  //First, we loop through all menu items to find the one we want to be the parent of the sub-menu with all the posts.
  foreach ( $items as $item ) {
    if ( in_array('rolunk-parent', $item->classes) ){
        $parent_item_id = $item->ID;
    }
  }
  if($parent_item_id > 0){
      foreach ( get_posts( 'post_type=rolunk&numberposts=-1' ) as $post ) {
        $post->menu_item_parent = $parent_item_id;
        $post->post_type = 'nav_menu_item';
        $post->object = 'custom';
        $post->type = 'custom';
        $post->menu_order = ++$menu_order;
        $post->title = $post->post_title;
        $post->url = get_permalink( $post->ID );
        array_push($child_items, $post);
      }
	  $reverse = array_reverse($child_items);
  }
  return array_merge( $items, $reverse );
}


/* CPT UI Custom Taxonomies Hiearchy */
function get_child_categories( $parent_category_id ){
    $html = '';
    $child_categories = get_categories( array( 'parent' => $parent_category_id, 'hide_empty' => false, 'taxonomy' => 'kategoria' ) );
    if( !empty( $child_categories ) ){
        $html .= '<ul>';
        foreach ( $child_categories as $child_category ) {
            $html .= '<li class="child">'.$child_category->name;
            $html .= get_child_categories( $child_category->term_id );
            $html .= '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

function list_categories(){
    $html = '';
    $parent_categories = get_categories( array( 'parent' => 0, 'hide_empty' => false, 'taxonomy' => 'kategoria' ) );
    $html.= '<ul>';
    foreach ( $parent_categories as $parent_category ) {
        $html .= '<li class="parent">'.$parent_category->name;
        $html .= get_child_categories( $parent_category->term_id  );
        $html .= '</li>';
    }
    $html.= '</ul>';
    return $html;
}
add_shortcode( 'list_categories', 'list_categories' );

/* Get hierarchical Taxonomy list of current post */
function get_child_taxcategories( $parent_category_id ){
    $html = '';
    $child_categories = get_categories( array( 'parent' => $parent_category_id, 'hide_empty' => false, 'taxonomy' => 'kategoria' ) );
    if( !empty( $child_categories ) ){
        foreach ( $child_categories as $child_category ) {
            $html .= $child_category->name.',';
        }
    }
    return $html;
}

function taxonomy_hierarchy() {
	global $post;
	$taxonomy = 'kategoria'; //Put your custom taxonomy term here
	$terms = wp_get_post_terms( $post->ID, $taxonomy );
	foreach ( $terms as $term ) {
		if ($term->parent == 0) // this gets the parent of the current post taxonomy
		{$myparent = $term;}
    }
	//echo '<strong>'.$myparent->name.'</strong>';
	if( !empty( $myparent ) ){
		echo ' '.get_child_taxcategories( $myparent->term_id  ).' ';
	}
	
	echo '_____________________';
	var_dump( $myparent );
}
add_shortcode( 'current_tax', 'taxonomy_hierarchy' );


function get_tax() {
	global $post;
	$taxonomy = 'kategoria'; //Put your custom taxonomy term here
	$terms = wp_get_post_terms( $post->ID, $taxonomy );
	foreach ( $terms as $term ) {
		if ($term->parent == 0) // this gets the parent of the current post taxonomy
		{$myparent = $term;}
    }
	echo ''.$myparent->name.'';	
}
add_shortcode( 'get_tax', 'get_tax' );

function get_child_tax() {
	global $post;
	$taxonomy = 'kategoria'; //Put your custom taxonomy term here
	$terms = wp_get_post_terms( $post->ID, $taxonomy );
	foreach ( $terms as $term ) {
		if ($term->parent == 0) // this gets the parent of the current post taxonomy
		{$myparent = $term;}
    }
	if( !empty( $myparent ) ){
		echo ' '.get_child_taxcategories( $myparent->slug  ).' ';
	}
}
add_shortcode( 'get_child_tax', 'get_child_tax' );

/* Add contact settings to Admin */
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Contact',
		'menu_title'	=> 'Kapcsolat infok',
		'menu_slug' 	=> 'kapcsolat',
		'capability'	=> 'edit_posts',
		'position' 		=> '0.3',
		'redirect'		=> false
	));
}

add_action( 'wp_enqueue_scripts', 'html5_blank_child_style' );
function html5_blank_child_style() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( 'html5blank' ),
        wp_get_theme()->get('Version') // this only works if you have Version in the style header
    );
}

?>
