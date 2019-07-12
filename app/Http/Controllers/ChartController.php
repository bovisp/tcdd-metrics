<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\PDF;
use App\Charts\testChart;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class ChartController extends Controller
{
    public function index()
    {
        $html = '
            <<!DOCTYPE html>
            <html>
            
            <head>
            
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style>
                    .pie-chart {
                        width: 900px;
                        height: 500px;
                        margin: 0 auto;
                    }
                </style>
            
                <script type="text/javascript" src="http://www.google.com/jsapi"></script>
            
                <script type="text/javascript">
                    function init() {
                        google.load("visualization", "1.1", {
                            packages: ["corechart"],
                            callback: "drawCharts"
                        });
                    }
                    function drawCharts() {
                        var data = google.visualization.arrayToDataTable([
                            ["Task", "Hours per Day"],
                            ["Coding", 11],
                            ["Eat", 1],
                            ["Commute", 2],
                            ["Looking for code Problems", 4],
                            ["Sleep", 6]
                        ]);
                        var options = {
                            title: My Daily Activities",
                        };
                        var chart = new google.visualization.PieChart(document.getElementById("piechart"));
                        chart.draw(data, options);
                    }
                </script>
            </head>
            
            <body onload="init()">
                <div id="piechart" class="pie-chart"></div>
            </body>
            
            </html>
        ';

        // $pdf = \PDF::loadHTML($html);
        $pdf = \PDF::loadView('chartjs')
        ->setOption('enable-javascript', true)
        ->setOption('javascript-delay', 5000)
        ->setOption('enable-smart-shrinking', true)
        ->setOption('no-stop-slow-scripts', true);
        return $pdf->save('chart.pdf');

        Browsershot::url('https://www.google.ca')
        ->setChromePath('"C:\Program Files (x86)\Google\Chrome\Application\chrome"')
        ->setNodeBinary('"C:\wamp64\www\nodejs\node"')
        ->setNpmBinary('"C:\wamp64\www\nodejs\npm"')
        ->setDelay(5000)
        ->save('chart.pdf');
    }
}
