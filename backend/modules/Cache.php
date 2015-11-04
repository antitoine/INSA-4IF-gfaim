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
    public function createIfNotExistsModuleOneTable()
    {
        return $this->db->exec(
            'CREATE TABLE IF NOT EXISTS ModuleOne (
                query TEXT NOT NULL, 
                result TEXT NOT NULL,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                PRIMARY KEY (query, result)
            );');
    }

    /**
     * Get the Result of the Query Search
     * @param $query the query
     * @return null|SQLite3Result
     */
    public function getResultListOfModuleOneByQuery($query)
    {
        if (!CACHE_ENABLED) {
            return null;
        }
        
        $queryResult = array();
        $ret = $this->createIfNotExistsModuleOneTable();
        if (!$ret) {
            echo $this->db->lastErrorMsg();
        } else {
            $statement = $this->db->prepare('SELECT result FROM ModuleOne WHERE query = :query;');
            $statement->bindValue(':query', $query);
            $queryResultsSet = $statement->execute();
            
            while ($result = $queryResultsSet->fetchArray()) {
                $queryResult[$result['result']] = array(
                    'title' => $result['title'],
                    'description' => $result['description']
                    );
            }
        }
        return $queryResult;
    }

    /**
     * Insert an Query and Result pair to the database
     * @param $query The query
     * @param $result the result of the query. 
     *        Associative array with key : url
     *                               value : array with keys 'title' & 'description'
     * @return bool True if inserted
     */
    public function setResultListInModuleOne($query, $results)
    {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        $textResult = null;
        $ret = $this->createIfNotExistsModuleOneTable();

        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $insertStatement = $this->db->prepare("INSERT OR IGNORE INTO ModuleOne ('query', 'result') VALUES (:query, :result)");
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

    /**
     * @return bool True if create or if exist, False if error
     */
    private function createIfNotExistsModuleTwoTable()
    {
        return $this->db->exec(
            'CREATE TABLE IF NOT EXISTS ModuleTwo (
                url TEXT PRIMARY KEY NOT NULL,
                content TEXT NOT NULL
            );');
    }

    /**
     * Get the content text of an URL web site
     * @param $url the URL of web site
     * @return null|SQLite3Result
     */
    public function getSingleResultOfModuleTwoByUrl($url)
    {
        if (!CACHE_ENABLED) {
            return null;
        }
        
        $textResult = null;
        
        
        $ret = $this->createIfNotExistsModuleTwoTable();
        
        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $statement = $this->db->prepare('SELECT content FROM ModuleTwo WHERE url = :url;');
            $statement->bindValue(':url', $url);

            $statementResultList = $statement->execute();

            while($statementResult = $statementResultList->fetchArray())
            {
                $textResult = $statementResult['content'];
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
    public function setSingleResultInModuleTwo($url, $text)
    {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        $ret = $this->createIfNotExistsModuleTwoTable();

        if (!$ret)
        {
            echo $this->db->lastErrorMsg();
        }
        else
        {
            $insertStatement = $this->db->prepare("INSERT OR IGNORE INTO ModuleTwo ('url', 'content') VALUES (:url, :content)");
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
}