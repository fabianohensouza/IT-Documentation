<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Criteria;
use Livro\Database\Repository;
use Livro\Widgets\Base\Element;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Form\Hidden;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Email;
use Livro\Widgets\Form\Date; 
use Livro\Widgets\Form\Text;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Form\RadioGroup;
use Livro\Database\Transaction;

use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

use Livro\Traits\SaveTrait;
use Livro\Traits\EditTrait;
use Livro\Widgets\Form\Button;

/**
 * Cadastro de Produtos
 */
class AdForm extends Page
{
    private $form; // formulário
    private $panel;
    private $connection;
    private $activeRecord;
    
    use EditTrait {
        onEdit as onEditTrait;
    }
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        $coop = (isset($_GET['id']))? $_GET['id'] : NULL;

        $this->activeRecord = 'Ad';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_ad'));
        $this->form->setTitle("{$coop} - Dominio Active Directory");
        
        // cria os campos do formulário
        $id      = new Hidden('id');
        $cod_coop     = new Entry('cod_coop');
        $dominio   = new Entry('dominio');
        $abrangencia     = new Combo('abrangencia');
        $dc_primario     = new Combo('dc_primario');
        $dc_secundario     = new Combo('dc_secundario');
        $dns_primario     = new Combo('dns_primario');
        $dns_secundario     = new Combo('dns_secundario');
        $wsus     = new Combo('wsus');
        $obs   = new Text('obs');   

        $id->setValue($coop);
        $cod_coop->setValue($coop);
        $abrangencia->addItems(array( "Matriz e PAs" => "Matriz e PAs",
                                          "Matriz e expandindo aos PAs" => "Matriz e expandindo aos PAs",
                                          "Apenas Matriz" => "Apenas Matriz"));
        
        // carrega os servidores do banco de dados
        Transaction::open('db');
        
        $criteria = new Criteria; 
        $criteria->add('cod_coop', '=',  $coop);
        $hosts = new Repository('Servidores');
        $hosts_coop = $hosts->load($criteria);
        $items = array();

        foreach ($hosts_coop as $obj_servidor) {
            $items[$obj_servidor->nome] = $obj_servidor->nome;
        }
        
        Transaction::close();
        
        $dc_primario->addItems($items);
        $dc_secundario->addItems($items);
        $dns_primario->addItems($items);
        $dns_secundario->addItems($items);
        $wsus->addItems($items);
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $cod_coop->setEditable(FALSE);
        
        $this->form->addField('',    $id, '30%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Domínio', $dominio, '70%');
        $this->form->addField('Abrangência',   $abrangencia, '70%');
        $this->form->addField('DC Primário',   $dc_primario, '70%');
        $this->form->addField('DC Secundário',   $dc_secundario, '70%');
        $this->form->addField('DNS Primário',   $dns_primario, '70%');
        $this->form->addField('DNS Secundário',   $dns_secundario, '70%');
        $this->form->addField('Servidor WSUS',   $wsus, '70%');
        $this->form->addField('Observações',   $obs, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        // adiciona o formulário na página
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
      
        parent::add($box);
    }

    function onEdit($param)
    {
        $object = $this->onEditTrait($param);
    }
    
    function onSave()
    {
        try
        {
            Transaction::open( $this->connection );
            
            $class = $this->activeRecord;
            $dados = $this->form->getData();
            
            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados
            $object->store(); // armazena o objeto
            
            $dados->id = $object->id;
            $dados->coop = $object->id;
            $this->form->setData($dados);
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
            
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

}