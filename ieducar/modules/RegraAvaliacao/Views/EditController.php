<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'RegraAvaliacao_Model_RegraDataMapper';
  protected $_titulo            = 'Cadastro de regra de avalia��o';
  protected $_processoAp        = 947;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  protected $_formMap = array(
    'instituicao' => array(
      'label'  => 'Institui��o',
      'help'   => '',
    ),
    'nome' => array(
      'label'  => 'Nome',
      'help'   => 'Nome por extenso do componente.',
    ),
    'tipoNota' => array(
      'label'  => 'Sistema de nota',
      'help'   => ''
    ),
    'tipoProgressao' => array(
      'label'  => 'Progress�o',
      'help'   => 'Selecione o m�todo de progress�o para a regra.'
    ),
    'tabelaArredondamento' => array(
      'label'  => 'Tabela de arredondamento de nota',
      'help'   => ''
    ),
    'media' => array(
      'label'  => 'M�dia final para promo��o',
      'help'   => 'Informe a m�dia necess�ria para promo��o<br />
                   do aluno, aceita at� 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                   Se o tipo de progress�o for <b>"Progressiva"</b>, esse<br />
                   valor n�o ser� considerado.'
    ),
    'mediaRecuperacao' => array(
      'label'  => 'M�dia final para promo��o',
      'help'   => 'Informe a m�dia necess�ria para promo��o<br />
                   do aluno, aceita at� 3 casas decimais. Exemplos: 5,00; 6,725, 6.<br >
                   Desconsidere esse campo caso selecione o tipo de nota "conceitual"'
    ),
    'formulaMedia' => array(
      'label'  => 'F�rmula de c�lculo da m�dia',
      'help'   => '',
    ),
    'formulaRecuperacao' => array(
      'label'  => 'F�rmula de c�lculo da m�dia de recupera��o',
      'help'   => '',
    ),
    'porcentagemPresenca' => array(
      'label'  => 'Porcentagem de presen�a',
      'help'   => 'A porcentagem de presen�a necess�ria para o aluno ser aprovado.<br />
                   Esse valor � desconsiderado caso o campo "Progress�o" esteja como<br />
                   "N�o progressiva autom�tica - Somente m�dia".<br />
                   Em porcentagem, exemplo: <b>75</b> ou <b>80,750</b>'
    ),
    'parecerDescritivo' => array(
      'label'  => 'Parecer descritivo',
      'help'   => '',
    ),
    'tipoPresenca' => array(
      'label'  => 'Apura��o de presen�a',
      'help'   => ''
    )
  );

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('id', $this->getEntity()->id);

    // Institui��o
    $instituicoes = App_Model_IedFinder::getInstituicoes();
    $this->campoLista('instituicao', $this->_getLabel('instituicao'), $instituicoes,
      $this->getEntity()->instituicao);

    // Nome
    $this->campoTexto('nome', $this->_getLabel('nome'), $this->getEntity()->nome,
      50, 50, TRUE, FALSE, FALSE, $this->_getHelp('nome'));

    // Nota tipo valor
    $notaTipoValor = RegraAvaliacao_Model_Nota_TipoValor::getInstance();
    $this->campoRadio('tipoNota', $this->_getLabel('tipoNota'), $notaTipoValor->getEnums(),
      $this->getEntity()->get('tipoNota'), '', $this->_getHelp('tipoNota'));

    // Tabela de arredondamento
    $tabelaArredondamento = $this->getDataMapper()->findTabelaArredondamento($this->getEntity());
    $tabelaArredondamento = CoreExt_Entity::entityFilterAttr($tabelaArredondamento, 'id', 'nome');
    $this->campoLista('tabelaArredondamento', $this->_getLabel('tabelaArredondamento'),
      $tabelaArredondamento, $this->getEntity()->get('tabelaArredondamento'), '',
      FALSE, $this->_getHelp('tabelaArredondamento'));

    // Tipo progress�o
    $tipoProgressao = RegraAvaliacao_Model_TipoProgressao::getInstance();
    $this->campoRadio('tipoProgressao', $this->_getLabel('tipoProgressao'),
      $tipoProgressao->getEnums(), $this->getEntity()->get('tipoProgressao'), '',
      $this->_getHelp('tipoProgressao'));

    // M�dia
    $this->campoTexto('media', $this->_getLabel('media'), $this->getEntity()->media,
      5, 50, TRUE, FALSE, FALSE, $this->_getHelp('media'));

    // C�lculo m�dia
    $formulas = $this->getDataMapper()->findFormulaMediaFinal();
    $formulas = CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');
    $this->campoLista('formulaMedia', $this->_getLabel('formulaMedia'),
      $formulas, $this->getEntity()->get('formulaMedia'), '', FALSE,
      $this->_getHelp('formulaMedia'));

    // C�lculo m�dia recupera��o
    $formulas = $this->getDataMapper()->findFormulaMediaRecuperacao();
    $formulasArray = array(0 => 'N�o usar recupera��o');
    $formulasArray = $formulasArray + CoreExt_Entity::entityFilterAttr($formulas, 'id', 'nome');

    $this->campoLista('formulaRecuperacao', $this->_getLabel('formulaRecuperacao'),
      $formulasArray, $this->getEntity()->get('formulaRecuperacao'), '', FALSE,
      $this->_getHelp('formulaRecuperacao'), '', FALSE, FALSE);

    // Porcentagem presen�a
    $this->campoTexto('porcentagemPresenca', $this->_getLabel('porcentagemPresenca'),
      $this->getEntity()->porcentagemPresenca, 5, 50, TRUE, FALSE, FALSE,
      $this->_getHelp('porcentagemPresenca'));

    // Parecer descritivo
    $parecerDescritivo = RegraAvaliacao_Model_TipoParecerDescritivo::getInstance();
    $this->campoRadio('parecerDescritivo', $this->_getLabel('parecerDescritivo'),
      $parecerDescritivo->getEnums(), $this->getEntity()->get('parecerDescritivo'), '',
      $this->_getHelp('parecerDescritivo'));

    // Presen�a
    $tipoPresenca = RegraAvaliacao_Model_TipoPresenca::getInstance();
    $this->campoRadio('tipoPresenca', $this->_getLabel('tipoPresenca'),
      $tipoPresenca->getEnums(), $this->getEntity()->get('tipoPresenca'), '',
      $this->_getHelp('tipoPresenca'));
  }
}