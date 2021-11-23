<?php
use Livro\Database\Transaction;
use Livro\Database\Criteria;
use Livro\Database\Repository;

class ManutencoesServices
{
    public static function getData($request)
    {
        $id_server = $request['id'];
        
        $server_array = array();
        Transaction::open('db'); // inicia transação
        print_r($id_server);
        echo '<hr>';

        $criteria = new Criteria; 
        $criteria->add('servidor_id', '=',  $id_server);
        $manutencoes = new Repository('Manutencoes');
        $server_mnt = $manutencoes->load($criteria);

        if ($server_mnt) {
            $server_array = $server_mnt->toArray();
        }
        else {
            throw new Exception("server {$id_server} não encontrado");
        }
        Transaction::close(); // fecha transação
        return $server_array;
    }
}