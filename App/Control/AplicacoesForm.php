<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Criteria;
use Livro\Database\Repository;
use Livro\Widgets\Base\Element;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Container\VBox;
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
class AplicacoesForm extends Page
{
    private $form; // formulário
    private $panel;
    private $coop;
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
        $this->coop = (isset($_GET['key']))? $_GET['key'] : NULL;
        $this->coop = (isset($_GET['cod_coop']))? $_GET['cod_coop'] : $this->coop;

        $this->activeRecord = 'Aplicacoes';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_aplicacoes'));
        $this->form->setTitle('Cadastro de Aplicações');
        
        // cria os campos do formulário
        $id                 = new Entry('id');
        $cod_coop           = new Entry('cod_coop');
        $nome               = new Entry('nome');
        $desenvolvedor      = new Entry('desenvolvedor');
        $tipo_hospedagem    = new Combo('tipo_hospedagem');
        $servidor           = new Combo('servidor');
        $endereco           = new Entry('endereco');
        $detalhes           = new Text('detalhes'); 
        $obs                = new Text('obs');   
        
        // carrega os servidores do banco de dados
        Transaction::open('db');
        
        $criteria = new Criteria; 
        $criteria->add('cod_coop', '=',  $this->coop);
        $hosts = new Repository('Servidores');
        $hosts_coop = $hosts->load($criteria);
        $items = array();

        foreach ($hosts_coop as $obj_servidor) {
            $items[$obj_servidor->nome] = $obj_servidor->nome;
        }

        $items[' - '] = ' - ';

        Transaction::close();

        $servidor->addItems($items);
        $tipo_hospedagem->addItems(array(   "OnPremisse" => "OnPremisse",
                                            "Cloud" => "Cloud"));

        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $cod_coop->setEditable(FALSE);
        $endereco->placeholder = "Informe o endereco de à aplicação";

        $cod_coop->setValue($this->coop);
        if(isset($_GET['id'])) {
            $id->setValue($_GET['id']);
        }

        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Nome', $nome, '70%');
        $this->form->addField('Desenvolvedor',   $desenvolvedor, '70%');
        $this->form->addField('Hospedagem',   $tipo_hospedagem, '70%');
        $this->form->addField('Servidor',   $servidor, '70%');
        $this->form->addField('Endereço de acesso',   $endereco, '70%');
        $this->form->addField('Detalhes da aplicação',   $detalhes, '70%');
        $this->form->addField('Observacões',   $obs, '70%');
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

            $dados->servidor = ($dados->tipo_hospedagem == "Cloud") ? ' - ' : $dados->servidor;
            
            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados
            $object->store(); // armazena o objeto
            
            $dados->id = $object->id;
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