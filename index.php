<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'SudokuClass.php';
include 'functions.php';

$puzzle = new SudokuSolver();
?>
<html>
<style>
	table {
		border: 1px;
	}
</style>
<?php arrayToTable($puzzle->getSolvedGrid());?>
</html>