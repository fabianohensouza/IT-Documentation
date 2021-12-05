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
use Livro\Widgets\Form\Number; 
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
class BackupForm extends Page
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

        $this->activeRecord = 'Backup';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_backup'));
        $this->form->setTitle("{$coop} - Backup");
        
        // cria os campos do formulário
        $id              = new Hidden('id');
        $cod_coop       = new Entry('cod_coop');
        $tipo_backup    = new Combo('tipo_backup');
        $server_onprimisse        = new Combo('server_onprimisse');
        $provedor_nuvem    = new Combo('provedor_nuvem');
        $sistema    = new Entry('sistema');
        $midia  = new Combo('midia');
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

        $server_onprimisse->addItems($items);
        $tipo_backup->addItems(array(   "OnPremisse" => "OnPremisse",
                                        "Cloud" => "Cloud"));
        $provedor_nuvem->addItems(array(    "AZURE" => "AZURE",
                                            "AWS" => "AWS",
                                            "Outro provedor" => "Outro provedor",
                                            " - " => " - "));
        $midia->addItems(array( "Cloud" => "Cloud",
                                "Fita LTO" => "Fita LTO",
                                "HD Externo" => "HD Externo",
                                "Cópia outro servidor" => "Cópia outro servidor",
                                "Outra mídia" => "Outra mídia"));
        
        $sistema->placeholder = "Informe o nome do sistema de backup utilizado";
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $cod_coop->setEditable(FALSE);
        
        $action = new Action(array('CooperativaServicesForm', 'onReload'));
        
        $this->form->addField('',    $id, '30%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Tipo de Backup', $tipo_backup, '70%');
        $this->form->addField('Servidor de Backup', $server_onprimisse, '70%');
        $this->form->addField('Provedor de Nuvem',   $provedor_nuvem, '70%');
        $this->form->addField('Sistema de Backup',   $sistema, '70%');
        $this->form->addField('Mídia de Backup',   $midia, '70%');
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

            $dados->midia = ($dados->tipo_backup == "Cloud") ? 'Cloud' : $dados->midia;
            $dados->provedor_nuvem = ($dados->tipo_backup == "OnPremisse") ? ' - ' : $dados->provedor_nuvem;
            
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