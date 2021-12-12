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
class RateiosFormList extends Page
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
        $this->activeRecord = 'Rateios';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_rateios'));
        $this->form->setTitle('Rateios');
        
        // cria os campos do formulário
        $periodo = new Combo('periodo');

        // carrega todos os rateios do banco de dados
        Transaction::open('db');
        $rateios = Rateios::all();
        $items = array();
        foreach ($rateios as $obj_rateio) {
            $items[$obj_rateio->periodo] = $obj_rateio->periodo;
        }
        Transaction::close();
        
        $periodo->addItems($items);

        $action = new Action(array('CooperativasFormList', 'onReload'));
        
        $this->form->addField('Período',   $periodo, '40%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Limpar Busca', $action);
        $this->form->addAction('Cadastrar Novo', new Action(array(new RateiosForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id   = new DatagridColumn('id',             'Código',    'center',  '10%');
        $periodo= new DatagridColumn('periodo',      'Período', 'center',   '30%');
        $valor_ic  = new DatagridColumn('valor_ic','Valor IC','center',   '30%');
        $valor_total  = new DatagridColumn('valor_total',        'Valor Total.',    'center',  '15%');
        $equipamentos    = new DatagridColumn('equipamentos',    'Equipamentos',     'center',  '15%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($periodo);
        $this->datagrid->addColumn($valor_ic);
        $this->datagrid->addColumn($valor_total);
        $this->datagrid->addColumn($equipamentos);
        
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
        if ($dados->periodo)
        {
            // filtra pela descrição do produto
            $this->filters[] = ['periodo', 'like', "%{$dados->periodo}%", 'and'];
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
