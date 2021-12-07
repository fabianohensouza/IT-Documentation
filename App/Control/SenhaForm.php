<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Email;
use Livro\Widgets\Form\Password;
use Livro\Widgets\Form\Date; 
use Livro\Widgets\Form\Text;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Form\RadioGroup;
use Livro\Database\Transaction;
use Livro\Database\Criteria;
use Livro\Database\Repository;

use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

use Livro\Traits\SaveTrait;
use Livro\Traits\EditTrait;

/**
 * Cadastro de Produtos
 */
class SenhaForm extends Page
{
    private $form; // formulário
    private $connection;
    private $activeRecord;
    
    //use SaveTrait;
    use EditTrait;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeRecord = 'Usuarios';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_usuarios'));
        $this->form->setTitle('Usuarios');
        
        // cria os campos do formulário
        $id             = new Entry('id');
        $nome           = new Entry('nome');
        $login          = new Entry('login');
        $senha          = new Password('senha');
        $valida_senha   = new Password('valida_senha');
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        $nome->setEditable(FALSE);
        $login->setEditable(FALSE);

        $senha->placeholder = "Informe a senha";
        $valida_senha->placeholder = "Repita a senha";

        $action = new Action(array('UsuariosFormList', 'onReload'));
        
        $this->form->addField('ID',    $id, '50%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Login',   $login, '50%');
        $this->form->addField('Senha',   $senha, '50%');
        $this->form->addField('',   $valida_senha, '50%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Retornar', $action);
        
        // adiciona o formulário na página
        parent::add($this->form);
    }

    function onSave()
    {
        try
        {
            Transaction::open( $this->connection );
            
            $class = $this->activeRecord;
            $dados = $this->form->getData();

            unset($dados->valida_senha);
            $dados->senha = md5($dados->senha);
                
            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados
            $object->store(); // armazena o objeto
            
            $dados->id = $object->id;
            $this->form->setData($dados);
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Senha alterada com sucesso');
            
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }
}