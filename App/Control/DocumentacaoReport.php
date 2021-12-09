<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Date;
use Livro\Widgets\Dialog\Message;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

/**
 * Relatório de vendas
 */
class DocumentacaoReport extends Page
{
    private $replaces;

    /**
     * método construtor
     */
    public function __construct()
    {
        parent::__construct();

        $loader = new Twig_Loader_Filesystem('App/Resources');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('doc_infra.html');


        if(isset($_REQUEST['id']))
        {
            $cod_coop = $_REQUEST['id'];
            $this->replaces = array();
            $tabelas = array(   "Pessoas" => "pessoas",
                                "Servidores" => "servidores",
                                "Ad" => "ad",
                                "Antivirus" => "antivirus",
                                "Arquivos" => "arquivos",
                                "Backup" => "backup",
                                "Contentfilter" => "contentfilter",
                                "Domweb" => "domweb",
                                "Aplicacoes" => "aplicacoes");
            try
            {
                Transaction::open('db');
                $this->replaces['coop'] = Cooperativas::find($cod_coop);
                $this->replaces['coop'] = $this->replaces['coop']->toArray();

                foreach($tabelas as $obj => $name)
                {
                    $criteria = new Criteria; 
                    $criteria->add('cod_coop', '=',  $cod_coop);
                    $info = new Repository($obj);
                    $this->replaces[$name] = $info->load($criteria);

                    foreach($this->replaces[$name] as $key => $value){
                        $this->replaces[$name][$key] = $value->toArray();
                    }
                }

                Transaction::close();
            }
            catch (Exception $e)
            {
                new Message('error', $e->getMessage());
                Transaction::rollback();
            }
            
            //$content = $template->render($this->replaces);
            
            // cria um painél para conter o formulário
            $panel = new Panel("Documentacão de Infraestrutura <br><br> {$this->replaces['coop']['id']} - {$this->replaces['coop']['nome']}");
            //$panel->add($content);
            //$panel->add($this->replaces);
            
            parent::add($panel);
            
        }
    }
}
