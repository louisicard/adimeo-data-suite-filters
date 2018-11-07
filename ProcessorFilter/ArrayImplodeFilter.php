<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class ArrayImplodeFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Array implode";
  }

  function getFields()
  {
    return array('string');
  }

  function getSettingFields()
  {
    return array(
      'separator' => array(
        'label' => 'Separator',
        'type' => 'string',
        'trim' => false,
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'array' => 'Array to implode',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    $array = $this->getArgumentValue('array', $document);
    $settings = $this->getSettings();
    $separator = isset($settings['separator']) ? $settings['separator'] : '';
    return array('string' => implode($separator, $array));
  }

}