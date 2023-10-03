<?php

class Diagram
{

    /**
     * List of color can be generate by the class
     */
    private $colorsList = array(
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

    /**
     * Generate a list of color and return it
     * 
     * @param int $nb Define the number of the color will be generated
     * @param bool $arrayMode Define the type of the return (string or array)
     * 
     */
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

    /**
     * Create a zone to draw charts
     * 
     * @param string $canvasName Define the name of the zone
     * @param string $nbColumn Define the width in bootstrap grid column
     * @param string $height Define the height of the zone (strange reaction with responsive charts)
     * 
     */
    public function createCanvas(string $canvasName, string $nbColumn, string $height)
    {
        ?>
        <div class='col-md-<?= $nbColumn ?>'>
            <canvas id="<?= $canvasName ?>" width="500" height="<?= $height ?>" />
        </div>
        <?php
    }

    /**
     * Create an horizontal or vertical bar chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $type Define if it will be a horizontal or vertical bar chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     */
    public function createBarChart(string $canvasName, string $type, string $title, array $labels, array $datasets)
    {
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: "<?= $type ?>",
                options: {
                    title: {
                        display: true,
                        text: "<?= $title ?>"
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    responsive: true,
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

    /**
     * Create a doughnut or pie chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     */
    public function createRoundChart(string $canvasName, string $type, string $title, array $labels, array $datasets)
    {
        require_once("require/charts/StatsChartsRenderer.php");
        $stats = new StatsChartsRenderer;
        ?>
        <script>
            var config = {
                type: '<?= $type ?>',
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