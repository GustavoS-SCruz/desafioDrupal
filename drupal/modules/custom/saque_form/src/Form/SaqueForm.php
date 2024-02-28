<?php

namespace Drupal\saque_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a saque_form form.
 */
class SaqueForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'saque_form_saque';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['valor'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DIgite o valor que deseja sacar'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sacar'),
    ];

    if ($form_state->isRebuilding()) {

      $output_valor = number_format($form_state->getValue('valor'), 2, ',', '.');
      $distribuicao_cedulas = $form_state->get('distribuicao_cedulas');

      $form['valor_saque'] = [
        '#type' => 'markup',
        '#markup' => $this->t("<b>Saque:</b> R$ ".$output_valor."<br>"),
      ];

      $form['qtd_cedulas'] = [
        '#type' => 'markup',
        '#markup' => $this->t("<b>Quantidade de Cédulas:</b> ".$form_state->get('qtd_cedulas')."<br>"),
      ];

      $form['distribuicao_cedulas'] = [
        '#type' => 'markup',
        '#markup' => $this->t("<b>Distribuição de Cédulas:</b><br>".implode($distribuicao_cedulas)),
      ];

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $valorTotal = $form_state->getValue('valor');

    if(!preg_match("/^\d+$/", $valorTotal)){
      $form_state->setErrorByName('valor', $this->t('Não aceitamos esse tipo de caractere, digite um número inteiro positivo'));
      unset($form['valor_saque']);
      unset($form['qtd_cedulas']);
      unset($form['distribuicao_cedulas']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $valorTotal = $form_state->getValue('valor');
    $cedulas = [100, 50, 10, 5, 2, 1];
    $qtd_cedulas = 0;
    $distribuicao_cedulas = [];
    $valorCalc = $valorTotal;

    for ($i=0; 0 < $valorCalc; $i++) { 
      $qtd_contada = 0;
      while($valorCalc>=$cedulas[$i]){
        $valorCalc -= $cedulas[$i];
        $qtd_cedulas++;
        $qtd_contada++;
      }
      $distribuicao_cedulas[$i] = "Você receberá $qtd_contada cédula(s) de R$$cedulas[$i]  <br>";
    }

    $form_state->set('qtd_cedulas', $qtd_cedulas);
    $form_state->set('distribuicao_cedulas', $distribuicao_cedulas);

    $this->messenger()->addStatus($this->t('Saque efetuado com sucesso!'));
    $form_state->setRebuild();
  }

}