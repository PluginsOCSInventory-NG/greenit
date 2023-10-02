<?php

class Diagram
{

    public $colorsList = array(
        "#1941A5",
        "#AFD8F8",
        "#F6BD0F",
        "#8BBA00",
        "#A66EDD",
        "#F984A1",
        "#CCCC00",
        "#999999",
        "#0099CC",
        "#FF0000",
        "#006F00",
        "#0099FF",
        "#FF66CC",
        "#669966",
        "#7C7CB4",
        "#FF9933",
        "#9900FF",
        "#99FFCC",
        "#CCCCFF",
        "#669900",
    );

    static public function generateColorList(int $nb, bool $arrayMode = false)
    {
        if ($arrayMode == false) {
            $self = new self();

            $string = "";
            for ($i = 0; $i <= $nb - 1; $i++) {
                $string .= "'" . $self->colorsList[$i] . "'";
                if ($i != $nb)
                    $string .= ", ";
            }
            return $string;
        } else if ($arrayMode == true) {
            $self = new self();

            $array = array();
            for ($i = 0; $i <= $nb - 1; $i++) {
                $color = "'" . $self->colorsList[$i] . "'";
                array_push($array, $color);
            }
            return $array;
        }
    }

    public function createCanvas(string $canvasName, string $nbColumn, string $height)
    {
        ?>
        <div class='col-md-<?= $nbColumn ?>'>
            <canvas id="<?= $canvasName ?>" width="500" height="<?= $height ?>" />
        </div>
        <?php
    }

    public function createBarChart(string $canvasName, string $title, array $labels, array $datasets)
    {
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
                        position: 'top'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    responsive: true
                },
                data: {
                    labels: [
                        <?php
                        foreach ($labels as $column) {
                            echo $column . ", ";
                        }
                        ?>
                    ],
                    datasets: [
                        <?php
                        foreach ($datasets as $column) {
                            echo "{\n";
                            foreach ($column as $key => $setting) {
                                echo $key . ": " . $setting . ",";
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

    public function createDoughnutChart(string $canvasName, string $title, array $labels, array $datasets)
    {
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
                        position: 'bottom'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                },
                data: {
                    labels: [
                        <?php
                        foreach ($labels as $column) {
                            echo $column . ", ";
                        }
                        ?>
                    ],
                    datasets: [
                        <?php
                        echo "{\n";
                        foreach ($datasets as $key => $setting) {
                            echo $key . ": " . $setting . ",";
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

    public function createPieChart(string $canvasName, string $title, array $labels, array $datasets)
    {
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
                        position: 'bottom'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                },
                data: {
                    labels: [
                        <?php
                        foreach ($labels as $column) {
                            echo $column . ", ";
                        }
                        ?>
                    ],
                    datasets: [
                        <?php
                        echo "{\n";
                        foreach ($datasets as $key => $setting) {
                            echo $key . ": " . $setting . ",";
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