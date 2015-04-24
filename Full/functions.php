<?php

//zbieranie danych
function add_hits($postID) {
	global $wpdb;
	$post_popularity_graph_table = $wpdb->prefix . 'post_popularity_graph';
	if (!preg_match('/bot|spider|crawler|slurp|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) { //jeśli nie istnieje rekord hit_count z podanym ID oraz ID nie jest równe 1 oraz odwiedzający nie jest botem
		$result = $wpdb->query("INSERT INTO $post_popularity_graph_table (post_id, date) VALUES ($postID, NOW())"); //dodaje do tablicy id postu, date oraz hit
		$wpdb->query("DELETE FROM $post_popularity_graph_table WHERE date <= NOW() - INTERVAL 30 DAY"); //removes database entry older than 30 days
	}
}

function show_graph($postID, $numberofdays, $chartstyle, $haxistitle, $vaxistitle, $backgroundcolor, $chartcolor) {
	global $wpdb;
	$post_popularity_graph_table = $wpdb->prefix . 'post_popularity_graph';
	if ($wpdb->query("SELECT post_id FROM $post_popularity_graph_table WHERE post_id = $postID")) {
		$result = $wpdb->get_results("SELECT COUNT(post_id) FROM $post_popularity_graph_table WHERE post_id = $postID AND date >= DATE(DATE_SUB(NOW(), INTERVAL $numberofdays DAY)) GROUP BY CAST(date AS DATE)", ARRAY_A);
		$date = $wpdb->get_results("SELECT CAST(date AS DATE) FROM $post_popularity_graph_table WHERE post_id = $postID AND date >= DATE(DATE_SUB(NOW(), INTERVAL $numberofdays DAY)) GROUP BY CAST(date AS DATE)", ARRAY_A);
		
//przedział dat - listowanie na podstawie $numberofdays
	$date1 = date("Y, m, d", strtotime("- $numberofdays day"));
	$date2 = date("Y, m, d");
	
	function returnDates($fromdate, $todate) {
		$fromdate = DateTime::createFromFormat('Y, m, d', $fromdate);
		$todate = DateTime::createFromFormat('Y, m, d', $todate);
    		return new DatePeriod(
        	$fromdate,
        	new DateInterval('P1D'),
        	$todate->modify('+1 day')
    		);
	}
	
	$datePeriod = returnDates($date1, $date2);
/*	foreach($datePeriod as $dateLoop) {
		$dateLoop = $dateLoop->format('Y, m, d');
		if(strstr($dateLoop, '2015, 03, 30')) {
			continue;
    		}else{
			echo $dateLoop, PHP_EOL;
		}
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
		foreach ($result as $key => $row) { //pętla wyświetlająca dane
			static $i2 = 0;
			$value1 = $date[$i2]['CAST(date AS DATE)'];
			++$i2;
			$value1 = DateTime::createFromFormat('Y-m-d', $value1);
			$compareDate = $value1->format('Y, m, d');
			$year = $value1->format('Y');
			$month = $value1->format('m');
			$month = $month - 1;
			$day = $value1->format('d');
			$value2 = $row['COUNT(post_id)'];
		}
		foreach($datePeriod as $dateLoop) { //pętla wyświetlająca zera, jeśli w zadanym okresie w konkretnych dniach nie ma danych
			$dateLoop1 = $dateLoop->format('Y, m, d');
			$yearLoop = $dateLoop->format('Y');
			$monthLoop = $dateLoop->format('m');
			$monthLoop = $monthLoop - 1;
			$dayLoop = $dateLoop->format('d');
			if(strstr($dateLoop1, $compareDate)) {
				echo "[new Date(".$year.", ".$month.", ".$day."), ".$value2."],";
    			}else{
				echo "[new Date(".$yearLoop.", ".$monthLoop.", ".$dayLoop."), 0],";
			}
		}
?>
      ]);

      var options = {
        hAxis: {
          title: "<?php echo $haxistitle; ?>",
          textPosition: 'none'
        },
        vAxis: {
          title: "<?php echo $vaxistitle; ?>"
        },
        legend: {
        	position: 'none'
        },
        chartArea: {
			left: '15%',
			top: '3%',
			width: '90%',
			height: '90%'
		},
		curveType: 'function',
		width: '100%',
		height: '100%',
		backgroundColor: "<?php echo $backgroundcolor; ?>",
		colors: ["<?php echo $chartcolor; ?>"]
      };

      var chart = new google.visualization.<?php echo $chartstyle; ?>(
        document.getElementById('ex0'));

      chart.draw(data, options);

    }
    </script>

	<div id="ex0" style="width: 100%;"></div>

<?php
	}
}

?>