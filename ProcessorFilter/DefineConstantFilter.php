<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class DefineConstantFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Define constant value";
  }

  function getFields()
  {
    return array('value');
  }

  function getSettingFields()
  {
    return array(
      'value' => array(
        'label' => 'Value',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array();
  }

  function execute(&$document, Datasource $datasource)
  {
    $settings = $this->getSettings();
    return array('value' => $settings['value']);
  }

}