<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class AssociativeArraySelectorFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Associative array selector";
  }

  function getFields()
  {
    return array('value');
  }

  function getSettingFields()
  {
    return array(
      'key' => array(
        'label' => 'Key',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'array' => 'Input array'
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    $settings = $this->getSettings();
    $array = $this->getArgumentValue('array', $document);
    if(strpos($settings['key'], '##') === FALSE){
      if ($array != null && is_array($array) && isset($array[$settings['key']])) {
        return array('value' => $array[$settings['key']]);
      }
    }
    else{
      $keys = explode('##', $settings['key']);
      for($i = 0; $i < count($keys); $i++){
        if($i == 0){
          if(isset($array[$keys[$i]])){
            $tmp = $array[$keys[$i]];
          }
        }
        else{
          if(isset($tmp[$keys[$i]])){
            $tmp = $tmp[$keys[$i]];
          }
        }
      }
      if(isset($tmp))
        return array('value' => $tmp);
    }
    return array('value' => null);
  }

}