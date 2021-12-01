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
class ArquivosForm extends Page
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

        $this->activeRecord = 'Arquivos';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_arquivos'));
        $this->form->setTitle("{$coop} - Servidore de Arquivos");
        
        // cria os campos do formulário
        $id      = new Hidden('id');
        $cod_coop     = new Entry('cod_coop');
        $hospedagem   = new Combo('hospedagem');
        $servidor     = new Combo('servidor');
        $compartilhamento     = new Entry('compartilhamento');
        $obs   = new Text('obs');   

        $id->setValue($coop);
        $cod_coop->setValue($coop);
        $hospedagem->addItems(array( "Office365" => "Office365",
                                          "Local" => "Local",
                                          "Local em transição para Office365" => "Local em transição para Office365"));
        
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
        
        $servidor->addItems($items);
        $compartilhamento->placeholder = "\\\servidor\caminho";
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $cod_coop->setEditable(FALSE);
        
        $this->form->addField('',    $id, '30%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Domínio', $hospedagem, '70%');
        $this->form->addField('Hospedagem',   $hospedagem, '70%');
        $this->form->addField('Servidor de Arquivos',   $servidor, '70%');
        $this->form->addField('Compartilhamento',   $compartilhamento, '70%');
        $this->form->addField('Observações',   $obs, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        $action = new Action(array("CooperativaServicesForm", 'onReload'));
        $action->setParameter('id', $coop);
        $this->form->addAction('Retornar', $action);
        
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