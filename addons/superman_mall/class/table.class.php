<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
class SupermanMallTable
{
	protected $_table;
	protected $_cache_key;
	protected $_pk = 'id';
	public function __construct($tablename)
	{
		$this->_table = $tablename;
	}
	public function fetch($filter, $orderby = '')
	{
		global $_W;
		$data = array();
		if (!empty($filter) && is_array($filter)) {
			$where = 'WHERE 1=1';
			$params = array();
			foreach ($filter as $key => $val) {
				$kpos = strpos($key, '#');
				if ($kpos !== false) {
					$key = substr($key, 0, $kpos);
				}
				if (is_string($val) && substr($val, 0, 1) == '#') {
					$val = substr($val, 1);
					$where .= " AND {$key}{$val}";
				} else {
					if (is_array($val)) {
						$where .= " AND {$key} IN (" . implode(',', $val) . ")";
					} else {
						$where .= " AND {$key}=:{$key}";
						$params[":{$key}"] = $val;
					}
				}
			}
		} else {
			if ($filter > 0 && is_numeric($filter)) {
				$where = " WHERE {$this->_pk}=:{$this->_pk}";
				$params[":{$this->_pk}"] = $filter;
			} else {
				return $data;
			}
		}
		if (defined('IN_SYS')) {
			if ($_W['uniacid'] && strpos($where, 'uniacid=') === false && pdo_fieldexists($this->_table, 'uniacid')) {
				$where .= ' AND uniacid=:uniacid';
				$params[':uniacid'] = $_W['uniacid'];
			}
		}
		$sql = 'SELECT * FROM ' . tablename($this->_table) . " {$where} {$orderby} LIMIT 1";
		$data = pdo_fetch($sql, $params);
		return $data;
	}
	public function fetchall($filter, $orderby = '', $start = 0, $pagesize = 10, $keyfiled = '')
	{
		global $_W;
		$data = array();
		if (!empty($filter)) {
			$where = 'WHERE 1=1';
			$params = array();
			foreach ($filter as $key => $val) {
				$kpos = strpos($key, '#');
				if ($kpos !== false) {
					$key = substr($key, 0, $kpos);
				}
				if (is_string($val) && substr($val, 0, 1) == '#') {
					$val = substr($val, 1);
					$where .= " AND {$key}{$val}";
				} else {
					if (is_array($val)) {
						$where .= " AND {$key} IN (" . implode(',', $val) . ")";
					} else {
						$where .= " AND {$key}=:{$key}";
						$params[":{$key}"] = $val;
					}
				}
			}
			if (!is_null($orderby) && empty($orderby)) {
				$orderby = 'ORDER BY ' . $this->_pk . ' DESC';
			}
			$limit = '';
			if ($pagesize > 0) {
				$limit = "LIMIT {$start},{$pagesize}";
			}
			$sql = 'SELECT * FROM ' . tablename($this->_table) . " {$where} {$orderby} {$limit}";
			$data = pdo_fetchall($sql, $params, $keyfiled);
		}
		return $data;
	}
	public function count($filter)
	{
		$data = array();
		if (!empty($filter)) {
			$where = 'WHERE 1=1';
			$params = array();
			foreach ($filter as $key => $val) {
				$kpos = strpos($key, '#');
				if ($kpos !== false) {
					$key = substr($key, 0, $kpos);
				}
				if (is_string($val) && substr($val, 0, 1) == '#') {
					$val = substr($val, 1);
					$where .= " AND {$key}{$val}";
				} else {
					if (is_array($val)) {
						$where .= " AND {$key} IN (" . implode(',', $val) . ")";
					} else {
						$where .= " AND {$key}=:{$key}";
						$params[":{$key}"] = $val;
					}
				}
			}
			$sql = 'SELECT COUNT(*) FROM ' . tablename($this->_table) . " {$where}";
			$data = pdo_fetchcolumn($sql, $params);
		}
		return $data;
	}
	public function sum($filter, $field)
	{
		$data = array();
		if (!empty($filter)) {
			$where = 'WHERE 1=1';
			$params = array();
			foreach ($filter as $key => $val) {
				$kpos = strpos($key, '#');
				if ($kpos !== false) {
					$key = substr($key, 0, $kpos);
				}
				if (is_string($val) && substr($val, 0, 1) == '#') {
					$val = substr($val, 1);
					$where .= " AND {$key}{$val}";
				} else {
					if (is_array($val)) {
						$where .= " AND {$key} IN (" . implode(',', $val) . ")";
					} else {
						$where .= " AND {$key}=:{$key}";
						$params[":{$key}"] = $val;
					}
				}
			}
			$sql = 'SELECT SUM(' . $field . ') FROM ' . tablename($this->_table) . " {$where}";
			$data = pdo_fetchcolumn($sql, $params);
		}
		return $data;
	}
	public function increment($data, $condition = array(), $glue = 'AND')
	{
		$result = false;
		if (is_array($data) && $data) {
			$condition = $this->implode($condition, $glue);
			$params = $condition['params'];
			$sql = "UPDATE " . tablename($this->_table) . " SET ";
			$fields = array();
			foreach ($data as $field => $value) {
				$fields[] = "{$field}={$field}+{$value}";
			}
			$sql .= implode(',', $fields);
			$sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
			$result = pdo_query($sql, $params);
		}
		return $result;
	}
	public function decrement($data, $condition = array(), $glue = 'AND')
	{
		$result = false;
		if (is_array($data) && $data) {
			$condition = $this->implode($condition, $glue);
			$params = $condition['params'];
			$sql = "UPDATE " . tablename($this->_table) . " SET ";
			$fields = array();
			foreach ($data as $field => $value) {
				$fields[] = "{$field}={$field}-{$value}";
			}
			$sql .= implode(',', $fields);
			$sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
			$result = pdo_query($sql, $params);
		}
		return $result;
	}
	public function insert($data)
	{
		pdo_insert($this->_table, $data);
		return pdo_insertid();
	}
	public function update($data, $condition)
	{
		global $_W;
		if (defined('IN_SYS')) {
			if ($_W['uniacid'] && !isset($condition['uniacid']) && pdo_fieldexists($this->_table, 'uniacid')) {
				$condition['uniacid'] = $_W['uniacid'];
			}
		}
		return pdo_update($this->_table, $data, $condition);
	}
	public function delete($condition)
	{
		global $_W;
		if (defined('IN_SYS')) {
			if ($_W['uniacid'] && !isset($condition['uniacid']) && pdo_fieldexists($this->_table, 'uniacid')) {
				$condition['uniacid'] = $_W['uniacid'];
			}
		}
		return pdo_delete($this->_table, $condition);
	}
	public function key($prefix)
	{
		return $prefix . ':' . $this->_cache_key;
	}
	public function field_exists($field)
	{
		return pdo_fieldexists($this->_table, $field);
	}
	public function cache_read($key, $unserialize = false)
	{
		global $_W;
		if ($_W['config']['setting']['cache'] == 'memcache') {
			return cache_load($key, $unserialize);
		}
		return null;
	}
	public function cache_write($key, $value, $ttl = 0)
	{
		global $_W;
		if ($_W['config']['setting']['cache'] == 'memcache') {
			return cache_write($key, $value, $ttl);
		}
	}
	private function implode($params, $glue = ',')
	{
		$result = array('fields' => ' 1 ', 'params' => array());
		$split = '';
		$suffix = '';
		if (in_array(strtolower($glue), array('and', 'or'))) {
			$suffix = '__';
		}
		if (!is_array($params)) {
			$result['fields'] = $params;
			return $result;
		}
		if (is_array($params)) {
			$result['fields'] = '';
			foreach ($params as $fields => $value) {
				if (is_array($value)) {
					$result['fields'] .= $split . "`{$fields}` IN ('" . implode("','", $value) . "')";
				} else {
					$result['fields'] .= $split . "`{$fields}` =  :{$suffix}{$fields}";
					$split = ' ' . $glue . ' ';
					$result['params'][":{$suffix}{$fields}"] = is_null($value) ? '' : $value;
				}
			}
		}
		return $result;
	}
}
if (!class_exists('M')) {
	class M
	{
		private static $_objs;
		public static function t($tablename)
		{
			$classname = 'table_' . $tablename;
			if (!isset(self::$_objs[$classname])) {
				if (!class_exists($classname, false)) {
					$filename = MODULE_ROOT . '/table/' . $tablename . '.php';
					if (file_exists($filename)) {
						require $filename;
						self::$_objs[$classname] = new $classname();
					} else {
						if (func_num_args()) {
							$ref = new ReflectionClass('SupermanMallTable');
							$obj = $ref->newInstanceArgs(func_get_args());
							if ($obj->field_exists('id')) {
								self::$_objs[$classname] = $obj;
							} else {
								trigger_error('表文件 "' . 'table/' . $tablename . '.php" 不存在，并且不存在 "id" 主键', E_USER_ERROR);
							}
						}
					}
				}
			}
			return self::$_objs[$classname];
		}
	}
} else {
	die('class M conflict');
}