<?php

/**
 * @package Alpha Channel Group Base Theme
 * @author Alpha Channel Group (www.alphachannelgroup.com)
 */

if ( function_exists('register_nav_menus' )) {
	register_nav_menus(
		array(
			'primary'	=> 'Primary Navigation Menu'
		)
	);
}


// Function to clean up the standard wp_list_pages returned values
function acg_wp_list_pages($args) {
	$echo = false;
	if ( ! is_array($args)) {
		$pairs = explode("&", $args);
		unset($args);
		foreach($pairs as $k=>$v) {
			$pair = explode("=", $v);
			$args[$pair[0]] = $pair[1];
		}
	}
	
	if ( ! isset($args["echo"]) || $args["echo"]!=0) {
		$args["echo"]=0;
		$echo = true;
	}
	
	if (isset($args["image"]) || isset($args["useimages"]) || isset($args["image_dir"])) {
		acg_image_menu($args);
		return;
	}
	
	if (empty($args['walker'])) {
		$args['walker'] = new acg_classify_menu;
	}
	
	// Change here to get the nav menu...
	// Function to clean up the standard wp_nav_menu returned values
	$str = wp_nav_menu($args);
	$pos = stripos($str, "<li") + 1;
	$pos = stripos($str, "class=", $pos) + 7;
	$str = substr($str, 0, $pos) . 'first_page ' . substr($str, $pos);
	$str = str_replace(array("\n", "\r", "\t"), "", $str);
	// Strip out the ul container
	$pos = stripos($str, "<ul");
	if ($pos!==false) {
		$end = stripos($str, ">", $pos);
		$str = substr($str, 0, $pos) . substr($str, $end+1);
		$pos = strripos($str, "</ul>");
		$str = substr($str, 0, $pos) . substr($str, $pos+5);
	}
	// Strip out the div container
	$pos = stripos($str, "<div");
	if ($pos!==false) {
		$end = stripos($str, ">", $pos);
		$str = substr($str, 0, $pos) . substr($str, $end+1);
		$pos = strripos($str, "</div>");
		$str = substr($str, 0, $pos) . substr($str, $pos+6);
	}
	if ($echo) {
		echo $str;
	} else {
		return $str;
	}
}

class acg_classify_menu extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth=0, $args = Array(), $id = 0) {
		global $wp_query;
		$args = (array)$args;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		
		$class_names = $value = '';
		
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		$classes[] = 'menu-' . strtolower(preg_replace('/[^\da-z]/i', '-', $item->title));
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';
		
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
		
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		
		$prepend = '';
		$append = '';
		
		$item_output = $args['before'];
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args['link_before'] . $prepend . apply_filters( 'the_title', $item->title, $item->ID ) . $append;
		$item_output .= $args['link_after'];
		$item_output .= '</a>';
		$item_output .= $args['after'];
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}


function acg_image_menu($args) {
	$str = wp_nav_menu($args);
	$oldmenu = str_replace("\r", "", str_replace("\n", "", $str));
	// Parse this string to do image replacement
	$count = 0;
	$separator = "";
	$imgdir = (isset($args["image_dir"])) ? $args["image_dir"] . "/" : "";
	if (isset($args["separator"])) {
		$separator = $args["separator"];
		if (stripos($separator, ".")!==false) {
			$separator = '<img src="' . TEMPLATE_URL . "/images/" . $imgdir . $separator . '" alt="|">';
		}
	} 
	if (isset($args["title"])) {
		$title = $args["title"];
		if (stripos($title, ".")!==false) {
			$title = '<img src="' . TEMPLATE_URL . "/images/" . $imgdir . $title . '" alt= "title">';
			$oldmenu = substr($oldmenu, 0, stripos($oldmenu, ">")+1) . $title . substr($oldmenu, stripos($oldmenu, ">")+1);
		}
	} 
	$offset = 0;
	$last = substr_count($oldmenu, "</a>");
	while(stripos($oldmenu, '</a>', $offset)!==false) {
		$end = stripos($oldmenu, '</a>', $offset);
		$start = strrpos(substr($oldmenu, 0, $end), ">", $offset)+1;
		if ($start==1) {break;}
		$anchor = substr($oldmenu, $start, $end-$start);
		global $post;
		// Stuff for on-state of LEFT NAV
		$this_page = strtolower(get_the_title($post->ID));
		if ($template=="index.php" && strtolower($anchor)==$this_page) {
			$class = "current";
		}
		
		$class = ($class) ? ' class="' . $class . '"' : '';
		$onstate = ($class) ? "-on" : "";
		$newanchor = acg_image_replacement($anchor, "nav");
		$oldmenu = substr($oldmenu, 0, $start) . $newanchor . '</a>' . substr($oldmenu, $end+4);
		$offset = $start + strlen($newanchor) + 7;
		if ($count++ > 25) {
			break;
		}
	}
	echo $oldmenu;
}

function acg_image_replacement($title, $imgdir) {
	$imgdir.= ($imgdir) ? "/" : "";
	$imgdir = str_replace("//", "/", $imgdir);
	$img = strtolower(str_replace(" ", "-", strtolower($title))) . $onstate . ".png";
	$imgloc = TEMPLATE_URL .  "/images/" . $imgdir . $img;
	$file = acg_get_path("images/" . $imgdir) . $img;
	if (file_exists($file)) {
		$newanchor = '<img ' . $class . ' src="' . $imgloc . '" alt="' . $title . '">';
	} else {
		$newanchor = $title;
	}
	return $newanchor;
}

function acg_get_path($folder) {
	$path = $_SERVER["SCRIPT_FILENAME"];
	if (stripos($path, "wp-admin")!==false) {
		$path = substr($path, 0, stripos($path, "wp-admin")-1);
	} else {
		$path = substr($path, 0, stripos($path, "index.php")-1);
	}
	$url = get_bloginfo("template_url");
	$url = substr($url, stripos($url, "/wp-content"));
	$dir = $path . $url . "/" . $folder;
	return $dir;
}

?>