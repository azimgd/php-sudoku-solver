<?php 
/**
* Implementation of backtracking and forward checking algorithms for sudoku
* @author Azim Gadjiagayev (http://azimgd.com)
*/

class SudokuSolver
{
	//file to extract sudoku puzzle from
	const FILE = 'grid.txt';
	private $_grid;

	public function __construct()
	{
		$this->_loadGridFromFile();
		$this->_grid = $this->_solve($this->_grid);
	}
	
	/**
	* Reading sudoku from file
	* and converting it to array
	*/
	private function _loadGridFromFile()
	{
		$data = file_get_contents('grid.txt');
		$data = rtrim($data, ',');
		$data = preg_replace('[\n]', '', $data);

		$data = explode(',', $data);

		if(count($data) != 9*9)
			die('Create a propper 9x9 matrix');

		$this->_grid = array_chunk($data, 9);

		return true;
	}
	
	/**
	* Return solved grid as an array
	*/
	public function getSolvedGrid()
	{
		return $this->_grid;
	}
	
	/**
	* Solve using backtrack
	*/
	private function _backtrackSolve($count = 0)
	{		
		if($count == 81)
		{
			//die(arrayToTable($this->getSolvedGrid()));
			return false;
		}

		//calculating coordinates
		$row = floor($count / 9);
		$col = floor($count % 9);

		//if cell is unsolved
		if($this->_grid[$row][$col] == 0)
		{
			//check each possible move
			foreach($this->_getAllowed($this->_grid, $row, $col) as $inputKey => $inputVal)
			{
				$this->_grid[$row][$col] = (int)$inputVal;
				$this->_solve($count + 1);
			}

			$this->_grid[$row][$col] = 0;
		}
		else
		{
			$this->_solve($count + 1);
		}

		return false;
	}

	/**
	* Solve using forwardchecking
	*/
	private function _forwardCheckingSolve($grid)
	{
		while(true)
		{
			$solutions = array();

			foreach($grid as $rowIndex => $row)
			{
				foreach($row as $columnIndex => $cell)
				{
					//skip if cell is solved
					if(!empty($cell))
						continue;

					//get allowed solutions for this cell
					//returns an array of solutions
					$allowed = $this->_getAllowed($grid, $rowIndex, $columnIndex);
					
					//if no no solution has been found
					if(count($allowed) == 0)
						return false;

					$solutions[] = array(
						'rowIndex' => $rowIndex,
						'columnIndex' => $columnIndex,
						'allowed' => $allowed
					);
				}
			}

			//if puzzle is solved
			if(count($solutions) == 0)
			{
				return $grid;
			}
			
			//sort solutions by quantity
			//this tweak will increse speed of the algorithm
			usort($solutions, array($this, '_sortOptions'));

			//assign solution to cell if solution has been found
			if(count($solutions[0]['allowed']) == 1)
			{
				$grid[$solutions[0]['rowIndex']][$solutions[0]['columnIndex']] = current($solutions[0]['allowed']);
				continue;
			}
			
			foreach($solutions[0]['allowed'] as $value)
			{
				$tmp = $grid;
				$tmp[$options[0]['rowIndex']][$options[0]['columnIndex']] = $value;

				//trying each solution
				if($result = $this->_solve($tmp))
				{
					return $result;
				}
			}
			
			return false;
		}
	}
	
	/**
	* Get allowed solutions for given coordinates
	*/
	private function _getAllowed($grid, $rowIndex, $columnIndex)
	{
		$valid = range(1, 9);
		//adding row values into invalid arr
		$invalid = $grid[$rowIndex];
		//adding col values into invalid arr
		foreach($grid as $row)
		{
			$invalid[] = $row[$columnIndex];
		}

		//getting box coordinates
		$boxRow = $rowIndex % 3 == 0 ? $rowIndex : $rowIndex - $rowIndex % 3;
		$boxCol = $columnIndex % 3 == 0 ? $columnIndex : $columnIndex - $columnIndex % 3;

		//adding square values into invalid arr
		$invalid = array_unique(array_merge(
			$invalid,
			array_slice($grid[$boxRow], $boxCol, 3),
			array_slice($grid[$boxRow + 1], $boxCol, 3),
			array_slice($grid[$boxRow + 2], $boxCol, 3)
		));

		return array_diff($valid, $invalid);
	}
	
	/**
	* Sort allowed values by quantity in ascending order
	*/
	private function _sortOptions($a, $b)
	{
		$a = count($a['allowed']);
		$b = count($b['allowed']);
		if($a == $b)
		{
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}
	
}