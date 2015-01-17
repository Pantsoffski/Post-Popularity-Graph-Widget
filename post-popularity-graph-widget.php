<?php
/*
Plugin Name: Post Popularity Graph Widget
Plugin URI: http://smartfan.pl/
Description: Widget which displays popularity graph for every post.
Author: Piotr Pesta
Version: 0.5
Author URI: http://smartfan.pl/
License: GPL12
*/

include 'functions.php';

$options = get_option('post-popularity-graph-widget');

register_activation_hook(__FILE__, 'post_popularity_graph_widget_activate'); //akcja podczas aktywacji pluginu
register_uninstall_hook(__FILE__, 'post_popularity_graph_widget_uninstall'); //akcja podczas deaktywacji pluginu

// instalacja i zak³adanie tabeli w mysql
function post_popularity_graph_widget_activate() {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'post_popularity_graph';
		$wpdb->query("CREATE TABLE IF NOT EXISTS $popular_posts_statistics_table (
		id BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		post_id BIGINT(50) NOT NULL,
		hit_count BIGINT(50),
		date DATETIME
		);");
}

// podczas odinstalowania - usuwanie tabeli
function post_popularity_graph_widget_uninstall() {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
	delete_option('widget_popular_posts_statistics');
	$wpdb->query( "DROP TABLE IF EXISTS $popular_posts_statistics_table" );
}

class popular_posts_statistics extends WP_Widget {

// konstruktor widgetu
function popular_posts_statistics() {

	$this->WP_Widget(false, $name = __('Popular Posts Statistics', 'wp_widget_plugin'));

}

// tworzenie widgetu, back end (form)
function form($instance) {

// nadawanie i ³¹czenie defaultowych wartoœci
	$defaults = array('cleandatabase' => '', 'visitstext' => 'visit(s)', 'ignoredcategories' => '', 'ignoredpages' => '', 'hitsonoff' => '1', 'cssselector' => '1', 'numberofdays' => '7', 'posnumber' => '5', 'title' => 'Popular Posts By Views In The Last 7 Days');
	$instance = wp_parse_args( (array) $instance, $defaults );
?>

<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
	<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
</p>

<p>
<label for="<?php echo $this->get_field_id('posnumber'); ?>">Number of positions:</label>
<select id="<?php echo $this->get_field_id('posnumber'); ?>" name="<?php echo $this->get_field_name('posnumber'); ?>" value="<?php echo $instance['posnumber']; ?>" style="width:100%;">	
	<option value="2" <?php if ($instance['posnumber']==2) {echo "selected";} ?>>1</option>
	<option value="3" <?php if ($instance['posnumber']==3) {echo "selected";} ?>>2</option>
	<option value="4" <?php if ($instance['posnumber']==4) {echo "selected";} ?>>3</option>
	<option value="5" <?php if ($instance['posnumber']==5) {echo "selected";} ?>>4</option>
	<option value="6" <?php if ($instance['posnumber']==6) {echo "selected";} ?>>5</option>
	<option value="7" <?php if ($instance['posnumber']==7) {echo "selected";} ?>>6</option>
	<option value="8" <?php if ($instance['posnumber']==8) {echo "selected";} ?>>7</option>
	<option value="9" <?php if ($instance['posnumber']==9) {echo "selected";} ?>>8</option>
	<option value="10" <?php if ($instance['posnumber']==10) {echo "selected";} ?>>9</option>
	<option value="11" <?php if ($instance['posnumber']==11) {echo "selected";} ?>>10</option>
</select>
</p>

<p>
<label for="<?php echo $this->get_field_id('numberofdays'); ?>">Include posts that where visited in how many last days?</label>
<select id="<?php echo $this->get_field_id('numberofdays'); ?>" name="<?php echo $this->get_field_name('numberofdays'); ?>" value="<?php echo $instance['numberofdays']; ?>" style="width:100%;">
	<option value="1" <?php if ($instance['numberofdays']==1) {echo "selected";} ?>>1</option>
	<option value="2" <?php if ($instance['numberofdays']==2) {echo "selected";} ?>>2</option>
	<option value="3" <?php if ($instance['numberofdays']==3) {echo "selected";} ?>>3</option>
	<option value="4" <?php if ($instance['numberofdays']==4) {echo "selected";} ?>>4</option>
	<option value="5" <?php if ($instance['numberofdays']==5) {echo "selected";} ?>>5</option>
	<option value="6" <?php if ($instance['numberofdays']==6) {echo "selected";} ?>>6</option>
	<option value="7" <?php if ($instance['numberofdays']==7) {echo "selected";} ?>>7</option>
</select>
</p>

<p>
<input type="checkbox" id="<?php echo $this->get_field_id('hitsonoff'); ?>" name="<?php echo $this->get_field_name('hitsonoff'); ?>" value="1" <?php checked($instance['hitsonoff'], 1); ?>/>
<label for="<?php echo $this->get_field_id('hitsonoff'); ?>">Show hit count number?</label>
</p>

<p>
	<label for="<?php echo $this->get_field_id('ignoredpages'); ?>">If you would like to exclude any pages from being displayed, you can enter the Page IDs (comma separated, e.g. 34, 25, 439):</label>
	<input id="<?php echo $this->get_field_id('ignoredpages'); ?>" name="<?php echo $this->get_field_name('ignoredpages'); ?>" value="<?php echo $instance['ignoredpages']; ?>" style="width:100%;" />
</p>

<p>
	<label for="<?php echo $this->get_field_id('ignoredcategories'); ?>">If you would like to exclude any categories from being displayed, you can enter the Category IDs (comma separated, e.g. 3, 5, 10):</label>
	<input id="<?php echo $this->get_field_id('ignoredcategories'); ?>" name="<?php echo $this->get_field_name('ignoredcategories'); ?>" value="<?php echo $instance['ignoredcategories']; ?>" style="width:100%;" />
</p>

<p>
<label for="<?php echo $this->get_field_id('cssselector'); ?>">Style Select:</label>
<select id="<?php echo $this->get_field_id('cssselector'); ?>" name="<?php echo $this->get_field_name('cssselector'); ?>" value="<?php echo $instance['cssselector']; ?>" style="width:100%;">	
	<option value="1" <?php if ($instance['cssselector']==1) {echo "selected";} ?>>Style no. 1 (color bars)</option>
	<option value="2" <?php if ($instance['cssselector']==2) {echo "selected";} ?>>Style no. 2 (color bars + text with white outline)</option>
	<option value="3" <?php if ($instance['cssselector']==3) {echo "selected";} ?>>Style no. 3 (grey numbered list)</option>
	<option value="4" <?php if ($instance['cssselector']==4) {echo "selected";} ?>>Style no. 4 (grey numbered list)</option>
	<option value="5" <?php if ($instance['cssselector']==5) {echo "selected";} ?>>Custom Style (custom.css)</option>
</select>
</p>

<p>
	<label for="<?php echo $this->get_field_id('visitstext'); ?>">If you would like to change "visit(s)" text, you can do it here:</label>
	<input id="<?php echo $this->get_field_id('visitstext'); ?>" name="<?php echo $this->get_field_name('visitstext'); ?>" value="<?php echo $instance['visitstext']; ?>" style="width:100%;" />
</p>

<p>
<input type="checkbox" id="<?php echo $this->get_field_id('cleandatabase'); ?>" name="<?php echo $this->get_field_name('cleandatabase'); ?>" value="1" <?php checked($instance['cleandatabase'], 1); ?>/>
<label for="<?php echo $this->get_field_id('cleandatabase'); ?>"><b>Delete all widget collected data?</b> (Check it only if you feel that database data is too large and makes widget run slow!)</label>
</p>

<?php

}

function update($new_instance, $old_instance) {
$instance = $old_instance;

// Dostêpne pola
$instance['title'] = strip_tags($new_instance['title']);
$instance['posnumber'] = strip_tags($new_instance['posnumber']);
$instance['numberofdays'] = strip_tags($new_instance['numberofdays']);
$instance['cssselector'] = strip_tags($new_instance['cssselector']);
$instance['hitsonoff'] = strip_tags($new_instance['hitsonoff']);
$instance['ignoredpages'] = strip_tags($new_instance['ignoredpages']);
$instance['ignoredcategories'] = strip_tags($new_instance['ignoredcategories']);
$instance['visitstext'] = strip_tags($new_instance['visitstext']);
$instance['cleandatabase'] = strip_tags($new_instance['cleandatabase']);
return $instance;
}

// wyswietlanie widgetu, front end (widget)
function widget($args, $instance) {
extract($args);

// to s¹ funkcje widgetu
$title = apply_filters('widget_title', $instance['title']);
$posnumber = $instance['posnumber'];
$numberofdays = $instance['numberofdays'];
$cssselector = $instance['cssselector'];
$hitsonoff = $instance['hitsonoff'];
$ignoredpages = $instance['ignoredpages'];
$ignoredpages = trim(preg_replace('/\s+/', '', $ignoredpages));
$ignoredpages = explode(",",$ignoredpages);
$ignoredcategories = $instance['ignoredcategories'];
$ignoredcategories = trim(preg_replace('/\s+/', '', $ignoredcategories));
$ignoredcategories = explode(",",$ignoredcategories);
$visitstext = $instance['visitstext'];
$cleandatabase = $instance['cleandatabase'];
echo $before_widget;

if ($cleandatabase == 1){
	clean_up_database();
	$update_options = get_option('widget_popular_posts_statistics');
	$update_options[2]['cleandatabase'] = '';
	update_option('widget_popular_posts_statistics', $update_options);
}

// Sprawdzanie, czy istnieje tytu³
if ($title) {
echo $before_title . $title . $after_title;
}

$postID = get_the_ID();

echo '<div id="pp-container">';
show_views($postID, $posnumber, $numberofdays, $hitsonoff, $ignoredpages, $ignoredcategories, $visitstext);
echo '</div>';

add_views($postID);

echo $after_widget;
}
}

// rejestracja widgetu
add_action('widgets_init', create_function('', 'return register_widget("popular_posts_statistics");'));

add_action('wp_enqueue_scripts', function () {
	$css_select = get_option('widget_popular_posts_statistics'); //pobieranie opcji z bazy danych
	$css_sel = array();
	foreach($css_select as $css_selector){
		$css_sel[] = $css_selector['cssselector'];
	}
	wp_enqueue_style('popular_posts_statistics', plugins_url(choose_style($css_sel[0]), __FILE__)); //nazwa pliku uzale¿niona od funkcji i aktualnie obowi¹zuj¹cej opcji
    });

?>