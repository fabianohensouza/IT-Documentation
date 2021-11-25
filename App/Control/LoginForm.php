<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Password;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;
use Livro\Session\Session;
use Livro\Database\Transaction;
use Livro\Database\Criteria;
use Livro\Database\Repository;

class LoginForm extends Page
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new FormWrapper(new Form('form_login'));
        $this->form->setTitle('Login');
        
        $login = new Entry('login');
        $senha = new Password('senha');
        
        $this->form->addField('Login', $login, 200);
        $this->form->addField('Senha', $senha, 200);
        
        $this->form->addAction('Login', new Action( [$this, 'onLogin'] ));
        
        parent::add($this->form);
    }
    
    public function onLogin($param)
    {
        $data = $this->form->getData();
        $data->senha = md5($data->senha);

        Transaction::open('db');
        $criteria = new Criteria; 
        $criteria->add('login', '=',  $data->login);
        $usuario = new Repository('Usuarios');
        $usuario_info = $usuario->load($criteria);
        Transaction::close();

        if($usuario_info[0]->login === $data->login AND $usuario_info[0]->senha === $data->senha) {
            
            Session::setValue('logged', TRUE);
            Session::setValue('id', $usuario_info[0]->id);
            Session::setValue('user', $usuario_info[0]->nome);
            Session::setValue('login', $usuario_info[0]->login);
            Session::setValue('email', $usuario_info[0]->email);
            Session::setValue('permissao', $usuario_info[0]->permissao);
            Session::setValue('status', $usuario_info[0]->status);

            echo "<script> window.location = 'index.php'; </script>";
        }
    }
    
    public function onLogout($param)
    {
        Session::freeSession();
        echo "<script> window.location = 'index.php'; </script>";
    }
}
