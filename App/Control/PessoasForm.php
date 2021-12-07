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
class PessoasForm extends Page
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

        $this->activeRecord = 'Pessoas';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_pessoas'));
        $this->form->setTitle('Pessoas');
        
        // cria os campos do formulário
        $id      = new Entry('id');
        $nome   = new Entry('nome');
        $cod_coop     = new Combo('cod_coop');
        $cargo = new Combo('cargo');
        $contato = new Email('contato');
        
        // carrega as cooperativas do banco de dados
        Transaction::open('db');
        $cod_coops = Cooperativas::all();
        $items = array();
        foreach ($cod_coops as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id;
        }
        Transaction::close();

        $cod_coop->addItems($items);
        $cargo->addItems(array( "Dir. Administrativo" => "Dir. Administrativo",
                              "Dir. Negócios" => "Dir. Negócios",
                              "Dir. Riscos" => "Dir. Riscos",
                              "Ger. Administrativo" => "Ger. Administrativo",
                              "Resp. TI" => "Resp. TI",
                              "Equipe TI" => "Equipe TI"));

        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);

        $action = new Action(array('PessoasFormList', 'onReload'));
        
        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Nome', $nome, '70%');
        $this->form->addField('Cooperativa',   $cod_coop, '70%');
        $this->form->addField('Cargo',   $cargo, '70%');
        $this->form->addField('Contato',   $contato, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Retornar', $action);
        
        // adiciona o formulário na página
        parent::add($this->form);
    }
}