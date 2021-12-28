<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Hidden;
use Livro\Widgets\Form\Number;
use Livro\Widgets\Form\Month;
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
class RateiosForm extends Page
{
    private $form; // formulário
    private $connection;
    private $activeRecord;
    
    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        $this->activeRecord = 'Rateios';
        $this->connection   = 'db';
        
        // carrega as Cooperativas do banco de dados
        Transaction::open('db');
        $cooperativas = Cooperativas::all();
        Transaction::close();
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_rateios'));
        $this->form->setTitle('Rateios');
        
        // cria os campos do formulário
        $id      = new Entry('id');
        $periodo   = new Month('periodo');
        $data     = new Entry('data');
        $valor_ic = new Number('valor_ic');
        $valor_total = new Number('valor_total');
        $equipamentos  = new Number('equipamentos');
        
        $coop_info = [];
        foreach($cooperativas as $info)
        {
            $coop_info["ic_{$info->id}"]  = new Hidden("ic[{$info->id}]");
            $coop_info["equipamentos_{$info->id}"]  = new Hidden("equipamentos[{$info->id}]");
            $coop_info["minutos_{$info->id}"]  = new Number("minutos[{$info->id}]");

            $coop_info["ic_{$info->id}"]->setValue($info->ic);
            $coop_info["equipamentos_{$info->id}"]->setValue($info->qt_equip);
            $coop_info["minutos_{$info->id}"]->setValue(0);
        }

        $id->setEditable(FALSE);
        $data->setEditable(FALSE);
        $valor_ic->setEditable(FALSE);
        $equipamentos->setEditable(FALSE);

        $valor_total->min="0.00";
        $valor_total->max="10000.00";
        $valor_total->step="0.01";

        $data->setValue(date("d/m/Y"));

        $action = new Action(array('RateiosFormList', 'onReload'));
        
        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Período', $periodo, '70%');
        $this->form->addField('Data do cálculo',   $data, '70%');
        $this->form->addField('Valor IC',   $valor_ic, '70%');
        $this->form->addField('Valor Total',   $valor_total, '70%');
        $this->form->addField('Equipamentos',   $equipamentos, '70%');


        foreach($cooperativas as $info)
        {
            $this->form->addField("",   $coop_info["ic_{$info->id}"], '30%');
            $this->form->addField("",   $coop_info["equipamentos_{$info->id}"], '30%');
            $this->form->addField(" {$info->id} - Minutos:",   $coop_info["minutos_{$info->id}"], '10%');
        }

        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
        $this->form->addAction('Retornar', $action);
        
        // adiciona o formulário na página
        parent::add($this->form);
    }

    function onSave()
    {
        $dados = $_REQUEST;
        $rateio['qtd_equip'] = $rateio['minutos_total'] = $rateio['minutos_ic'] = $rateio['valor_total'] = 0;
         
        foreach($dados['equipamentos'] as $key => $value)
        {
            $rateio['minutos_total'] += $dados['minutos'][$key];

            if($dados['ic'][$key] == 'Sim')
            {
                $rateio['minutos_ic'] += $dados['minutos'][$key];
                $rateio['qtd_equip'] += $value;
            }           
        }

        $dados['total_equip'] = $rateio['qtd_equip'];

        $rateio['minutos_nao_ic']   = $rateio['minutos_total'] - $rateio['minutos_ic'];        
        $rateio['valor_minuto']     = $dados['valor_total'] / $rateio['minutos_total'];
        $rateio['valor_nao_ic']     =  $rateio['valor_minuto'] * $rateio['minutos_nao_ic'];
        $rateio['valor_ic']         =  $dados['valor_total'] - $rateio['valor_nao_ic'];

        $rateio['valor_minuto_ic']  = ( $rateio['valor_ic'] / 2 ) / $rateio['minutos_ic'];
        $rateio['valor_equip_ic']   = ( $rateio['valor_ic'] / 2 ) / $rateio['qtd_equip'];

        foreach($dados['ic'] as $key => $value)
        {
            if($value == 'Sim')
            {
                $rateio['ic'][$key]['minutos']              = $dados['minutos'][$key];
                $rateio['ic'][$key]['equipamentos']         = $dados['equipamentos'][$key];
                $rateio['ic'][$key]['valor_minutos']        = $dados['minutos'][$key] * $rateio['valor_minuto_ic'];
                $rateio['ic'][$key]['valor_equipamentos']   = $dados['equipamentos'][$key] * $rateio['valor_equip_ic'];
                $rateio['ic'][$key]['valor_coop']           = $rateio['ic'][$key]['valor_minutos'] + $rateio['ic'][$key]['valor_equipamentos'];
                $rateio['valor_total']                     += $rateio['ic'][$key]['valor_coop'];
            }
            else
            {   
                $rateio['nao_ic'][$key]['minutos']  = $dados['minutos'][$key];
                $rateio['nao_ic'][$key]['total']    = $dados['minutos'][$key] * $rateio['valor_minuto'];
                $rateio['valor_total']             += $rateio['nao_ic'][$key]['total'];
            }           
        }

        $dados['valor_ic'] = $rateio['valor_ic'];
        $dados['valor_nao_ic'] = $rateio['valor_nao_ic'];

        unset($dados['equipamentos']);
        unset($dados['ic']);
        unset($dados['minutos']);
        unset($dados['class']);
        unset($dados['method']);

        $dados['id'] = str_replace('-', '', $dados['periodo']);
        $dados['rateio'] = serialize($rateio);

        
        try
        {
            Transaction::open( $this->connection );
            
            $class = $this->activeRecord;
            //$dados = $this->form->getData();
            echo '<pre>';print_r($dados);echo '<hr>';print_r($this->form->getData());die();
            
            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados
            $object->store(); // armazena o objeto
            
            $dados->id = $object->id;
            //$this->form->setData($dados);
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
            
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }
}



/*
        
<div class="form-group">
    <label class="col-sm-4 control-label">ID</label>
    <div class="col-sm-2">
        <input class="form-control" name="id" type="text" style="width:100%" readonly="1">
    </div>
    <div class="col-sm-2">
        <input class="form-control" name="id" type="text" style="width:100%">
    </div>
</div>

*/