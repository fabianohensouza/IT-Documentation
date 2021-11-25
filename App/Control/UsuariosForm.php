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
class UsuariosForm extends Page
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
        $id      = new Entry('id');
        $nome   = new Entry('nome');
        $login   = new Entry('login');
        $email   = new Email('email');
        $permissao     = new Combo('permissao');
        $status     = new Combo('status');
        $senha = new Password('senha');
        $valida_senha = new Password('valida_senha');
        
        $permissao->addItems(array( "Administrador" => "Administrador",
                              "Usuario" => "Usuario"));

        $status->addItems(array( "Ativo" => "Ativo",
                              "Inativo" => "Inativo",
                              "Bloqueado" => "Bloqueado"));
        
        // define alguns atributos para os campos do formulário
        $id->setEditable(FALSE);
        
        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Nome', $nome, '70%');
        $this->form->addField('Login',   $login, '70%');
        $this->form->addField('E-mail',   $email, '70%');
        $this->form->addField('Permissao',   $permissao, '70%');
        $this->form->addField('Status',   $status, '70%');
        $this->form->addField('Digite a Senha',   $senha, '70%');
        $this->form->addField('Repita a Senha',   $valida_senha, '70%');
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        
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
            
            if($dados->senha === $dados->valida_senha) 
            {
                $criteria = new Criteria; 
                $criteria->add('login', '=',  $dados->login);
                $usuario = new Repository('Usuarios');
                $usuario_info = $usuario->count($criteria);

                if ($usuario_info == "1")
                {
                    throw new Exception("Usuário já cadastrado");
                }

                unset($dados->valida_senha);
                $dados->senha = md5($dados->senha);
                $object = new $class; // instancia objeto
                $object->fromArray( (array) $dados); // carrega os dados
                $object->store(); // armazena o objeto
                
                $dados->id = $object->id;
                $this->form->setData($dados);
                
                Transaction::close(); // finaliza a transação
                new Message('info', 'Dados armazenados com sucesso');
            }
            else 
            {
                throw new Exception("As senha nao conferem");
            }
            
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }
}