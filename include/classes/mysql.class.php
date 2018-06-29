<?php
/**
 * Obsługa bazy danych MySQL
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Parametry połączenia z bazą danych
 *
 */
class DBConnectionSettings {
	public $Host;
	public $Port;
	public $Schema;
	public $User;
	public $Password;

	function __construct($DBConnectionSettings) {
		$this->Host = (isset($DBConnectionSettings['host']) ? $DBConnectionSettings['host'] : '');
		$this->Port = (isset($DBConnectionSettings['port']) ? $DBConnectionSettings['port'] : '');
		$this->Schema = (isset($DBConnectionSettings['schema']) ? $DBConnectionSettings['schema'] : '');
		$this->User = (isset($DBConnectionSettings['user']) ? $DBConnectionSettings['user'] : '');
		$this->Password = (isset($DBConnectionSettings['password']) ? $DBConnectionSettings['password'] : '');
	}
}

/**
 * Obsługa bazy danych MySQL
 *
 */
class DBMySQL {
	private $ConnectionSettings;
	private $Connection = null;
	private $Result = null;
	private $LastErrorNumber = 0;
	private $LastError = '';
	private $NumRows = 0;
	private $AffectedRows = 0;
	private $LastInsertID = 0;
	private $CurrentRow = 0;
	private $LogQuery = false;
	private $ErrorDescriptions = array(
		1062 => 'Próba dodania elementu o parametrach, które muszą być unikalne a istnieje już element który je posiada.',
		1451 => 'Nie można usunąć elementu do którego odnoszą się inne elementy.'
	);

	function __construct(DBConnectionSettings &$ConnectionSettings) {
		$this->ConnectionSettings = $ConnectionSettings;
		$this->Connect();
	}

	function Connect() {
		if (!$this->Connected()) {
			if ($this->Connection = mysql_connect($this->ConnectionSettings->Host.($this->ConnectionSettings->Port ? ':'.$this->ConnectionSettings->Port : ''), $this->ConnectionSettings->User, $this->ConnectionSettings->Password)) {
				mysql_selectdb($this->ConnectionSettings->Schema, $this->Connection);
				mysql_query("SET NAMES 'utf8'");
				return true;
			}
			return false;
		}
		return true;
	}
	
	function Connected() {
		return ($this->Connection && mysql_ping($this->Connection));
	}

	function Query($Query) {
		if ($this->LogQuery) {
			error_log('SQL Query: '.htmlspecialchars($Query, ENT_QUOTES));
		}
		if (!$this->Connect()) {
			return false;
		}
		$this->NumRows = 0;
		$this->CurrentRow = 0;
		$this->LastError = '';
		if ($this->Result = mysql_query($Query, $this->Connection)) {
			if ($this->Result !== true) {
				$this->NumRows = mysql_num_rows($this->Result);
			}
			elseif (!($this->LastInsertID = mysql_insert_id($this->Connection))) {
				$this->AffectedRows = mysql_affected_rows($this->Connection);
			}
			return true;
		}
		else {
			$this->LastError = mysql_error();
			$this->LastErrorNumber = mysql_errno();
			if ($this->LogQuery) {
				error_log("SQL Error $this->LastErrorNumber: $this->LastError");
			}
			return false;
		}
	}

	function GetRow($RowNumber = -1, $ResultType = MYSQL_ASSOC) {
		if ($RowNumber < 0) {
			if ($this->CurrentRow >= $this->NumRows) {
				return false;
			}
			$this->CurrentRow++;
			return mysql_fetch_array($this->Result, $ResultType);
		}
		elseif ($RowNumber <= $this->NumRows) {
			$this->CurrentRow = $RowNumber;
			mysql_data_seek($this->Result, $this->CurrentRow);
			return mysql_fetch_array($this->Result, $ResultType);
		}
		else {
			return false;
		}
	}

	function GetObject($RowNumber = -1) {
		if ($RowNumber < 0) {
			if ($this->CurrentRow >= $this->NumRows) {
				return false;
			}
			$this->CurrentRow++;
			return mysql_fetch_object($this->Result);
		}
		elseif ($RowNumber <= $this->NumRows) {
			$this->CurrentRow = $RowNumber;
			mysql_data_seek($this->Result, $this->CurrentRow);
			return mysql_fetch_object($this->Result);
		}
		else {
			return false;
		}
	}

	function GetResult($ResultType = MYSQL_ASSOC) {
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_array($this->Result, $ResultType)) {
				$Result[] = $Wiersz;
			}
			return new DBQueryResult($Result);
		}
		else {
			return false;
		}
	}

	function GetValue($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			return mysql_result($this->Result, 0 ,0);
		}
		else {
			return false;
		}
	}

	function GetOptions($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_row($this->Result)) {
				$Result[$Wiersz[0]] = $Wiersz[1];
			}
			return $Result;
		}
		else {
			return false;
		}
	}

        function GetData($Query){
            $this->Query($Query);
            return $this->GetRow();
        }

        function GetRows($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_array($this->Result)) {
				$Result[] = $Wiersz;
			}
			return $Result;
		}
		else {
			return false;
		}
	}

	function GetValues($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_row($this->Result)) {
				$Result[] = $Wiersz[0];
			}
			return $Result;
		}
		else {
			return false;
		}
	}
	
	function GetResultAsArray($Query, $PoleID){
		$Result = array();
		$this->Query($Query);
		while($Element = $this->GetRow()){
			$Result[$Element[$PoleID]] = $Element;
		}
		return $Result;
	}

	function GetLastInsertID() {
		return $this->LastInsertID;
	}

	function GetLastError() {
		return $this->LastError;
	}

	function GetLastErrorNumber() {
		return $this->LastErrorNumber;
	}
	
	function GetNumRows() {
		return $this->NumRows;
	}
	
	function GetLastErrorDescription() {
		if ($this->LastErrorNumber) {
			if (isset($this->ErrorDescriptions[$this->LastErrorNumber])) {
				return $this->ErrorDescriptions[$this->LastErrorNumber];
			}
			else {
				return 'Numer problemu zwrócony przez baze danych: '.$this->LastErrorNumber."<br />".$this->LastError;
			}
		}
		return false;
	}
	
	function Close() {
		if ($this->Connection) {
			if ($this->Result) {
				mysql_free_result($this->Result);
				$this->Result = null;
			}
			mysql_close($this->Connection);
			$this->Connection = null;
		}
	}

	function PrepareInsert($Table, $Fields) {
		$Result = "INSERT INTO $Table SET";
		foreach ($Fields as $FieldName => $Value) {
			$Result .= " $FieldName = ".("$Value" == "My_null" ? "null," : "'".mysql_real_escape_string($Value)."',");
		}
		return rtrim($Result, ',');
	}

	function PrepareUpdate($Table, $Fields, $WhereFields) {
		$Result = "UPDATE $Table SET";
		foreach ($Fields as $FieldName => $Value) {
                        $Result .= " $FieldName = ".("$Value" == "My_null" ? "null," : "'".mysql_real_escape_string($Value)."',");
		}
		$Result = rtrim($Result, ',');
		if (count($WhereFields)) {
			$Result .= " WHERE";
			foreach ($WhereFields as $FieldName => $Value) {
				$Result .= " $FieldName = '".mysql_real_escape_string($Value)."' AND";
			}
		}
		return rtrim($Result, ' AND');
	}
	
	function EnableLog($Enable = true) {
		$this->LogQuery = $Enable;
	}
	
	function SaveSet1n($Table, $IDField, $ValueField, $ID, $Values) {
		if ($this->Query("DELETE FROM $Table WHERE $IDField = '$ID'")) {
			if (is_array($Values) && count($Values)) {
				foreach ($Values as $Value) {
					$this->Query("INSERT INTO $Table($IDField, $ValueField) VALUES('$ID', '$Value')");
				}
			}
		}
	}
	
	function SaveSet1n2val($Table, $IDField, $IDField2, $ValueField, $ID, $IDs, $Values) {
		if ($this->Query("DELETE FROM $Table WHERE $IDField = '$ID'")) {
			if (is_array($Values) && count($Values)) {
				foreach ($Values as $IDValue => $Value) {
					$this->Query("INSERT INTO $Table($IDField, $IDField2, $ValueField) VALUES('$ID', '{$IDs[$IDValue]}', '$Value')");
				}
			}
		}
	}

	function SaveSet2n($Table, $IDField, $IDField2, $ValueField, $ID, $ID2, $Value) {
		if ($this->Query("DELETE FROM $Table WHERE $IDField = '$ID' AND $IDField2 = '$ID2'") && $Value != "") {
			$this->Query("INSERT INTO $Table($IDField, $IDField2, $ValueField) VALUES('$ID', '$ID2', '$Value')");
		}
	}

	function GetSet1n($Table, $IDField, $ValueField, $ID) {
		return $this->GetValues("SELECT $ValueField FROM $Table WHERE $IDField = '$ID'");
	}
	
	function GetSet2n($Table, $IDField, $IDField2, $ValueField, $ID, $ID2) {
		return $this->GetValues("SELECT $ValueField FROM $Table WHERE $IDField = '$ID' AND $IDField2 = '$ID2'");
	}
	
	function SaveSetnn($GroupTable, $Table, $GroupNameField, $GroupIDField, $ValueField, $Values) {
		if ($this->Query("INSERT INTO $GroupTable($GroupNameField) VALUES(NULL)")) {
			$ID = $this->LastInsertID;
			if (is_array($Values) && count($Values)) {
				foreach ($Values as $Value) {
					$this->Query("INSERT INTO $Table($GroupIDField, $ValueField) VALUES('$ID', '$Value')");
				}
			}
			return $ID;
		}
		return false;
	}
	
	function GetSetnn($Table, $GroupIDField, $ValueField, $ID) {
		return $this->GetValues("SELECT $ValueField FROM $Table WHERE $GroupIDField = '$ID'");
	}
	
	function SaveValues($Table, $Values, $Where = null){
		if(is_array($Values) && count($Values)){
			$SetValue = "";
			foreach($Values as $klucz => $wartosc){
				$SetValue .= " $klucz = '$wartosc',";
			}
			$SetValue = rtrim($SetValue,',');
			if(!is_null($Where) && is_array($Where) && count($Where)){
				$Warunek = "";
				foreach($Where as $klucz => $wartosc){
					$Warunek .= ($Warunek == "" ? " " : " AND ")."$klucz = '$wartosc'";
				}
				$this->Query("UPDATE $Table SET$SetValue".($Warunek != "" ? " WHERE $Warunek" : ""));
			}else{
				$this->Query("INSERT INTO $Table SET$SetValue");
			}
		}
	}
}

/**
 * Wynik zapytania jako tablica
 *
 */
class DBQueryResult {
	public $Data;

	function __construct(&$Data = null) {
		if (is_array($Data)) {
			$this->Data = $Data;
		}
	}
}

?>
