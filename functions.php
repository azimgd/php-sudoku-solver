<?php
function arrayToTable($data) {
	if(!count($data)) 
	{
		$data = array_chunk(array_fill(0, 81, 0), 9);
	}

	echo '<table>';

	foreach($data as $row_key => $row_val)
	{
		echo '<tr>';

		foreach($data as $col_key => $col_val)
		{
			echo '<td>' . $data[$row_key][$col_key] . '</td>';
		}

		echo '</tr>';
	}

	echo '</table>';
}