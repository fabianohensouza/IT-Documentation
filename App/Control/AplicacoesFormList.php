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
class AplicacoesFormList extends Page
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
        $coop = (isset($_GET['id']))? $_GET['id'] : NULL;
        
        // Define o Active Record
        $this->activeRecord = 'Aplicacoes';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_aplicacoes'));
        $this->form->setTitle("{$coop} - Aplicações");
        
        // cria os campos do formulário
        $nome = new Entry('$nome');
        
        $action = new Action(array('AplicacoesForm', 'onReload'));
        $action->setParameter('id', $coop);

        $this->form->addField('Nome',   $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Cadastrar', $action);
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id   = new DatagridColumn('id', 'ID',    'center',  '3%');
        $cod_coop   = new DatagridColumn('cod_coop', 'Cooperativa',    'center',  '5%');
        $nome= new DatagridColumn('nome', 'Nome', 'center',   '20%');
        $tipo= new DatagridColumn('tipo', 'Tipo', 'center',   '8%');
        $so  = new DatagridColumn('so', 'Sistema Operacional','center',   '25%');
        $serial  = new DatagridColumn('serial', 'Serial','center',   '10%');
        $ip_principal  = new DatagridColumn('ip_principal', 'IP','center',   '15%');
        $servidor_status  = new DatagridColumn('servidor_status', 'Status',    'center',  '15%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($cod_coop);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($tipo);
        $this->datagrid->addColumn($so);
        $this->datagrid->addColumn($serial);
        $this->datagrid->addColumn($ip_principal);
        $this->datagrid->addColumn($servidor_status);

        $this->datagrid->addAction( 'Editar',  new Action([new ServidoresForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']),          'id', 'fa fa-trash fa-lg red');
        
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
        if ($dados->id)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['id', 'like', "%{$dados->id}%", 'and'];
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
