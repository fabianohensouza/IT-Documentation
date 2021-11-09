<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Criteria;
use Livro\Database\Repository;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
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

/**
 * Cadastro de Produtos
 */
class ServidoresForm extends Page
{
    private $form; // formulário
    private $connection;
    private $activeRecord;
    
    use SaveTrait;
    use EditTrait;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeRecord = 'Servidores';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_servidores'));
        $this->form->setTitle('Servidores');
        
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

        $coop = $cod_coop->getValue();
        if(!empty($coop)) {
            $criteria = New Criteria();
            $criteria->add('cod_coop', '=', $coop);
            $hosts = new Repository('Servidores');
            $lista_hosts = $hosts->load($criteria);
            $items = array();
            foreach ($lista_hosts as $obj_host) {
                $items[$obj_host->nome] = $obj_host->nome;
            }
            $host_virtual->addItems($items);
        }

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
        $this->form->addField('Serial',   $serial, '70%');
        $this->form->addField('Host de Virtualizacao',   $host_virtual, '70%');
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
        parent::add($this->form);
    }
}