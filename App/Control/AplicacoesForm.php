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
        $coop = (isset($_GET['key']))? $_GET['key'] : NULL;
        $server_id = (isset($_GET['id']))? $_GET['id'] : NULL;

        $this->activeRecord = 'Servidores';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_servidores'));
        $this->form->setTitle('Cadastro de Servidor');
        
        // cria os campos do formulário
        $id      = new Entry('id');
        $nome   = new Entry('nome');
        $cod_coop     = new Combo('cod_coop');
        $servidor_status     = new Combo('servidor_status');
        $modelo   = new Entry('modelo');
        $serial   = new Entry('serial');
        $tipo = new Combo('tipo');
        $host_virtual   = new Combo('host_virtual');
        $ip_principal   = new Entry('ip_principal');
        $ip_idrac   = new Entry('ip_idrac');
        $so   = new Combo('so');
        $so_status   = new Entry('so_status');
        $garantia   = new Date('garantia');
        $acessormt_tipo   = new Combo('acessormt_tipo');
        $acessormt_endereco   = new Entry('acessormt_endereco');
        $hardware_status   = new Combo('hardware_status');
        $obs   = new Text('obs');   
        
        // carrega os cidades do banco de dados
        Transaction::open('db');
        
        $cod_coops = Cooperativas::all();
        $items = array();
        foreach ($cod_coops as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id;
        }
        $cod_coop->addItems($items);
        
        $criteria = new Criteria; 
        $criteria->add('cod_coop', '=',  $coop);
        $criteria->add('tipo', '=',  'Físico');
        $hosts = new Repository('Servidores');
        $hosts_coop = $hosts->load($criteria);
        $items = array();
        foreach ($hosts_coop as $obj_servidor) {
            $items[$obj_servidor->nome] = $obj_servidor->nome;
        }
        $host_virtual->addItems($items);
        
        $sistemas = Sistemas::all();
        $items = array();
        foreach ($sistemas as $obj_sistema) {
            $items[$obj_sistema->nome] = $obj_sistema->nome;
        }
        $so->addItems($items);

        $servidor_status->addItems(array( "Reserva Técnica" => "Reserva Tecnica",
                                          "Em Producao" => "Em Producao",
                                          "Desativado" => "Desativado"));
        $tipo->addItems(array( "Físico" => "Físico",
                              "Virtual" => "Virtual"));
        $acessormt_tipo->addItems(array( "SSH" => "SSH",
                               "RDP" => "RDP",
                               "VNC" => "VNC"));
        $hardware_status->addItems(array( "Adequado" => "Adequado",
                               "Desatualizado - Requer Upgrade" => "Desatualizado - Requer Upgrade",
                               "Obsoleto - Deve ser desativado" => "Obsoleto - Deve ser desativado"));

        Transaction::close();
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $so_status->setEditable(FALSE);
        
        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Nome', $nome, '70%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Status do Servidor',   $servidor_status, '70%');
        $this->form->addField('Modelo',   $modelo, '70%');
        $this->form->addField('Tipo',   $tipo, '70%');
        $this->form->addField('Tipo',   $tipo, '70%');
        $this->form->addField('Host de Virtualizacao',   $host_virtual, '70%');
        $this->form->addField('Serial',   $serial, '70%');
        $this->form->addField('IP Principal',   $ip_principal, '70%');
        $this->form->addField('IP IDrac',   $ip_idrac, '70%');
        $this->form->addField('Sistema Operacional',   $so, '70%');
        $this->form->addField('',   $so_status, '70%');
        $this->form->addField('Garantia',    $garantia, '30%');
        $this->form->addField('Acesso Remoto - Tipo',   $acessormt_tipo, '70%');
        $this->form->addField('Endereco Remoto',   $acessormt_endereco, '70%');
        $this->form->addField('Status de Hardware',   $hardware_status, '70%');
        $this->form->addField('Observacões',   $obs, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        // adiciona o formulário na página
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);

        if(!is_null($server_id)){
            $this->panel = new Panel('Manutencões');

            $action = new Action(array(new ManutencoesForm, 'onEdit'));
            $action->setParameter('id', $server_id);
            
            $button = new Element('a');
            $button->add('Registrar');
            $button->class = 'btn btn-info';

            $button->href = $action->serialize();

            $box->add($this->panel);
            $this->panel->add($button);

            Transaction::open('db');

            $pdo = Transaction::get();
            $stmt = $pdo->prepare("SELECT * FROM manutencoes WHERE servidor_id=:servidor_id ORDER BY id DESC" );
            $stmt->execute(['servidor_id' => $server_id]); 
            $server_mnt = $stmt->fetchAll();
            
            foreach ($server_mnt as $mnt) {
                $this->panel->add(new Element('hr'));

                $data = date('d/m/Y',strtotime($mnt['data']));
                $text = "{$data} - {$mnt['servidor']}<br>Responsavel:{$mnt['responsavel']}";
                $info = new Element('p');
                $info->style = "font-size:12px;margin:0px;padding:0px";
                $info->add($text);

                $descricao = new Element('p');
                $descricao->style = "font-size:12px;margin:0px;padding:0px";
                $descricao->add($mnt['descricao']);
                
                $this->panel->add($info);
                $this->panel->add($descricao);
            }

            Transaction::close();
        }
      
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
            
            $sistemas = Sistemas::all();
            $items = array();
            foreach ($sistemas as $obj_sistema) {
                $status[$obj_sistema->nome] = $obj_sistema->status;
            }
            $dados->so_status = $status[$dados->so];
            
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