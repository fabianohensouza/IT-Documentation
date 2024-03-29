<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
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
class CooperativasFormList extends Page
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
        $this->activeRecord = 'Cooperativas';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_cooperativas'));
        $this->form->setTitle('Cooperativas');
        
        // cria os campos do formulário
        $id = new Combo('id');

        // carrega as cooperativas do banco de dados
        Transaction::open('db');
        $cod_coops = Cooperativas::all();
        $items = array();
        foreach ($cod_coops as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id;
        }
        Transaction::close();
        
        $id->addItems($items);

        $action = new Action(array('CooperativasFormList', 'onReload'));
        
        $this->form->addField('Código',   $id, '40%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Limpar Busca', $action);
        $this->form->addAction('Cadastrar Novo', new Action(array(new CooperativasForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id   = new DatagridColumn('id',             'Código',    'center',  '10%');
        $nome= new DatagridColumn('nome',      'Nome', 'center',   '30%');
        $cidade  = new DatagridColumn('cidade','Cidade','center',   '30%');
        $ic  = new DatagridColumn('ic',        'InfraCredis.',    'center',  '15%');
        $qt_equip    = new DatagridColumn('qt_equip',    'Equipamentos',     'center',  '15%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($cidade);
        $this->datagrid->addColumn($ic);
        $this->datagrid->addColumn($qt_equip);
        
        $this->datagrid->addAction( 'Editar',  new Action([new CooperativasForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']),          'id', 'fa fa-trash fa-lg red');
        $this->datagrid->addAction( 'Documentação de Infraestrutura', new Action([new DocumentacaoReport, '']),          'id', 'far fa-file-alt fa-lg');
        
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
