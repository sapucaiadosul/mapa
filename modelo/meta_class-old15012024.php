<?php

/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class Meta {

    private $id = 0;
    private $numero = 0;
    private $titulo = '';
    private $responsavelId = 0;
    private $responsavelNome = '';
    private $responsavelPessoa = '';
    private $dataInicial = '';
    private $dataFinal = '';
    private $dataConclusao = '';
    private $acaoTotais = null;
    private $acao = null;
    private $acompanhamento = null; //Array de objetos Acompanhamento, variável utilizada no formulário
    private $acompanhamentoTipoMonitoramento = 0; //Quantidade de acompanhamentos de monitoramento, variável utilizada na lista
    private $acompanhamentoTipoInformacao = 0; //Quantidade de acompanhamentos de informação, variável utilizada na lista
    private $acompanhamentoTipoObservacao = 0; //Quantidade de acompanhamentos de observação, variável utilizada na lista
    private $acompanhamentoTipoProblema = 0; //Quantidade de acompanhamentos de problema, variável utilizada na lista
    private $anexo = null; //Array de objetos Anexo, variável utilizada no formulário
    private $anexos = 0; //Quantidade de anexos, variável utilizada na lista
    private $indObjetivo = '';
    private $indOds = '';
    private $indTitulo = '';
    private $indReferencia = 0;
    private $indIndicador = 0;
    private $indUnidade = '';
    private $indSecValor = 0;
    private $indSecDatahora = '';
    private $indMonValor = 0;
    private $indMonDatahora = '';
    private $indValorAnterior = null;
    private $manterMonitoria = null;
    private $criado = '';
    private $criadorId = 0;
    private $modificado = '';
    private $modificadorId = 0;
    private $desativado = false;

    function __construct(
            $id,
            $numero,
            $titulo,
            $responsavelId,
            $responsavelNome,
            $responsavelPessoa,
            $criado = '',
            $criadorId = 0,
            $modificado = '',
            $modificadorId = 0,
            $desativado = false,
            $acompanhamentos = null,
            $anexos = null,
            $qtdAnexos = 0,
            $acompanhamentoTipoObservacao = 0,
            $acompanhamentoTipoInformacao = 0,
            $acompanhamentoTipoProblema = 0,
            $acompanhamentoTipoMonitoramento = 0,
            $dataInicial = '',
            $dataFinal = '',
            $dataConclusao = '',
            $acaoTotais = null,
            $acao = null,
            $indObjetivo = '',
            $indOds = '',
            $indTitulo = '',
            $indReferencia = 0,
            $indIndicador = 0,
            $indUnidade = '',
            $indSecValor = 0,
            $indSecDatahora = '',
            $indMonValor = 0,
            $indMonDatahora = '',
            $indValorAnterior = null,
            $manterMonitoria = null
    ) {
        //Valida campos
        $this->__set('id', $id);
        $this->__set('numero', $numero);
        $this->__set('responsavelId', $responsavelId);
        $this->__set('acao', $acao);
        $this->__set('acompanhamento', $acompanhamentos);
        $this->__set('anexo', $anexos);
        $this->__set('dataInicial', $dataInicial);
        $this->__set('dataFinal', $dataFinal);
        $this->__set('dataConclusao', $dataConclusao);
        if (strlen($titulo) > 0 && mb_strlen($titulo) <= 200)
            $this->titulo = $titulo;
        if (strlen($responsavelNome) > 0 && strlen($responsavelNome) <= 20)
            $this->responsavelNome = $responsavelNome;
        if (strlen($responsavelPessoa) > 0 && mb_strlen($responsavelPessoa) <= 40)
            $this->responsavelPessoa = $responsavelPessoa;
        $this->criado = Funcao::limpar_data($criado);
        if (is_numeric($criadorId) && $criadorId > 0 && strlen($criadorId) <= 5)
            $this->criadorId = $criadorId;
        $this->modificado = Funcao::limpar_data($modificado);
        if (is_numeric($modificadorId) && $modificadorId > 0 && strlen($modificadorId) <= 5)
            $this->modificadorId = $modificadorId;
        if (!is_null($desativado) && $desativado !== false)
            $this->desativado = true;
        if (is_numeric($qtdAnexos) && $qtdAnexos > 0 && strlen($qtdAnexos) <= 9)
            $this->anexos = $qtdAnexos;
        if (is_numeric($acompanhamentoTipoObservacao) && $acompanhamentoTipoObservacao > 0 && strlen($acompanhamentoTipoObservacao) <= 9)
            $this->acompanhamentoTipoObservacao = $acompanhamentoTipoObservacao;
        if (is_numeric($acompanhamentoTipoInformacao) && $acompanhamentoTipoInformacao > 0 && strlen($acompanhamentoTipoInformacao) <= 9)
            $this->acompanhamentoTipoInformacao = $acompanhamentoTipoInformacao;
        if (is_numeric($acompanhamentoTipoProblema) && $acompanhamentoTipoProblema > 0 && strlen($acompanhamentoTipoProblema) <= 9)
            $this->acompanhamentoTipoProblema = $acompanhamentoTipoProblema;
        if (is_numeric($acompanhamentoTipoMonitoramento) && $acompanhamentoTipoMonitoramento > 0 && strlen($acompanhamentoTipoMonitoramento) <= 9)
            $this->acompanhamentoTipoMonitoramento = $acompanhamentoTipoMonitoramento;
        if (is_array($acaoTotais) && count($acaoTotais) > 0)
            $this->acaoTotais = $acaoTotais;
        if (strlen($indObjetivo) > 0 && mb_strlen($indObjetivo) <= 300)
            $this->indObjetivo = $indObjetivo;
        if (is_numeric($indOds) > 0)
            $this->indOds = $indOds;
        if (strlen($indTitulo) > 0 && mb_strlen($indTitulo) <= 200)
            $this->indTitulo = $indTitulo;
        if (strlen($indUnidade) > 0 && mb_strlen($indUnidade) <= 40)
            $this->indUnidade = $indUnidade;
        if (strlen($indSecDatahora) == 19)
            $this->indSecDatahora = $indSecDatahora;
        if (strlen($indMonDatahora) == 19)
            $this->indMonDatahora = $indMonDatahora;

        //Só há valor anterior se está vindo do formulário. Nesse caso, remove os PONTOS de milhares dos valores
        $indValorAnterior = str_replace(',', '.', str_replace('.', '', $indValorAnterior));
        if (is_numeric($indValorAnterior) && $indValorAnterior >= -1 && strlen($indValorAnterior) <= 12)
            $this->indValorAnterior = $indValorAnterior;
        $indReferencia = str_replace(',', '.', !is_null($this->indValorAnterior) ? str_replace('.', '', $indReferencia) : $indReferencia);
        if (is_numeric($indReferencia) && $indReferencia >= 0 && strlen($indReferencia) <= 12)
            $this->indReferencia = $indReferencia;
        $indIndicador = str_replace(',', '.', !is_null($this->indValorAnterior) ? str_replace('.', '', $indIndicador) : $indIndicador);
        if (is_numeric($indIndicador) && $indIndicador >= 0 && strlen($indIndicador) <= 12)
            $this->indIndicador = $indIndicador;

        //Valores devem estar entre referência e indicador
        $indSecValor = str_replace(',', '.', !is_null($this->indValorAnterior) ? str_replace('.', '', $indSecValor) : $indSecValor);
        $indMonValor = str_replace(',', '.', !is_null($this->indValorAnterior) ? str_replace('.', '', $indMonValor) : $indMonValor);
        if ($indIndicador > $indReferencia) {
            /* if($indIndicador < $indSecValor) $indSecValor = $indIndicador;
              if($indSecValor < $indReferencia) $indSecValor = $indReferencia;
              if($indIndicador < $indMonValor) $indMonValor = $indIndicador;
              if($indMonValor < $indReferencia) $indMonValor = $indReferencia; */
        } else {
            /* if($indIndicador > $indSecValor) $indSecValor = $indIndicador;
              if($indSecValor > $indReferencia) $indSecValor = $indReferencia;
              if($indIndicador > $indMonValor) $indMonValor = $indIndicador;
              if($indMonValor > $indReferencia) $indMonValor = $indReferencia; */
        }
        if (is_numeric($indSecValor) && $indSecValor > 0 && strlen($indSecValor) <= 12)
            $this->indSecValor = $indSecValor;
        if (is_numeric($indMonValor) && $indMonValor > 0 && strlen($indMonValor) <= 12)
            $this->indMonValor = $indMonValor;
        if ($manterMonitoria == true)
            $this->manterMonitoria = true;
    }

    public function __get($key) {
        //Formata valores
        switch ($key) {
            case 'indValorReferencia':
                $valor = $this->indReferencia;
                break;
            case 'indValorIndicador':
                $valor = $this->indIndicador;
                break;
            case 'indValorAtual':
                if ($this->indSecDatahora > $this->indMonDatahora)
                    $valor = $this->indSecValor;
                else
                    $valor = $this->indMonValor;
                break;
        }
        //Se a unidade de medida tiver um $, formata com 2 casas decimais
        //Senão, formata apenas com dividor de milhares e com o número de casas decimais existentes no valor
        if (isset($valor))
            return substr_count($this->indUnidade, '$') > 0 ? number_format($valor, 2, ',', '.') : (substr_count($valor, '.') > 0 ? number_format($valor, strlen(explode('.', $valor)[1]), ',', '.') : number_format($valor, 0, '', '.'));

        //Formata datas
        switch ($key) {
            case 'dataInicialFormatada':
                $data = $this->dataInicial;
                break;
            case 'dataFinalFormatada':
                $data = $this->dataFinal;
                break;
            case 'dataConclusaoFormatada':
                $data = $this->dataConclusao;
                break;
        }
        if (isset($data))
            return (strlen($data) == 10 ? (explode('-', $data)[2] . '/' . explode('-', $data)[1] . '/' . explode('-', $data)[0]) : '');

        //Como irá exibir como texto, na inexistência de número retorna vazio
        if ($key == 'numero')
            return ($this->numero == 0 ? '' : $this->numero);

        return (isset($this->$key) ? $this->$key : '');
    }

    public function __set($key, $value) {
        if ($key == 'id')
            if (is_numeric($value) && $value > 0 && strlen($value) <= 5)
                $this->id = $value;
        if ($key == 'numero')
            if (is_numeric($value) && $value > 0 && strlen($value) <= 4)
                $this->numero = $value;
        if ($key == 'responsavelId')
            if (is_numeric($value) && $value > 1 && strlen($value) <= 3)
                $this->responsavelId = $value;
        if ($key == 'acao')
            $this->acao = $this->validarArrayObjetos($value, 'Acao');
        if ($key == 'acompanhamento')
            $this->acompanhamento = $this->validarArrayObjetos($value, 'Acompanhamento');
        if ($key == 'anexo')
            $this->anexo = $this->validarArrayObjetos($value, 'Anexo');
        if ($key == 'dataInicial')
            if (strlen($value) == 10)
                $this->dataInicial = Funcao::limpar_data($value);
        if ($key == 'dataFinal')
            if (strlen($value) == 10)
                $this->dataFinal = Funcao::limpar_data($value);
        if ($key == 'dataConclusao')
            if (strlen($value) == 10)
                $this->dataConclusao = Funcao::limpar_data($value);
    }

    public function tipoClass($tipo) {
        //Valida parâmetro
        if (!is_numeric($tipo) || $tipo < 0 || $tipo > 3)
            return '';

        //Classes CSS para caixas de texto
        //alert-default foi criada, pois não existia no bootstrap
        $class = array('alert-default', 'alert-success', 'alert-danger', 'alert-warning');

        return $class[$tipo];
    }

    private function validarArrayObjetos($array, $classe) {
        if (is_array($array)) {
            foreach ($array as $objeto) {
                //Valida cada elemento
                if (!is_object($objeto) || get_class($objeto) != $classe) {
                    //Se um elemento não for objeto da classe informada, limpa campo
                    $array = null;
                    break;
                }
            }

            //Verifica se os elementos passaram pela validação
            if (count($array) > 0)
                return $array;
            else
                return null;
        } elseif (is_null($array))
            return null;
    }

}

?>
