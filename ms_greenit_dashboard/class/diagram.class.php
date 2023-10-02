<?php

class Diagram {

    public $colorsList = array(
        "#1941A5", //Dark Blue
        "#AFD8F8",
        "#F6BD0F",
        "#8BBA00",
        "#A66EDD",
        "#F984A1",
        "#CCCC00", //Chrome Yellow+Green
        "#999999", //Grey
        "#0099CC", //Blue Shade
        "#FF0000", //Bright Red
        "#006F00", //Dark Green
        "#0099FF", //Blue (Light)
        "#FF66CC", //Dark Pink
        "#669966", //Dirty green
        "#7C7CB4", //Violet shade of blue
        "#FF9933", //Orange
        "#9900FF", //Violet
        "#99FFCC", //Blue+Green Light
        "#CCCCFF", //Light violet
        "#669900", //Shade of green
    );

    static public function generateColorList($nb){
        $self = new self();

        $string = "";
        for($i = 0; $i <= $nb; $i++)
        {
            $string .= "'".$self->colorsList[$i]."'";
            if ($i != $nb) $string .= ", ";
        }
        return $string;
    }

    public function createCanvas(string $canvasName, string $nbColumn, string $height){
        ?>
        <div class='col-md-<?= $nbColumn ?>'>
            <canvas id="<?= $canvasName ?>" width="500" height="<?= $height ?>"/>
        </div>
        <?php
    }

    public function createBarChart(string $canvasName, string $title, array $labels, array $datasets){
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: 'bar',
                options: {
                    title: {
                        display: true,
                        text: "<?= $title ?>"
                    },
                    legend: {
                        display: true,
                        position: 'right'
                    },
                    animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                    scales: {
                        yAxes: [{
                            ticks:{
                                beginAtZero:true
                            }
                        }]
                    },
                    responsive: true
                },
                data: {
                    labels: [
                        <?php
                            foreach($labels as $column)
                            {
                                echo $column.", ";
                            }
                        ?>
                    ],
                    datasets: [
                        <?php
                            foreach($datasets as $column)
                            {
                                echo "{\n";
                                foreach($column as $key => $setting)
                                {
                                    echo $key.": ".$setting.",";
                                }
                                echo "},\n";
                            }
                        ?>
                    ],
                }
            }
    
            var ctx = document.getElementById("<?= $canvasName ?>").getContext("2d");
            window.mySNMP = new Chart(ctx, config);
        </script>
        <?php
    }

    public function createDoughnutChart(string $canvasName, string $title, array $labels, array $datasets){
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: 'doughnut',
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: "<?= $title ?>"
                    },
                    legend: {
                        position: 'right'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                },       
                data: {
                    labels: [
                        <?php
                            foreach($labels as $column)
                            {
                                echo $column.", ";
                            }
                        ?>
                    ],
                    datasets: [
                        <?php
                            echo "{\n";
                                foreach($datasets as $key => $setting)
                                {
                                    echo $key.": ".$setting.",";
                                }
                            echo "},\n";
                        ?>
                    ],
                }
            }
    
            var ctx = document.getElementById("<?= $canvasName ?>").getContext("2d");
            window.mySNMP = new Chart(ctx, config);
        </script>
        <?php
    }

    public function createPieChart(string $canvasName, string $title, array $labels, array $datasets){
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: 'pie',
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: "<?= $title ?>"
                    },
                    legend: {
                        position: 'right'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                },                
                data: {
                    labels: [
                        <?php
                            foreach($labels as $column)
                            {
                                echo $column.", ";
                            }
                        ?>
                    ],
                    datasets: [
                        <?php
                            echo "{\n";
                                foreach($datasets as $key => $setting)
                                {
                                    echo $key.": ".$setting.",";
                                }
                            echo "},\n";
                        ?>
                    ],
                }
            }
    
            var ctx = document.getElementById("<?= $canvasName ?>").getContext("2d");
            window.mySNMP = new Chart(ctx, config);
        </script>
        <?php
    }
}

?>