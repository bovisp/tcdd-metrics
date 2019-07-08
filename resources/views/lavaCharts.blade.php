<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Charts</title>

        
    </head>
    <body>
        <div id="stocks-chart"></div>
        {!! \Lava::render('ColumnChart', 'MyStocks', 'stocks-chart') !!}
        
    </body>
</html>
