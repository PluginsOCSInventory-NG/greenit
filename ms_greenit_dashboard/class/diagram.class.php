<?php

class Diagram {

    public function createCanvas(string $canvasName){
        ?>
        <div class='col-md-12'>
            <canvas id="<?= $canvasName?>" width="400" height="100"/>
        </div>
        <?php
    }

    public function createBarChart(string $canvasName, string $title, array $labels, array $labelsSettings){
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: 'bar',
                options: {
                    title: {
                        display: true,
                        text: "<?php echo $title ?>"
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
                            foreach($labelsSettings as $column)
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
}

?>