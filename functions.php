<?php

//zbieranie danych
function add_views($postID) {
	global $wpdb;
	$post_popularity_graph_table = $wpdb->prefix . 'post_popularity_graph';
	if (!preg_match('/bot|spider|crawler|slurp|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) { //jeśli nie istnieje rekord hit_count z podanym ID oraz ID nie jest równe 1 oraz odwiedzający nie jest botem
		$result = $wpdb->query("INSERT INTO $post_popularity_graph_table (post_id, date) VALUES ($postID, NOW())"); //dodaje do tablicy id postu, date oraz hit
	}
} 

function show_graph($postID, $posnumber, $numberofdays, $ignoredpages, $ignoredcategories) {
	global $wpdb;
	$post_popularity_graph_table = $wpdb->prefix . 'post_popularity_graph';
	if ($wpdb->query("SELECT COUNT(post_id) FROM $post_popularity_graph_table WHERE post_id = $postID")) {
		$result = $wpdb->get_results("SELECT COUNT(post_id) FROM $post_popularity_graph_table WHERE post_id = $postID GROUP BY CAST(date AS DATE) ORDER BY date", ARRAY_A);
		$date = $wpdb->get_results("SELECT CAST(date AS DATE) FROM $post_popularity_graph_table WHERE post_id = $postID ORDER BY date", ARRAY_A);
	}
	foreach ($result[0] as $value) {
		echo "[$value";
	}
	foreach ($date[0] as $value) {
			$valu = preg_replace("@(\d{4})/-(\d{2})/-(\d{2})@", "dupa", $value); //zmień format daty tak, żeby pasowała do [new Date(2008, 0, 1), 1],
			echo ", $valu]";
		}
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
                 google.load('visualization', '1', {packages: ['corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('date', 'Date');
      data.addColumn('number', 'Visits');

      data.addRows([
<?php
		foreach ($date[0] as $value) {
			$value = preg_replace("@(\d{4})/-(\d{2})/-(\d{2})@", "$1, $2, $3", $value);
			echo "[new Date($value)";
		}
		foreach ($result[0] as $value) {
			echo ", $value]";
		}
?>
      ]);

      var options = {
        hAxis: {
          title: 'Time',
        },
        vAxis: {
          title: 'Visits'
        },
        legend: {
        	position: 'none'
        },
        chartArea: {
			left: 15,
			top: 2,
			width: '85%',
			height: '85%' 
		},
		curveType: 'function'
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('ex0'));

      chart.draw(data, options);

    }
    </script>

	<div id="ex0" style="width: 100%;"></div>

<?php
}

function choose_style($css_sel) {
	if($css_sel == 1){
		return 'style-popular-posts-statistics-1.css';
	}
}

?>