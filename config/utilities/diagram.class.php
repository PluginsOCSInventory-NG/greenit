<?php

/**
 * Used to create chartjs diagrams
 * 
 * @version Release: 1.0
 * @since Class available since Release 2.0
 */
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
     * @return string|array Return colors in a string or an array
     */
    public function GenerateColorList(int $nb, bool $arrayMode = false): string|array
    {
        if ($arrayMode == false) {
            $colors = "";
            for ($i = 0; $i <= $nb - 1; $i++) {
                $colors .= "'" . $this->colorsList[$i] . "'";
                if ($i != $nb - 1)
                    $colors .= ", ";
            }
        } else {
            $colors = array();
            for ($i = 0; $i < $nb; $i++) {
                $color = "'" . $this->colorsList[$i] . "'";
                array_push($colors, $color);
            }
        }
        return $colors;
    }

    /**
     * Create a zone to draw charts
     * 
     * @param string $canvasName Define the name of the zone
     * @param string $nbColumn Define the width in bootstrap grid column
     * @param string $height Define the height of the zone (strange reaction with responsive charts)
     * 
     * @return void Return nothing
     */
    public function CreateCanvas(string $canvasName, string $nbColumn, string $height): void
    {
        echo "
            <div class='col-md-$nbColumn'>
                <canvas id='$canvasName' width='500' height='$height' />
            </div>
        ";
    }

    /**
     * Create an vertical bar chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     * @return void Return nothing
     */
    public function CreateVerticalBarChart(string $canvasName, string $title, array $labels, array $datasets): void
    {
        echo "
            <script>
                var config = {
                    type: 'bar',
                    options: {
                        title: {
                            display: " . ($title == "" ? "false" : "true") . ",
                            text: '" . str_replace("'", "\\'", $title) . "',
                            position: 'bottom'
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
        ";
        foreach ($labels as $column) {
            echo "'" . str_replace("'", "\\'", $column) . "', ";
        }
        echo "],
                        datasets: [
        ";
        foreach ($datasets as $column) {
            echo '{';
            foreach ($column as $key => $setting) {
                echo "$key: $setting,";
            }
            echo "},";
        }
        echo "],
                    }
                }

                var ctx = document.getElementById('" . str_replace("'", "\\'", $canvasName) . "').getContext('2d');
                window.mySNMP = new Chart(ctx, config);
            </script>
        ";
    }

    /**
     * Create an horizontal bar chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     * @return void Return nothing
     */
    public function CreateHorizontalBarChart(string $canvasName, string $title, array $labels, array $datasets): void
    {
        echo "
            <script>
                var config = {
                    type: 'horizontalBar',
                    options: {
                        title: {
                            display: " . ($title == "" ? "false" : "true") . ",
                            text: '" . str_replace("'", "\\'", $title) . "',
                            position: 'bottom'
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
        ";
        foreach ($labels as $column) {
            echo "'" . str_replace("'", "\\'", $column) . "', ";
        }
        echo "
                        ],
                        datasets: [
        ";
        foreach ($datasets as $column) {
            echo '{';
            foreach ($column as $key => $setting) {
                echo "$key: $setting,";
            }
            echo '},';
        }
        echo "
                        ],
                    }
                }

                var ctx = document.getElementById('" . str_replace("'", "\\'", $canvasName) . "').getContext('2d');
                window.mySNMP = new Chart(ctx, config);
            </script>
        ";
    }

    /**
     * Create a doughnut chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     * @return void Return nothing
     */
    public function CreateDoughnutChart(string $canvasName, string $title, array $labels, array $datasets)
    {
        echo "
            <script>
                var config = {
                    type: 'doughnut',
                    options: {
                        responsive: true,
                        title: {
                            display: " . ($title == "" ? "false" : "true") . ",
                            text: '" . str_replace("'", "\\'", $title) . "'
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
        ";
        foreach ($labels as $column) {
            echo "'" . str_replace("'", "\\'", $column) . "', ";
        }
        echo "
                    ],
                        datasets: [
                            {
        ";
        foreach ($datasets as $key => $setting) {
            echo "$key: $setting,";
        }
        echo "
                            },
                        ],
                    }
                }

                var ctx = document.getElementById('" . str_replace("'", "\\'", $canvasName) . "').getContext('2d');
                window.mySNMP = new Chart(ctx, config);
            </script>
        ";
    }

    /**
     * Create a pie chart
     * 
     * @param string $canvasName Identify the name of the zone where will be draw the chart
     * @param string $title Define the title of the chart
     * @param array $labels Define the labels of the chart
     * @param array $datasets Define the data of the chart
     * 
     * @return void Return nothing
     */
    public function CreatePieChart(string $canvasName, string $title, array $labels, array $datasets)
    {
        echo "
            <script>
                var config = {
                    type: 'pie',
                    options: {
                        responsive: true,
                        title: {
                            display: " . ($title == "" ? "false" : "true") . ",
                            text: '" . str_replace("'", "\\'", $title) . "'
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
        ";
        foreach ($labels as $column) {
            echo "'" . str_replace("'", "\\'", $column) . "', ";
        }
        echo "
                    ],
                        datasets: [
                            {
        ";
        foreach ($datasets as $key => $setting) {
            echo "$key: $setting,";
        }
        echo "
                            },
                        ],
                    }
                }

                var ctx = document.getElementById('" . str_replace("'", "\\'", $canvasName) . "').getContext('2d');
                window.mySNMP = new Chart(ctx, config);
            </script>
        ";
    }
}

?>