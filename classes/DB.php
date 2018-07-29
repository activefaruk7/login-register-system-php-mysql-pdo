<?php

class DB{

    private static $_instance = null;

    private $_conn,
            $_error = false,
            $_query,
            $_results,
            $_count = 0;

    private function __construct()
    {

        try {
            $this->_conn = new PDO("mysql:host=" . Config::get('mysql/host') . ";dbname=" . Config::get('mysql/db') , Config::get('mysql/username') , Config::get('mysql/password'));
            // set the PDO error mode to exception
            $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }

    }

    public static function getInstance(){

        if(!isset(self::$_instance)){

            self::$_instance = new DB();

        }else{
            return self::$_instance;
        }

    }

    public function query($sql, $params = array()){
        $this->_error = false;

        if($this->_query = $this->_conn->prepare($sql)){
            $x = 1;

            if(count($params)){
                foreach ($params as $param) {

                    $this->_query->bindValue($x, $param);
                    $x++;

                }
            }

            if($this->_query->execute()){
                //$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            }else{
                $this->_error = true;
            }

        }

        return $this;

    }

    public function aciton($action, $table, $where = array()){

        if(count($where) === 3){

            $oparators = array('=', '>', '<', '>=', '<=');

            $field = $where[0];
            $oparator = $where[1];
            $value = $where[2];

            if(in_array($oparator, $oparators)){

                $sql = "{$action} FROM {$table} WHERE {$field} {$oparator} ?";

                if(!$this->query($sql, array($value))->error()){

                    return $this;

                }

            }

        }

        return false;

    }

    public function get($table, $where){

        return $this->aciton("SELECT *", $table, $where);

    }

    public function insert($table, $fields = array()){

        $keys = array_keys($fields);
        $x = 1;
        $values = null;

        foreach($fields as $field){
            $values .= '?';
            if($x < count($fields)){
                $values .= ", ";
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

        if(!$this->query($sql, $fields)->error()){
            return true;
        }

        return false;

    }

    public function results(){
        return $this->_results;
    }

    public function first(){
        $data = $this->results();
        return $data[0];
    }

    public function error(){
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }
}

DB::getInstance();
