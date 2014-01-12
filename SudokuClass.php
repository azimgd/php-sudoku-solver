<?php 

class SudokuSolver
{
	//file to load sudoku from
	const FILE = 'grid.txt';
	private $_validVars;
	public $_grid_init;
	private $_grid;

	public function __construct()
	{
		$this->_validVars = range(1, 9);

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

		$row = floor($count / 9);
		$col = floor($count % 9);

		if($this->_grid[$row][$col] == 0)
		{
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
			//to save possible results
			$options = array();

			//looping over grid
			foreach($grid as $rowIndex => $row)
			{
				foreach($row as $columnIndex => $cell)
				{
					//skip if cell is filled
					if(!empty($cell))
					{
						continue;
					}

					//get allowed inputs for this cell
					//returns an array of elements
					$allowed = $this->_getAllowed($grid, $rowIndex, $columnIndex);
					
					//if no inputs are allowed for this cell
					if(count($allowed) == 0)
						return false;

					$options[] = array(
						'rowIndex' => $rowIndex,
						'columnIndex' => $columnIndex,
						'allowed' => $allowed
					);
				}
			}

			//if puzzle is solved
			if(count($options) == 0)
			{
				return $grid;
			}
			
			//sort options to get most
			usort($options, array($this, '_sortOptions'));

			//if array of valid inputs has only one element
			//assign it to related cell and skip to next iteration
			if(count($options[0]['allowed']) == 1)
			{
				$grid[$options[0]['rowIndex']][$options[0]['columnIndex']] = current($options[0]['allowed']);
				continue;
			}
			
			//loop over possible values
			//for related cell
			foreach($options[0]['allowed'] as $value)
			{
				$tmp = $grid;
				$tmp[$options[0]['rowIndex']][$options[0]['columnIndex']] = $value;

				//checking for next solutions
				if($result = $this->_solve($tmp))
				{
					return $result;
				}
			}
			
			return false;
		}
	}
	
	/**
	* Get allowed inputs for given coordinates
	*/
	private function _getAllowed($grid, $rowIndex, $columnIndex)
	{
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

		return array_diff($this->_validVars, $invalid);
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