<?php
class DB_Connect {
    var $connection = null;
    var $debug = false;
    var $table_schema = '';

    function __construct(){}

    function __destruct() 
    {
        $this->close();
    }

    public function connect() 
    {
        try {
            if ($this->debug) {
                $this->table_schema = "recidenciacyj_apeaja";
                $this->connection = new PDO('mysql:host=mysql-recidenciacyj.alwaysdata.net;dbname=recidenciacyj_apeajal', '279932_jessica', 'BTS2103', [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            } else {
                $this->table_schema = "materiaseca_apeajal";
                $this->connection = new PDO('mysql:host=localhost;dbname=materiaseca_apeajal', 'root', '', [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            }
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
            $this->close();
        }
        return $this->connection;
    }

    public function connect_externa() 
    {
        try {
            if ($this->debug) {
                $this->connection = new PDO("mysql:host=mysql-recidenciacyj.alwaysdata.net;dbname=recidenciacyj_bts", "279932_jessica", "BTS2103", [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            } else {
                $this->connection = new PDO("mysql:host=localhost;dbname=materiaseca_apeajal", "root", "", [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            }
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
            $this->close();
        }
        return $this->connection;
    }

    public function close() 
    {
        unset($this->connection);
    }

}
?>
