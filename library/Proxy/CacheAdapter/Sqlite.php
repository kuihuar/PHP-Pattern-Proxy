<?php
namespace Proxy\CacheAdapter;

class Sqlite implements Cacheable
{
    protected $sqlite;

    protected $cacheTime;

    const DEFAULT_SQLITEFILE = "/tmp/ProxyCache.sq3";

    const DEFAULT_QUERY      = "SELECT value FROM cache WHERE key='%s' AND time < %d";

    public function __construct($sqliteFile = self::DEFAULT_SQLITEFILE)
    {
        if (!extension_loaded('sqlite3')) {
            throw new \RuntimeException("ext/sqlite3 is needed");
        }
        if (!file_exists($sqliteFile)) {
            if ($sqliteFile == self::DEFAULT_SQLITEFILE) {
                touch($sqliteFile);
                $this->sqlite = new \Sqlite3($sqliteFile);
                $this->prepareTables();
                return;
            }
            throw new \InvalidArgumentException("File '$sqliteFile' not found");
        }
        if (!is_writable($sqliteFile) || !is_readable($sqliteFile)) {
            throw new \InvalidArgumentException("Cannot access file '$sqliteFile'");
        }
        $this->sqlite = new \Sqlite3($sqliteFile);
    }

    public function get($item)
    {
        return $this->sqlite->querySingle(
                sprintf(self::DEFAULT_QUERY, $this->sqlite->escapeString($item), time()-$this->cacheTime));
    }

    public function set($item, $value)
    {
        $this->sqlite->exec("INSERT INTO cache (key, value, time) VALUES(
                                  '{$this->sqlite->escapeString($item)}',
                                  '{$this->sqlite->escapeString($value)}',
                                  ".time()."
                                  )");
        return $this;
    }

    public function has($item)
    {
        return null != $this->sqlite->querySingle(
                 sprintf(self::DEFAULT_QUERY, $this->sqlite->escapeString($item), time()-$this->cacheTime));
    }

    public function setCacheTime($time)
    {
        $this->cacheTime = abs((int)$time);
        return $this;
    }

    protected function prepareTables()
    {
        $this->sqlite->exec("DROP TABLE IF EXISTS cache");
        $this->sqlite->exec("CREATE TABLE cache (key CHAR, value CHAR, time INTEGER)");
    }

    public function getCacheTime()
    {
        return $this->cacheTime;
    }
}