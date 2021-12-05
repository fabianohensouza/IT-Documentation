<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Database\Transaction;

use Livro\Traits\DeleteTrait;
use Livro\Traits\ReloadTrait;

use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Base\Element;

/**
 * Página de produtos
 */
class CooperativaServicesForm extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $filters;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_REQUEST['cod_coop'])) 
        {
            $this->panel = new Panel("{$_REQUEST['cod_coop']} - Serviços e Aplicações");

            $apps = [   "AdForm" => "Domínio AD",
                        "AntivirusForm" => "Antivírus",
                        "ArquivosForm" => "Arquivos",
                        "BackupForm" => "Backup",
                        "ContentfilterForm" => "CFS",
                        "DomwebForm" => "Domínio WEB",
                        "AplicacoesFormList" => "Aplicações"];
            
            $action = array();
            $button = array();

            foreach($apps as $key => $value)
            {
                $action[$key] = new Action(array(new $key, 'onEdit'));
                $action[$key]->setParameter('id', $_REQUEST['cod_coop']);
                    
                $button[$key] = new Element('a');
                $button[$key]->add($value);
                $button[$key]->style = 'width: 120px; margin-right: 8px';
                $button[$key]->class = 'btn btn-success';
                $button[$key]->href = $action[$key]->serialize();

                $this->panel->add($button[$key]);
            }                
            
            parent::add($this->panel);
        }
    }
    
    public function onReload()
    {
        $this->loaded = true;
    }
    
    /**
     * Exibe a página
     */
    public function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
