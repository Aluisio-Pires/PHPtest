<?php

namespace app;

use app\Database;

require "Database.php";

class Busca
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(DB_HOST, DB_PORT, DB_USER, DB_PSWD, DB_NAME);
    }

    private function parseXML(string $data): array
    {
        $result = [];
        $xml    = new \SimpleXMLElement($data);

        $result["cep"]         = (string)$xml->cep[0];
        $result["logradouro"]  = (string)$xml->logradouro[0] ? $xml->logradouro[0] : null;
        $result["bairro"]      = (string)$xml->bairro[0] ? $xml->bairro[0] : null;
        $result["localidade"]  = (string)$xml->localidade[0] ;
        $result["uf"]          = (string)$xml->uf[0];

        return $result;
    }

    public function fetchCep(int $cep)
    {
        $mask     = substr($cep, 0, 5) . "-" . substr($cep, 5, 9);
        $endereco = $this->db->read("cep", ["cep" => $mask]);

        if (empty($endereco)) {
            $url     = "https://viacep.com.br/ws/" . $cep . "/xml/";
            $content = file_get_contents($url);

            $viacep = $this->parseXML($content);

            if (!$this->db->create("cep", $viacep)) {
                return $content;
            }

            return $content;
        }

        $render = [];
        foreach ($endereco as $k => $v) {
            if (strlen($v) <= 0) {
                unset($endereco[$k]);
                continue;
            }

            $render[$v] = $k;
        }

        $xml = new \SimpleXMLElement('<xmlcep/>');
        array_walk_recursive($render, [$xml, 'addChild']);

        return $xml->asXML();
    }
}