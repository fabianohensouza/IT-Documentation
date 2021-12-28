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
    private $cooperativas;
    
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
        $this->cooperativas = Cooperativas::all();
        Transaction::close();
        
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_rateios'));
        $this->form->setTitle('Rateios');
        
        // cria os campos do formulário
        $id      = new Entry('id');
        $periodo   = new Month('periodo');
        $data     = new Entry('data');
        $valor_total = new Number('valor_total');
        $valor_ic = new Number('valor_ic');
        $valor_nao_ic = new Number('valor_nao_ic');
        $total_equip  = new Number('total_equip');
        
        $coop_info = [];
        foreach($this->cooperativas as $info)
        {
            $coop_info["minutos_{$info->id}"]  = new Number("info[{$info->id}][minutos]");
            $coop_info["minutos_{$info->id}"]->setValue(0);
        }

        $id->setEditable(FALSE);
        $data->setEditable(FALSE);
        $valor_ic->setEditable(FALSE);
        $valor_nao_ic->setEditable(FALSE);
        $total_equip->setEditable(FALSE);

        $valor_total->min="0.00";
        $valor_total->max="10000.00";
        $valor_total->step="0.01";

        $data->setValue(date("d/m/Y"));

        $action = new Action(array('RateiosFormList', 'onReload'));
        
        $this->form->addField('ID',    $id, '30%');
        $this->form->addField('Período', $periodo, '70%');
        $this->form->addField('Data do cálculo',   $data, '70%');
        $this->form->addField('Valor Total',   $valor_total, '70%');
        $this->form->addField('Valor IC',   $valor_ic, '70%');
        $this->form->addField('Valor Não IC',   $valor_nao_ic, '70%');
        $this->form->addField('Equipamentos',   $total_equip, '70%');


        foreach($this->cooperativas as $info)
        {
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
        
        foreach ($this->cooperativas as $coop) {
            $dados['info'][$coop->id]['qt_equip'] = $coop->qt_equip;
            $dados['info'][$coop->id]['ic'] = $coop->ic;

        }
        
        $rateio['qtd_equip'] = $rateio['minutos_total'] = $rateio['minutos_ic'] = $rateio['valor_total'] = 0;
         
        foreach($dados['info'] as $key => $value)
        {   
            $rateio['minutos_total'] += $value['minutos'];

            if($value['ic'] == 'Sim')
            {
                $rateio['minutos_ic'] += $value['minutos'];
                $rateio['qtd_equip'] += $value['qt_equip'];
            }         
        }

        $dados['total_equip'] = $rateio['qtd_equip'];

        $rateio['minutos_nao_ic']   = $rateio['minutos_total'] - $rateio['minutos_ic'];        
        $rateio['valor_minuto']     = $dados['valor_total'] / $rateio['minutos_total'];
        $rateio['valor_nao_ic']     =  $rateio['valor_minuto'] * $rateio['minutos_nao_ic'];
        $rateio['valor_ic']         =  $dados['valor_total'] - $rateio['valor_nao_ic'];

        $rateio['valor_minuto_ic']  = ( $rateio['valor_ic'] / 2 ) / $rateio['minutos_ic'];
        $rateio['valor_equip_ic']   = ( $rateio['valor_ic'] / 2 ) / $rateio['qtd_equip'];
        echo '<pre>';print_r($_REQUEST);

        foreach($dados['info'] as $key => $value)
        {
            if($value['ic'] == 'Sim')
            {
                $rateio['ic'][$key]['minutos']              = $value['minutos'];
                $rateio['ic'][$key]['equipamentos']         = $value['qt_equip'];
                $rateio['ic'][$key]['valor_minutos']        = $value['minutos'] * $rateio['valor_minuto_ic'];
                $rateio['ic'][$key]['valor_equipamentos']   = $value['qt_equip'] * $rateio['valor_equip_ic'];
                $rateio['ic'][$key]['valor_coop']           = $rateio['ic'][$key]['valor_minutos'] + $rateio['ic'][$key]['valor_equipamentos'];
                $rateio['valor_total']                     += $rateio['ic'][$key]['valor_coop'];
            }
            else
            {   
                $rateio['nao_ic'][$key]['minutos']  = $value['minutos'];
                $rateio['nao_ic'][$key]['total']    = $value['minutos'] * $rateio['valor_minuto'];
                $rateio['valor_total']             += $rateio['nao_ic'][$key]['total'];
            }           
        }

        $dados['valor_ic'] = $rateio['valor_ic'];
        $dados['valor_nao_ic'] = $rateio['valor_nao_ic'];
        
        unset($dados['info']);
        unset($dados['class']);
        unset($dados['method']);

        $dados['id'] = str_replace('-', '', $dados['periodo']);
        $dados['rateio'] = serialize($rateio);
        
        try
        {
            Transaction::open( $this->connection );
            
            $class = $this->activeRecord;
            echo '<hr>';print_r($this->formatData($dados));
            die();
            
            $object = new $class; // instancia objeto
            $object->fromArray( (array) $dados); // carrega os dados
            $object->store(); // armazena o objeto
            
            $dados->id = $object->id;
            //$this->form->setData($this->formatData($dados));
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
            
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

    function formatData($data)
    {
        $unserialized = unserialize($data['rateio']);
        $info = $unserialized['ic'] + $unserialized['nao_ic'];
        ksort($info);
        unset($data['rateio']);

        foreach ($info as $key => $value) {
            $data['info'][$key]['minutos'] = $value['minutos'];
        }
        
        return $data;
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