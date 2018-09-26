<?php include "header.php";
$connection = new MongoDB\Driver\Manager();
$pipeline = [	['$group' => array(
					'_id' => '$DISTRICT' ,
					'count' => ['$sum' => 1 ]
				)
				],
				['$sort' => ['count' => -1 ]]
			];

$command = new \MongoDB\Driver\Command([
        'aggregate' => 'cases',
        'pipeline' => $pipeline
        ]);

$cursor = $connection->executeCommand('govt', $command);

echo '<table> <thead>
<tr>
<th>City</th>
<th>Crime Reported</th>
</tr> </thead> <tbody>';
foreach ($cursor as $row){
	foreach ($row->result as $value) {
			echo "<tr><td>$value->_id</td>";
			echo "<td>$value->count</td></tr>";
	}
}
echo '</tbody></table>';

include "footer.php"; ?>
