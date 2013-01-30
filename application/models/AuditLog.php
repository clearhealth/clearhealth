<?php
/*****************************************************************************
*       AuditLog.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * This class is used to persist audit logs on application shutdown
 * @access static
 */

class AuditLog {
	private static $_dbConfig = array();
	private static $_sql = array();

	/**
	 * Class Constructor
	 * Declared as private to prevent from instantiating
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Class Clone
	 * Declared as private to prevent from cloning
	 * @access private
	 */
	private function __clone() {}

	/**
	 * Get database configuration
	 * @return array
	 */
	public static function getDbConfig() {
		return self::$_dbConfig;
	}

	/**
	 * Set database configuration
	 * @param array database configuration
	 */
	public static function setDbConfig(Array $dbConfig) {
		self::$_dbConfig = $dbConfig;
	}

	/**
	 * Get sql queries
	 * @return array
	 */
	public static function getSql() {
		return self::$_sql;
	}

	/**
	 * Set SQL queries
	 * @param array SQL queries
	 */
	public static function setSql(Array $sql) {
		self::$_sql = $sql;
	}

	/**
	 * Close browser connection before persisting SQL queries
	 * This is called by registered shutdown function on App.php
	 */
	public static function closeConnection() {
		header("Connection: close");
		$size = ob_get_length();
		header("Content-Length: $size");
		ob_end_flush();
		flush();
		self::persist();
	}

	/**
	 * Append one or more SQL query/ies
	 * @param mixed SQL query/ies
	 */
	public static function appendSql($queries) {
		if (!is_array($queries)) {
			$queries = array($queries);
		}
		foreach ($queries as $sql) {
			self::$_sql[] = $sql;
		}
	}

	/**
	 * Persist audit logs to database, this is called after header connection closed
	 */
	public static function persist() {
		if (!count(self::$_sql) > 0 || !count(self::$_dbConfig) > 0) {
			return;
		}

		$config = self::$_dbConfig;
		$hostname = $config['params']['host'];
		$username = $config['params']['username'];
		$password = $config['params']['password'];
		$dbname = $config['params']['dbname'];

		// in the meantime, just use mysqli because of its multi-query support
		$mysqli = new mysqli($hostname,$username,$password,$dbname);

		/* check connection */
		$retry = 10; // 10 seconds to retry
		$ctr = 0;
		do {
			$err = mysqli_connect_errno();
		} while($err && $ctr++ < $retry);

		if ($err) {
			$error = 'Connect failed: '.mysqli_connect_error().PHP_EOL;
			printf($error);
			trigger_error($error,E_USER_WARNING);
			return;
		}

		// generate queries
		$queries = implode(";\n",self::$_sql);
		file_put_contents('/tmp/audit',print_r($queries,true));

		/* execute multi query */
		$ret = $mysqli->multi_query($queries);
		if ($ret === false) {
			$error = 'Audit SQL failed: '.$queries;
			trigger_error($error,E_USER_WARNING);
		}

		/* close connection */
		$mysqli->close();
	}
}

