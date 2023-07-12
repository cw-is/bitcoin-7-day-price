<?php
ob_start();
// Fetch Bitcoin price data for the last 7 days
$url = 'https://api.coingecko.com/api/v3/coins/bitcoin/market_chart?vs_currency=usd&days=7&interval=daily';
$json = file_get_contents($url);
$obj = json_decode($json, true);
$prices = $obj['prices'];
$price_data = [];

foreach ($prices as $price) {
    $date = date('Y-m-d', intval($price[0] / 1000));
    $price_usd = $price[1];
    $price_data[] = [$date, $price_usd];
}

// Store the price data as a JSON string in a JavaScript variable
$js_price_data = json_encode($price_data);
$php_output = ob_get_clean();
?>

<!DOCTYPE html>
<html>
<body>
    <div id="chart_div"></div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            let data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Price');

            const priceData = <?php echo $js_price_data; ?>;
            for (const price of priceData) {
                data.addRow([price[0], price[1]]);
            }

            let options = {
                title: 'Bitcoin Price in USD - Last 7 Days',
                curveType: 'function',
                legend: { position: 'bottom' },
                height: 400
            };

            let chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</body>
</html>