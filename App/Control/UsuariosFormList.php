<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Database\Transaction;

use Livro\Traits\DeleteTrait;
use Livro\Traits\ReloadTrait;

use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Container\Panel;

/**
 * Página de produtos
 */
class UsuariosFormList extends Page
{
    private $form;
    private $datagrid;
    private $loaded;
    private $connection;
    private $activeRecord;
    private $filters;
    
    use DeleteTrait;
    use ReloadTrait {
        onReload as onReloadTrait;
    }
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        
        // Define o Active Record
        $this->activeRecord = 'Usuarios';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_usuarios'));
        $this->form->setTitle('Usuarios');
        
        // cria os campos do formulário
        $nome = new Entry('nome');

        $action = new Action(array('UsuariosFormList', 'onReload'));
        
        $this->form->addField('Nome',   $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Limpar Busca', $action);
        $this->form->addAction('Cadastrar Novo', new Action(array(new UsuariosForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id   = new DatagridColumn('id',             'ID',    'center',  '5%');
        $nome= new DatagridColumn('nome', 'Nome', 'center',   '30%');
        $login  = new DatagridColumn('login','Login','center',   '10%');
        $email  = new DatagridColumn('email',        'E-mail',    'center',  '25%');
        $permissao    = new DatagridColumn('permissao',    'Permissao',     'center',  '10%');
        $status    = new DatagridColumn('status',    'Status',     'center',  '10%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($login);
        $this->datagrid->addColumn($email);
        $this->datagrid->addColumn($permissao);
        $this->datagrid->addColumn($status);
        
        $this->datagrid->addAction( 'Editar',  new Action([new UsuariosForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']),          'id', 'fa fa-trash fa-lg red');
        $this->datagrid->addAction( 'Excluir', new Action([new SenhaForm, 'onEdit']),          'id', 'fas fa-key fa-lg');
        
        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
    }
    
    public function onReload()
    {
        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();
        
        // verifica se o usuário preencheu o formulário
        if ($dados->nome)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['nome', 'like', "%{$dados->nome}%", 'and'];
        }
        
        $this->onReloadTrait();   
        $this->loaded = true;
    }
    
    /**
     * Exibe a página
     */
    public function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
