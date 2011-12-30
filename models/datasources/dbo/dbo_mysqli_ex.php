<?

App::import('Datasource', 'DboMysqli');

class DboMysqliEx extends DboMysqli {

	public $description = "MySQLi DBO Driver - Extended";

	// Override resultSet to allow for Model__field type aliases
	function resultSet(&$results) {
		if(isset($this->results) && is_resource($this->results) && $this->results != $results){
			mysqli_free_result($this->results);
		}
		$this->results = & $results;
		$this->map = array();
		$num_fields = mysqli_num_fields($results);
		$index = 0;
		$j = 0;
		while($j < $num_fields){
			$column = mysqli_fetch_field_direct($results, $j);

			if(!empty($column->table) && strpos($column->name, $this->virtualFieldSeparator) === false){
				$table = $column->table;
				$field = $column->name;
			}else{
				$pos = strrpos($column->name, $this->virtualFieldSeparator);
				if($pos !== false){
					$table = substr($column->name, 0, $pos);
					$field = substr($column->name, $pos + strlen($this->virtualFieldSeparator));
				}else{
					$table = 0;
					$field = $column->name;
				}
			}

			if(strpos($table, $this->virtualFieldSeparator) !== false){
				$arr = explode($this->virtualFieldSeparator, $table);
				$arr[] = $field;
				$this->map[$index++] = $arr;
			}else{
				$this->map[$index++] = array($table, $field);
			}
			$j++;
		}
	}

	/**
	 * Fetches the next row from the current result set
	 * Lo sobreescribo para permitir la inclusion de submodelos desde joins
	 * @return unknown
	 */
	function fetchResult() {
		if(($row = mysqli_fetch_row($this->results))){
			$resultRow = array();
			foreach($row as $index => $field){
				$table = $column = null;
				if(count($this->map[$index]) === 2){
					list($table, $column) = $this->map[$index];
					$resultRow[$table][$column] = $row[$index];
				}else{
					$this->__fset($resultRow, $this->map[$index], $row[$index]);
				}
			}
			return $resultRow;
		}
		return false;
	}

	/**
	 * Insert an element into the array
	 * @param array $data the array to insert the item
	 * @param string $keys the path where to insert the item into the array
	 * @param mixed $value the value
	 */
	function __fset(array &$data, $keys, $value) {
		if(!is_array($keys)){
			$keys = explode('.', $keys);
		}
		$last = array_pop($keys);
		foreach($keys as $k){
			if(isset($data[$k]) && is_array($data[$k])){
				$data = & $data[$k];
			}else{
				$data[$k] = array();
				$data = & $data[$k];
			}
		}
		$data[$last] = $value;
	}

}