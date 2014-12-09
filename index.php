<?php
/**
 * User: Robin Taylor
 * Date: 04/11/2014
 * Time: 11:42
 */

include_once('GateFileReader.php');

$fileReader = new GateFileReader("/Users/rtaylor3/MainLibrary.csv",",");
$fileReader->readFile();

$allGatesHours = makeDataTable($fileReader->hours_all);
$mainGateHours = makeDataTable($fileReader->hours_main_gate);
$cafeGateHours = makeDataTable($fileReader->hours_cafe_gate);
$hubGateHours = makeDataTable($fileReader->hours_hub_gate);

$stackedHours = stackCafeMainEntrants($fileReader->hours_main_gate, $fileReader->hours_cafe_gate);
$stackedCollegeEntrants = stackCollegeEntrants($fileReader->hours_all_sce, $fileReader->hours_all_hss, $fileReader->hours_all_mvm);



function makeDataTable($array) {
    $dataTable = '[';
    foreach ($array as $key => $value) {
        $dataTable .= '['.$key.','.$value.'],';
    }
    $dataTable .= ']';
    return $dataTable;
}

function stackCafeMainEntrants($array1, $array2) {
    //$dataTable = '[';
    $dataTable = "[['Genre','Main Gate','Cafe Gate',{ role: 'annotation' }],";

    for ($i = 0; $i < count($array1); $i++) {
        //$dataTable .= '['.$i.','.$array1[$i].','.$array2[$i].'],';
        $dataTable .= "['$i',$array1[$i],$array2[$i],''],";
    }

    //foreach ($array as $key => $value) {
    //    $dataTable .= '['.$key.','.$value.'],';
   // }
    $dataTable .= ']';
    return $dataTable;
}

function stackCollegeEntrants($array1, $array2, $array3) {
    $dataTable = "[['Genre','SCE','HSS','MVM' ,{ role: 'annotation' }],";

    for ($i = 0; $i < count($array1); $i++) {
        $dataTable .= "['$i',$array1[$i],$array2[$i],$array3[$i],''],";
    }

    $dataTable .= ']';
    return $dataTable;
}

function printArray($array) {
    foreach($array as $key => $value) {
        print_r("key is ".$key." ,value is ".$value."</br>");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gate Stats</title>

    <!-- Latest compiled and minified Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">


</head>
<body>

<div class="container-fluid">

    <h1>Gate Stats for period - 20/10/14 to 26/10/14</h1>

    <div class="row">

        <div class="col-md-4">
            <h3>Entrants by hour using main and cafe gates</h3>

              <?php
                foreach ($fileReader->hours_all as $key => $value) {
                    echo "Hour ".$key." = ".$value."</br>";
                }
              ?>
        </div>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable(<?php echo $stackedHours ?>);

                // Crude method of calculating an appropriate height in pixels
                //var size = data.getNumberOfRows();
                //var height = size * 40;

                var options = {
                    legend: { position: 'top', maxLines: 3 },
                    //legend: 'none',
                    //height: height
                    width: 1200,
                    height: 600,
                    isStacked: true
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('pageViews_div1'));

                chart.draw(data, options);
            }
        </script>

        <div id="pageViews_div1" class="col-md-8"></div>

    </div> <!-- row -->

    <div class="row">
        <div class="col-md-4">
            <h3>Entrants by college</h3>

            <?php
            foreach ($fileReader->collegeArray as $key => $value) {
                echo $key." = ".$value."</br>";
            }
            ?>
        </div>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable(<?php echo $stackedCollegeEntrants ?>);

                // Crude method of calculating an appropriate height in pixels
                //var size = data.getNumberOfRows();
                //var height = size * 40;

                var options = {
                    legend: { position: 'top', maxLines: 3 },
                    //legend: 'none',
                    //height: height
                    width: 1200,
                    height: 600,
                    isStacked: true
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('pageViews_div2'));

                chart.draw(data, options);
            }
        </script>

        <div id="pageViews_div2" class="col-md-8"></div>

    </div> <!-- row -->

    <div class="row">
        <div class="col-md-12">
            <h3>Entrant category</h3>

            <?php
            foreach ($fileReader->entrantCategory as $key => $value) {
                echo $key." = ".$value."</br>";
            }
            ?>

        </div>
    </div> <!-- row -->

    <div class="row">
        <div class="col-md-8">
            <br />
            <p>
                VIS - Visitor, STF - Staff, UGN - UG New Entrant, UGR - UG Returner, PGN - PG New Entrant, PGR - PG Returner, NGN - Non-Grad New Entrant, PGE - PGDE New Entrant, PTM - Part-time Modular Masters
            </p>
        </div>
    </div> <!-- row -->

    <div class="row">
        <div class="col-md-12">
            <h3>Entrants by postcode</h3>

            <?php

            //echo "Size of array is ".count($fileReader->postcodeArray)."</br>";


            $i = 0;
            foreach ($fileReader->postcodeArray as $key => $value) {

                if ($i > 29) {
                    break;
                } else {
                    $i++;
                }
                echo $key." = ".$value."</br>";
                //echo $key."</br>";
            }
            ?>


        </div>
    </div> <!-- row -->



</div> <!-- container -->

<div id="footer">
    <div class="container">
        <p>"Lies, damned lies, and statistics"</p>
    </div>
</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Latest compiled and minified Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>

</body>
</html>

