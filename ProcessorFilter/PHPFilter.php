<?php

namespace AdimeoDataSuite\ProcessorFilter;


use AdimeoDataSuite\Bundle\CommonsBundle\Model\ProcessorFilter;

class PHPFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "PHP filter";
  }

  function getSettingFields()
  {
    return array(
      'php_code' => array(
        'label' => 'PHP Code',
        'type' => 'textarea',
        'required' => true
      )
    );
  }

  function getFields()
  {
    return array('return');
  }

  function getArguments()
  {
    return array();
  }

  private function evalCode(&$document, $code){
    return eval($code);
  }

  function execute(&$document)
  {
    $settings = $this->getSettings();
    $return = NULL;
    if(isset($settings['php_code'])){
      try{
        $return = $this->evalCode($document, $settings['php_code']);
      } catch (\Exception $ex) {
        $return  = '';
      }
    }
    return array('return' => $return);
  }

}