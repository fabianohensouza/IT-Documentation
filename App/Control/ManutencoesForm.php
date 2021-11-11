<?php
use Livro\Control\Page;
use Livro\Control\Action;
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
class ManutencoesForm extends Page
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

        $this->activeRecord = 'Manutencoes';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_manutencoes'));
        $this->form->setTitle('Manutencoes');
        
        // cria os campos do formulário
        $servidor      = new Entry('servidor');
        $data   = new Entry('data');
        $responsavel      = new Entry('responsavel');
        $desc     = new Text('desc');

        $servidor->setEditable(FALSE);
        $responsavel->setEditable(FALSE);
        $data->setEditable(FALSE);
        
        // carrega os cidades do banco de dados
        Transaction::open('db');
        $obj_servidor = new Servidores;
        $servidor_nome = $obj_servidor->load($_GET['id']);
        $servidor->setValue($servidor_nome->nome);
        Transaction::close();

        $data->setValue(date('d/m/Y'));
        $responsavel->setValue($_SESSION['nome']);
        
        $this->form->addField('Servidor', $servidor, '70%');
        $this->form->addField('Data',   $data, '70%');
        $this->form->addField('Responsável',   $responsavel, '70%');
        $this->form->addField('Descricão',   $desc, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
        // adiciona o formulário na página
        parent::add($this->form);
    }
}