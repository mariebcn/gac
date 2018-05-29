<html>
<body>
    <h1>TEST QUERIES</h1>
    <p>Total real time of calls : <?php echo $resultQueries['total_call']; ?> seconds</p>
    <p>-------------------------- Execution time : <?php echo  $resultQueries['execution_time']['total_call']; ?> seconds --------------------------</p>

    <p>Top 10 of data : 
        <table border="1">
            <tr>
                <td>Subscriber</td>
                <td>Date</td>
                <td>Data</td>
            </tr>
        <?php foreach ($resultQueries['top_data'] as $topData): ?>
            <tr>
                <td><?php echo $topData['subscriber']; ?></td>
                <td><?php echo $topData['date']; ?></td>
                <td><?php echo $topData['consumption']; ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </p>
    <p>-------------------------- Execution time : <?php echo  $resultQueries['execution_time']['top_data']; ?> seconds --------------------------</p>

    <p>Total of sms : <?php echo $resultQueries['total_sms']; ?>
    <p>-------------------------- Execution time : <?php echo  $resultQueries['execution_time']['total_sms']; ?> seconds --------------------------</p>
</body>