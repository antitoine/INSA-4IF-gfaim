<?php

class Cache
{
    /**
     * @var the instance of Cache
     */
    private static $instance = null;

    /**
     * @var SQLite3 the database
     */
    private $db;

    /**
     * Constructor
     */
    private function __construct() {
        $this->db = new SQLite3(DATABASE_ROOT);
    }

    /**
     * @return Cache the instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Cache();
        }
        return self::$instance;
    }

    /**
     * @return bool True if create or if exist, False if error
     */
    private function createIfNotExistsTextTable()
    {
        return $this->db->exec('CREATE TABLE IF NOT EXISTS TEXT (URL TEXT PRIMARY KEY NOT NULL, CONTENT TEXT NOT NULL);');
    }

    /**
     * @return bool True if create or if exist, False if error
     */
    public function createIfNotExistsSearchTable()
    {
        return $this->db->exec('CREATE TABLE IF NOT EXISTS SEARCH (QUERY TEXT PRIMARY KEY NOT NULL, RESULT TEXT NOT NULL);');
    }

    /**
     * Get the content text of an URL web site
     * @param $url the URL of web site
     * @return null|SQLite3Result
     */
    public function getTextByUrl($url)
    {
        $textResult = null;
        $ret = $this->createIfNotExistsTextTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $statement = $this->db->prepare('SELECT CONTENT FROM TEXT WHERE URL = :url;');
            $statement->bindValue(':url', $url);
            $textResult = $statement->execute();
        }
        return $textResult;
    }

    /**
     * Insert an URL and Text content pair to the database
     * @param $url the URL of web site
     * @param $text The content text of the web site
     * @return bool True if inserted
     */
    public function setTextUrl($url, $text)
    {
        $textResult = null;
        $ret = $this->createIfNotExistsTextTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $safeUrl = SQLite3::escapeString($url);
            $safeText = SQLite3::escapeString($text);
            $ret = $this->db->exec('INSERT INTO TEXT ("URL","CONTENT") VALUES ('.$safeUrl.', '.$safeText.');');
        }
        return $ret;
    }

    /**
     * Get the Result of the Query Search
     * @param $query the query
     * @return null|SQLite3Result
     */
    public function getResultSearchByQuery($query)
    {
        $queryResult = null;
        $ret = $this->createIfNotExistsSearchTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $statement = $this->db->prepare('SELECT RESULT FROM SEARCH WHERE QUERY = :query;');
            $statement->bindValue(':query', $query);
            $queryResult = $statement->execute();
        }
        return $queryResult;
    }

    /**
     * Insert an Query and Result pair to the database
     * @param $query The query
     * @param $result the result of the query
     * @return bool True if inserted
     */
    public function setResultSearchQuery($query, $result)
    {
        $textResult = null;
        $ret = $this->createIfNotExistsSearchTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $safeQuery = SQLite3::escapeString($query);
            $safeResult = SQLite3::escapeString($result);
            $ret = $this->db->exec('INSERT INTO SEARCH ("QUERY","RESULT") VALUES ('.$safeQuery.', '.$safeResult.');');
        }
        return $ret;
    }
}