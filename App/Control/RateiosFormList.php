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
        $rateios = $this->load();
        $items = array();
        foreach ($rateios as $obj_rateio) {
            $items[$obj_rateio->periodo] = $obj_rateio->periodo;
        }//echo '<pre>';print_r($rateios);print_r($items);die();

        $periodo->addItems($items);

        $action = new Action(array('RateiosFormList', 'onReload'));
        
        $this->form->addField('Período',   $periodo, '40%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Limpar Busca', $action);
        $this->form->addAction('Cadastrar Novo', new Action(array(new RateiosForm, 'onEdit')));
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id   = new DatagridColumn('id',             'ID',    'center',  '10%');
        $periodo= new DatagridColumn('periodo',      'Período', 'center',   '30%');
        $valor_ic  = new DatagridColumn('valor_ic','Valor IC','center',   '30%');
        $valor_total  = new DatagridColumn('valor_total',        'Valor Total.',    'center',  '15%');
        $equipamentos    = new DatagridColumn('total_equip',    'Equipamentos',     'center',  '15%');
        
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
        
        try
        {
            /*Transaction::open( $this->connection );
            $repository = new Repository( $this->activeRecord );
            // cria um critério de seleção de dados
            $criteria = new Criteria;
            $criteria->setProperty('order', 'id');
            
            if (isset($this->filters))
            {
                foreach ($this->filters as $filter)
                {
                    $criteria->add($filter[0], $filter[1], $filter[2], $filter[3]);
                }
            }
            
            // carreta os objetos que satisfazem o critério
            $objects = $repository->load($criteria);*/
            if ($dados->periodo)
            {
                $objects = $this->load($dados->periodo);                
            }
            else
            {
                $objects = $this->load();                
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {   
                    // adiciona o objeto na DataGrid
                    $this->datagrid->addItem($object);
                }
            }
            Transaction::close();
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
           
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
    
    /**
     * Exibe a página
     */
    public function load()
    {         
        Transaction::open('db');
        $obj = Rateios::getAll();
        Transaction::close();
        return $obj;
    }
}
