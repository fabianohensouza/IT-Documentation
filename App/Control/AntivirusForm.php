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
use Livro\Widgets\Form\Number;
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
class AntivirusForm extends Page
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

        $this->activeRecord = 'Antivirus';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_antivirus'));
        $this->form->setTitle("{$coop} - Console de Antivírus");
        
        // cria os campos do formulário
        $id              = new Hidden('id');
        $cod_coop       = new Entry('cod_coop');
        $servidor        = new Combo('servidor');
        $produto    = new Combo('produto');
        $outro_produto    = new Entry('outro_produto');
        $tipo_console  = new Combo('tipo_console');
        $patch   = new Entry('patch');
        $url = new Entry('url');
        $licencas           = new Number('licencas');
        $expiracao           = new Date('expiracao');
        $obs            = new Text('obs');   
        
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

        $items[' - '] = ' - ';

        Transaction::close();
        
        $id->setValue($coop);
        $cod_coop->setValue($coop);

        $servidor->addItems($items);
        $produto->addItems(array(   "Trend - Smart Protection" => "Trend - Smart Protection",
                                    "Trend - Enterprise Protection" => "Trend - Enterprise Protection",
                                    "Outro produto" => "Outro produto"));
        $tipo_console->addItems(array(  "OnPremisse" => "OnPremisse",
                                        "Cloud" => "Cloud"));
        
        $outro_produto->placeholder = "Informe o nome do produto caso não esteja listado acima";
        $patch->placeholder = "Informe a versão do ultimo patch aplicado";
        $url->placeholder = "Informe o endereco de acesso ao console";
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $cod_coop->setEditable(FALSE);
        
        $action = new Action(array('CooperativaServicesForm', 'onReload'));
        
        $this->form->addField('',    $id, '30%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Servidor do Console', $servidor, '70%');
        $this->form->addField('Produto',   $produto, '70%');
        $this->form->addField('Outro produto',   $outro_produto, '70%');
        $this->form->addField('Tipo de Console',   $tipo_console, '70%');
        $this->form->addField('Patch de seguranca',   $patch, '70%');
        $this->form->addField('Endereco de acesso',   $url, '70%');
        $this->form->addField('N. de licencas',   $licencas, '70%');
        $this->form->addField('Expiracão',   $expiracao, '70%');
        $this->form->addField('Observacões',   $obs, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
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

            $dados->servidor = ($dados->tipo_console == "Cloud") ? ' - ' : $dados->servidor;
            
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