<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Session\Session;

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
    /*use ReloadTrait {
        onReload as onReloadTrait;
    }*/
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        // instancia nova seção
        new Session;
        
        $coop = (isset($_GET['id']))? $_GET['id'] : NULL;
        Session::setValue('coop', $coop);
        
        // Define o Active Record
        $this->activeRecord = 'Aplicacoes';
        $this->connection   = 'db';
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_busca_aplicacoes'));
        $this->form->setTitle("{$coop} - Aplicações");
        
        // cria os campos do formulário
        $nome = new Entry('$nome');
        
        $action = new Action(array('AplicacoesForm', 'onReload'));
        $action->setParameter('cod_coop', $coop);
        $action2 = new Action(array('CooperativaServicesForm', 'onReload'));
        $action2->setParameter('cod_coop', $coop);

        $this->form->addField('Nome',   $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Cadastrar', $action);
        $this->form->addAction('Retornar', $action2);
        
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);
        
        // instancia as colunas da Datagrid
        $id                 = new DatagridColumn('id', 'ID',    'center',  '5%');
        $cod_coop           = new DatagridColumn('cod_coop', 'Cooperativa', 'center',   '10%');
        $nome               = new DatagridColumn('nome', 'Nome', 'center',   '20%');
        $desenvolvedor      = new DatagridColumn('desenvolvedor', 'Desenvolvedor', 'center',   '20%');
        $tipo_hospedagem    = new DatagridColumn('tipo_hospedagem', 'Tipo de hospedagem','center',   '20%');
        $endereco           = new DatagridColumn('endereco', 'Endereço de acesso','center',   '25%');
        
        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($cod_coop);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($desenvolvedor);
        $this->datagrid->addColumn($tipo_hospedagem);
        $this->datagrid->addColumn($endereco);

        $this->datagrid->addAction( 'Editar',  new Action([new AplicacoesForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue', 'cod_coop');
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
        if (isset($dados->nome))
        {
            // filtra pela descrição do produto
            $this->filters[] = ['nome', 'like', "%{$dados->nome}%", 'and'];
        }

        try
        {
            Transaction::open( $this->connection );

            $repository = new Repository( $this->activeRecord );

            // cria um critério de seleção de dados
            $criteria = new Criteria;
            $criteria->add('cod_coop', '=',  $_GET['id']);
            $criteria->setProperty('order', 'id');
            $objects = $repository->load($criteria);
            
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
        
        //$this->onReloadTrait();   
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
