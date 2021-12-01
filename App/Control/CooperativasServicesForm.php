<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Database\Transaction;

use Livro\Traits\DeleteTrait;
use Livro\Traits\ReloadTrait;

use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

/**
 * Página de produtos
 */
class CooperativasServicesForm extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $filters;
    
    //use DeleteTrait;
    //use ReloadTrait {
    //    onReload as onReloadTrait;
    //}
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        
        // Define o Active Record
        $this->activeRecord = 'Cooperativas';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_services_cooperativas'));
        $this->form->setTitle('Cooperativas - Serviços e Aplicações');
        
        // cria os campos do formulário
        $coop = new Combo('cod_coop');

        Transaction::open('db');
        $cod_coops = Cooperativas::all();
        $items = array();
        foreach ($cod_coops as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id;
        }
        $coop->addItems($items);

        Transaction::close();
        
        $this->form->addField('Selecione',   $coop, '70%');
        $this->form->addAction('Acessar', new Action(array("CooperativaServicesForm", 'onReload')));
        
        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        
        parent::add($box);
    }
    
    /**
     * Exibe a página
     */
    public function show()
    {
         parent::show();
    }
}
