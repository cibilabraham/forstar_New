<?php
/**
 * CSV helper for cakePHP. Compatible with version 1.1.x.x and higher.
 *
 * PHP versions 4 and 5
 *
 * Licensed under The MIT License
 *
 * @copyright		Adam Royle
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class CsvHelper {
	
	var $delimiter = ',';
	var $enclosure = '"';
	var $filename = null;
	var $line = array();
	var $buffer;
	
 /** 
	* This option preserves leading zeros on numeric data when opened in Excel.
	* Use it ONLY when the csv file is going to be opened in Excel, as it uses
	* non-standard syntax, and will probably break in other systems.
	*/
	var $preserveLeadingZerosInExcel = false;
	
	
	var $_tmpFile = false;
	
	function CsvHelper() {
		$this->clear();
	}

/**
 * Adds a multi-dimensional array to the buffer. 
 *
 * @param array $data Multi-dimensional array
 * @param boolean $addFieldNames Add a row of field names before adding data
 * @access public
 */
	function addGrid($data, $addFieldNames = true, $fieldList = null) {
		
		if (!$data) {
			return false;
		}
		
		if (@is_array(reset($row = reset($data)))) {
			
			// Array generated by cakePHP model
			// eg.
			// $data = array(array('Model' => array('field_name' => 'field value')), array('Model' => array('field_name' => 'field value')))
			
			$defaultModel = key($row);
			if ($this->filename === null) {
				$this->setFilename(Inflector::pluralize($defaultModel));
			}
			
			if ($fieldList) {
				
				$fields = array();
				
				foreach ($fieldList as $fieldName) {
					if (strpos($fieldName, '.')) {
						list($modelName, $fieldName) = explode('.', $fieldName);
					} else {
						$modelName = $defaultModel;
					}
					$fields[] = array(Inflector::humanize($modelName), $fieldName);
				}
				
				if ($addFieldNames){
					foreach ($fields as $field) {
						if ($field[0] != $defaultModel) {
							$this->addField($field[0].' '.Inflector::humanize($field[1]));
						} else {
							$this->addField(Inflector::humanize($field[1]));
						}
					}
					$this->endRow();
				}
				foreach ($data as $row) {
					foreach ($fields as $field) {
						@$this->addField($row[$field[0]][$field[1]]);
					}
					$this->endRow();
				}
				
			} else {
				
				if ($addFieldNames){
					foreach (reset($row) as $key => $value) {
						$this->addField(Inflector::humanize($key));
					}
					$this->endRow();
				}
				foreach ($data as $row) {
					$this->addRow($row[$defaultModel]);
				}
				
			}
			
		} else {
			
			// Regular 2-dimensional array (with or without keys).
			// eg. 
			//			$data = array(array('field_name' => 'field_value'), array('field_name' => 'field_value'))
			//	or
			//			$data = array(array('field value'), array('field value'))
			
			if ($fieldList) {
				
				if ($addFieldNames){
						foreach ($fieldList as $fieldName) {
							$this->addField(Inflector::humanize($fieldName));
						}
						$this->endRow();
				}
				foreach ($data as $row) {
					foreach ($fieldList as $fieldName) {
						@$this->addField($row[$fieldName]);
					}
					$this->endRow();
				}
				
				
			} else {
				
				if ($addFieldNames) {
					foreach (reset($data) as $key => $value) {
						$this->addField(Inflector::humanize($key));
					}
					$this->endRow();
				}
				foreach ($data as $row) {
					$this->addRow($row);
				}
				
			}
		}
		
	}

/**
 * Adds a single field value to the buffer. You must call $csv->endRow() to commit fields to the buffer.
 *
 * @param string $value Field value
 * @access public
 */
	function addField($value) {
		$this->line[] = $value;
	}
	
/**
 * Commits the row of fields that were added by addField()
 *
 * @access public
 */
	function endRow() {
		$this->addRow($this->line);
		$this->line = array();
	}
	
/**
 * Adds a single row to the buffer.
 *
 * @param array $row Data to be added
 * @access public
 */
	function addRow($row) {
		if ($this->preserveLeadingZerosInExcel) {
			// convert the number to a string formula
			foreach ($row as $key => $value){
				if (strlen($value) > 1 && $value[0] == '0' && is_numeric($value)) {
					$row[$key] = '="'.$value.'"';
				}
			}
		}
		fputcsv($this->buffer, $row, $this->delimiter, $this->enclosure);
	}

/**
 * Outputs headers
 *
 * @param string $filename Filename to save as
 * @access public
 */
	function renderHeaders($filename = null) {

		if (is_string($filename)) {
			$this->setFilename($filename);
		} 
		
		if ($this->filename === null) {
			$this->filename = 'Data.csv';
		}
		
		if ($this->filename) {
			header("Content-disposition:attachment;filename=".$this->filename);
		}

		header("Content-type:application/vnd.ms-excel");
		
	}

/**
 * Sets the output filename. Automatically appends .csv if necessary.
 *
 * @param string $filename Filename to save as
 * @access public
 */
	function setFilename($filename) {
		$this->filename = $filename;
		if (strtolower(substr($this->filename, -4)) != '.csv') {
			$this->filename .= '.csv';
		}
	}

/**
 * Returns CSV string and clears internal buffer. Outputs headers() if necessary.
 *
 * @param mixed $outputHeaders Boolean to determine if should output headers, or a string to set the filename
 * @param string $to_encoding Encoding to use
 * @param string $from_encoding 
 * @return string String CSV formatted string
 * @access public
 */
	function render($outputHeaders = true, $to_encoding = null, $from_encoding = "auto") {
		if ($outputHeaders) {
			if (is_string($outputHeaders)) {
				$this->setFilename($outputHeaders);
			}
			$this->renderHeaders();
		}
		
		if ($this->_tmpFile) {
			rewind($this->buffer);
			$output = '';
			while (!feof($this->buffer)) {
			 $output .= fread($this->buffer, 8192);
			}
			fclose($this->buffer);
		} else {
			rewind($this->buffer);
			$output = stream_get_contents($this->buffer);
		}
				
		// get around excel bug (http://support.microsoft.com/kb/323626/)
		if (substr($output,0,2) == 'ID') {
			$pos = strpos($output, $this->delimiter);
			if ($pos === false) {
				$pos = strpos($output, "\n");
			}
			if ($pos !== false) {
				$output = $this->enclosure . substr($output, 0, $pos) . $this->enclosure . substr($output, $pos);
			}
		}
		
		if ($to_encoding) {
			$output = mb_convert_encoding($output, $to_encoding, $from_encoding);
		}
		
		$this->clear();
		
		return $this->output($output);
	}

	/*
	Return CSV string
	*/
	function getAsCSV($outputHeaders = false, $to_encoding = null, $from_encoding = "auto")
	{		
		if ($this->_tmpFile) {
			rewind($this->buffer);
			$output = '';
			while (!feof($this->buffer)) {
			 $output .= fread($this->buffer, 8192);
			}
			fclose($this->buffer);
		} else {
			rewind($this->buffer);
			$output = stream_get_contents($this->buffer);
		}
				
		// get around excel bug (http://support.microsoft.com/kb/323626/)
		if (substr($output,0,2) == 'ID') {
			$pos = strpos($output, $this->delimiter);
			if ($pos === false) {
				$pos = strpos($output, "\n");
			}
			if ($pos !== false) {
				$output = $this->enclosure . substr($output, 0, $pos) . $this->enclosure . substr($output, $pos);
			}
		}
		
		if ($to_encoding) {
			$output = mb_convert_encoding($output, $to_encoding, $from_encoding);
		}
		
		$this->clear();
		
		return $output;
	}

/**
 * Clears internal buffer. This is called automatically by CsvHelper::render()
 *
 * @access public
 */
	function clear() {
		$this->line = array();
		$this->buffer = @fopen('php://temp/maxmemory:'.(5*1024*1024), 'r+');
		if ($this->buffer === false) {
			$this->_tmpFile = true;
			$this->buffer = tmpfile();
		}
	}

	
}



/**
 * A PHP4 implementation of the equivalent PHP5 function
 *
 * See (http://www.php.net/manual/en/function.fputcsv.php) for details
 */
 
if (!function_exists('fputcsv')) {
	
	function fputcsv(&$handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
		$str = '';
		$escape_char = '\\';
		foreach ($fields as $value) {
			settype($value, 'string');
			if (strpos($value, $delimiter) !== false ||
					strpos($value, $enclosure) !== false ||
					strpos($value, "\n") !== false ||
					strpos($value, "\r") !== false ||
					strpos($value, "\t") !== false ||
					strpos($value, ' ') !== false) {
				$str2 = $enclosure;
				$escaped = 0;
				$len = strlen($value);
				for ($i=0;$i<$len;$i++) {
					if ($value[$i] == $escape_char) {
						$escaped = 1;
					} else if (!$escaped && $value[$i] == $enclosure) {
					  $str2 .= $enclosure;
					} else {
					  $escaped = 0;
					}
					$str2 .= $value[$i];
				}
				$str2 .= $enclosure;
				$str .= $str2.$delimiter;
			} else {
				$str .= $value.$delimiter;
			}
		}
		$str = substr($str,0,-1);
		$str .= "\n";
		return fwrite($handle, $str);
	}
	
}

?>