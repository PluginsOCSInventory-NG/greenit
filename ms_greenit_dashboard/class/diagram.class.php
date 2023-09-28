<?php

class Diagram {

    public function createCanvas(string $canvasName, string $nbColumn, string $height){
        ?>
        <div class='col-md-<?= $nbColumn ?>'>
            <canvas id="<?= $canvasName ?>" width="500" height="<?= $height ?>"/>
        </div>
        <?php
    }

    public function createBarChart(string $canvasName, array $labels, array $datasets){
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: 'bar',
                options: {
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
                        text: '<?= $title ?>'
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

            console.log(config);
    
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
                        text: '<?= $title ?>'
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

            console.log(config);
    
            var ctx = document.getElementById("<?= $canvasName ?>").getContext("2d");
            window.mySNMP = new Chart(ctx, config);
        </script>
        <?php
    }
}

?>