<?php 
include 'sudokuClass.php';
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