<?php

namespace app;

use MongoDB\Driver\Query;

class Database
{
    public $status;

    private $host;
    private $port;
    private $user;
    private $pswd;
    private $base;
    private $con;

    public function __construct($host = "localhost", $port = 3306, $user = "root", $pswd = "", $base = "")
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pswd = $pswd;
        $this->base = $base;

        if ($this->connect()) {
            mysqli_set_charset($this->con, "utf8");
        }
    }

    private function connect(): bool
    {
        $this->con = mysqli_connect($this->host, $this->user, $this->pswd, $this->base, $this->port);

        if (!$this->con) {
            $this->status["link"]    = false;
            $this->status["status"]  = "Erro ao conectar no banco de dados.";
            $this->status["message"] = mysqli_error(mysqli_connect($this->host, $this->user, $this->pswd, $this->base, $this->port));

            return false;
        }
        $this->status["link"]    = true;
        $this->status["status"]  = "Conexão OK.";
        $this->status["message"] = "";

        return $this->con->ping();
    }

    public function read(string $table, $where = []): array
    {
        if( !$this->con ) {
            return [];
        }

        $query = "SELECT * FROM " . $this->base . "." . $table;

        if (!empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $val) {
                $query .= "{$key} = \"{$val}\" AND";
            }

            $query = substr($query, 0, -4);
        }

        $row = $this->con->query($query);

        if (!$row) {
            return [];
        }

        if ($row->num_rows <= 0) {
            return [];
        }

        return $row->fetch_assoc();
    }

    public function create(string $table, array $columns): bool
    {
        if (strlen($table) <= 0) {
            return false;
        }

        if (count($columns) <= 0) {
            return false;
        }

        $keys   = "";
        $values = "";

        foreach ($columns as $k => $v) {
            $k    = mysqli_real_escape_string($this->con, $k);
            $keys .= "{$k}, ";

            $v = mysqli_real_escape_string($this->con, $v);
            if (strlen($v) <= 0) {
                $values .= "NULL, ";
            } else {
                $values .= "\"{$v}\", ";
            }
        }
        $keys   = substr($keys, 0, -2);
        $values = substr($values, 0, -2);

        $query = "INSERT INTO " . $this->base . "." . $table . "(" . $keys . ")" . " VALUES (" . $values . ")";

        if ($this->con->query($query)) {
            return true;
        }

        return false;
    }
}
