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
    private function __construct()
    {
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
        return $this->db->exec(
            'CREATE TABLE IF NOT EXISTS TEXT (
                URL TEXT PRIMARY KEY NOT NULL,
                CONTENT TEXT NOT NULL
            );');
    }

    /**
     * @return bool True if create or if exist, False if error
     */
    public function createIfNotExistsSearchTable()
    {
        //$this->db->exec('DROP TABLE SEARCH');
        
        return $this->db->exec(
            'CREATE TABLE IF NOT EXISTS SEARCH (
                QUERY TEXT NOT NULL, 
                RESULT TEXT NOT NULL,
                PRIMARY KEY (QUERY, RESULT)
            );');
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
        
        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $statement = $this->db->prepare('SELECT CONTENT FROM TEXT WHERE URL = :url;');
            $statement->bindValue(':url', $url);

            $statementResultList = $statement->execute();

            while($statementResult = $statementResultList->fetchArray())
            {
                $textResult = $statementResult['CONTENT'];
                break;
            }
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
        $ret = $this->createIfNotExistsTextTable();

        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $insertStatement = $this->db->prepare("INSERT OR IGNORE INTO TEXT ('URL', 'CONTENT') VALUES (:url, :content)");
            $insertStatement->bindParam(':url', $url);
            $insertStatement->bindParam(':content', $text);

            $ret = $insertStatement->execute();

            if (!$ret)
            {
                echo $this->db->lastErrorMsg();
            }
        }

        return $ret;
    }

    /**
     * Get the Result of the Query Search
     * @param $query the query
     * @return null|SQLite3Result
     */
    public function getResultsSearchByQuery($query)
    {
        $queryResult = array();
        $ret = $this->createIfNotExistsSearchTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $statement = $this->db->prepare('SELECT RESULT FROM SEARCH WHERE QUERY = :query;');
            $statement->bindValue(':query', $query);
            $queryResultsSet = $statement->execute();
            
            while ($result = $queryResultsSet->fetchArray()) {
                $queryResult[] = $result['RESULT'];
            }
        }
        return $queryResult;
    }

    /**
     * Insert an Query and Result pair to the database
     * @param $query The query
     * @param $result the result of the query
     * @return bool True if inserted
     */
    public function setResultsSearchQuery($query, $results)
    {
        $textResult = null;
        $ret = $this->createIfNotExistsSearchTable();

        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $insertStatement = $this->db->prepare("INSERT OR IGNORE INTO SEARCH ('QUERY', 'RESULT') VALUES (:query, :result)");
            $insertStatement->bindParam(':query', $query);
            
            foreach ($results as $result)
            {
                $insertStatement->bindValue(':result', $result);
                
                $ret = $insertStatement->execute();
                
                if (!$ret)
                {
                    echo $this->db->lastErrorMsg();
                }
            }

        }
        
        return $ret;
    }
}