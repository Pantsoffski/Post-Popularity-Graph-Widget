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
	if ($wpdb->query("SELECT post_id FROM $post_popularity_graph_table WHERE post_id = $postID")) {
		$result = $wpdb->get_results("SELECT COUNT(post_id) FROM $post_popularity_graph_table WHERE post_id = $postID GROUP BY CAST(date AS DATE)", ARRAY_A);
		$date = $wpdb->get_results("SELECT CAST(date AS DATE) FROM $post_popularity_graph_table WHERE post_id = $postID GROUP BY CAST(date AS DATE)", ARRAY_A);
	}

/*	foreach($result as $key => $row){
		echo $row['COUNT(post_id)']."<br>";
		static $i = 0;
		echo $date[$i]['CAST(date AS DATE)']."<br>";
		$i++;
	}*/
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
		foreach ($result as $key => $row) {
			static $i = 0;
			$value = $date[$i]['CAST(date AS DATE)'];
			++$i;
			$a = preg_replace("|(\d{4})\-(\d{2})-(\d{2})|", "$1", $value);
			$b = preg_replace("|(\d{4})\-(\d{2})-(\d{2})|", "$2", $value);
			$b = (int)$b - 1;
			$c = preg_replace("|(\d{4})\-(\d{2})-(\d{2})|", "$3", $value);
			$value = $row['COUNT(post_id)'];
			echo "[new Date(".(int)$a.", ".$b.", ".(int)$c."), ".(int)$value."],";
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