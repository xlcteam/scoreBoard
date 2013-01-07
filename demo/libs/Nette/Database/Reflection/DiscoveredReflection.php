<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Database\Reflection
 */



/**
 * Reflection metadata class with discovery for a database.
 *
 * @author     Jakuv Vrana
 * @property-write NConnection $connection
 * @package Nette\Database\Reflection
 */
class NDiscoveredReflection extends NObject implements IReflection
{
	/** @var NCache */
	protected $cache;

	/** @var ICacheStorage */
	protected $cacheStorage;

	/** @var NConnection */
	protected $connection;

	/** @var array */
	protected $structure = array(
		'primary' => array(),
		'hasMany' => array(),
		'belongsTo' => array(),
	);



	/**
	 * Create autodiscovery structure.
	 * @param  ICacheStorage
	 */
	public function __construct(ICacheStorage $storage = NULL)
	{
		$this->cacheStorage = $storage;
	}



	public function setConnection(NConnection $connection)
	{
		$this->connection = $connection;
		if (!in_array($this->connection->getAttribute(PDO::ATTR_DRIVER_NAME), array('mysql'))) {
			throw new NotSupportedException("NDiscoveredReflections supports only mysql driver");
		}

		if ($this->cacheStorage) {
			$this->cache = new NCache($this->cacheStorage, 'Nette.Database.' . md5($connection->getDsn()));
			$this->structure = ($tmp=$this->cache->load('structure')) ? $tmp : $this->structure;
		}
	}



	public function __destruct()
	{
		if ($this->cache) {
			$this->cache->save('structure', $this->structure);
		}
	}



	public function getPrimary($table)
	{
		$primary = & $this->structure['primary'][$table];
		if (isset($primary)) {
			return $primary;
		}

		if ($this->connection->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
			$query = $this->connection->query("PRAGMA table_info($table)");
			$primaryKey = 'pk';
			$primaryVal = '1';
			$primaryKeyColumn = 'name';
		} else {
			$query = $this->connection->query("EXPLAIN $table");
			$primaryKey = 3;
			$primaryVal = 'PRI';
			$primaryKeyColumn = 0;
		}

		foreach ($query as $column) {
			if ($column[$primaryKey] === $primaryVal) { // 3 - "Key" is not compatible with PDO::CASE_LOWER
				if ($primary !== NULL) {
					$primary = FALSE; // multi-column primary key is not supported
					break;
				}
				$primary = $column[$primaryKeyColumn];
			}
		}

		return $primary;
	}



	public function getHasManyReference($table, $key, $refresh = TRUE)
	{
		$reference = $this->structure['hasMany'];
		if (!empty($reference[$table])) {
			foreach ($reference[$table] as $targetTable => $targetColumn) {
				if (strpos($targetTable, strtolower($key)) !== FALSE) {
					return array(
						$targetTable,
						$targetColumn,
					);
				}
			}
		}

		if (!$refresh) {
			throw new PDOException("No reference found for \${$table}->related({$key}).");
		}

		$this->reloadTableReferenceFor($table);
		return $this->getHasManyReference($table, $key, FALSE);
	}



	public function getBelongsToReference($table, $key, $refresh = TRUE)
	{
		$reference = $this->structure['belongsTo'];
		if (!empty($reference[$table])) {
			foreach ($reference[$table] as $column => $targetTable) {
				if (strpos($column, strtolower($key)) !== FALSE) {
					return array(
						$targetTable,
						$column,
					);
				}
			}
		}

		if (!$refresh) {
			throw new PDOException("No reference found for \${$table}->{$key}.");
		}

		$this->reloadTableReference($table);
		return $this->getBelongsToReference($table, $key, FALSE);
	}



	protected function reloadTableReferenceFor($table)
	{
		$tables = array();
		$query = 'SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
			WHERE TABLE_SCHEMA = DATABASE()	AND REFERENCED_TABLE_NAME = ' . $this->connection->quote($table);

		foreach ($this->connection->query($query) as $row) {
			$tables[strtolower($row[0])] = $row[1];
		}

		uksort($tables, create_function('$a, $b', '
			return strlen($a) - strlen($b);
		'));

		$this->structure['hasMany'][$table] = $tables;
	}



	protected function reloadTableReference($table)
	{
		$tables = array();
		$query = 'SELECT COLUMN_NAME, REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE
			WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ' . $this->connection->quote($table);

		foreach ($this->connection->query($query) as $row) {
			$tables[strtolower($row[0])] = $row[1];
		}

		uksort($tables, create_function('$a, $b', '
			return strlen($a) - strlen($b);
		'));

		$this->structure['belongsTo'][$table] = $tables;
	}

}
