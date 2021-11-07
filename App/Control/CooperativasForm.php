<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Form\Entry;
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
class CooperativasForm extends Page
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

        $this->activeRecord = 'Cooperativas';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_Cooperativas'));
        $this->form->setTitle('Cooperativas');
        
        // cria os campos do formulário
        $id      = new Entry('id');
        $nome   = new Entry('nome');
        $cidade     = new Combo('cidade');
        $uar = new Combo('uar');
        $qt_equip = new Entry('qt_equip');
        $ic = new Combo('ic');
        $dt_contrato  = new Date('dt_contrato');
        $dt_rescisao        = new Date('dt_rescisao');
        $obs     = new Text('obs');
        
        // carrega os cidades do banco de dados
        Transaction::open('db');
        $cidades = Cidades::all();
        $items = array();
        foreach ($cidades as $obj_cidade) {
            $items[$obj_cidade->nome] = $obj_cidade->nome;
        }
        $cidade->addItems($items);
        $uar->addItems(array( 1 => "1",
                              2 => "2",
                              3 => "3",
                              4 => "4",
                              5 => "5",
                              6 => "6",
                              7 => "7",
                              8 => "8"));
        $ic->addItems(array("Sim" => "Sim", "Nao" => "Nao"));

        Transaction::close();
        
        $this->form->addField('Código',    $id, '30%');
        $this->form->addField('Nome', $nome, '70%');
        $this->form->addField('Cidade',   $cidade, '70%');
        $this->form->addField('UAR',   $uar, '70%');
        $this->form->addField('Equipamentos',   $qt_equip, '70%');
        $this->form->addField('InfraCredis',   $ic, '70%');
        $this->form->addField('Contrato',   $dt_contrato, '70%');
        $this->form->addField('Rescisão',   $dt_rescisao, '70%');
        $this->form->addField('Observacões',   $obs, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        // adiciona o formulário na página
        parent::add($this->form);
    }
}
