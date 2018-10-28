<?php

namespace AdimeoDataSuite\Bundle\ProcessorsBundle\ProcessorFilter;

use AdimeoDataSuite\Bundle\CommonsBundle\Model\ProcessorFilter;

class ConcatenateFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Concatenate";
  }

  function getFields()
  {
    return array('result');
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
      'field_1' => 'Field 1',
      'field_2' => 'Field 2'
    );
  }

  function execute(&$document)
  {
    $field1 = $this->getArgumentValue('field_1', $document);
    $field2 = $this->getArgumentValue('field_2', $document);
    $settings = $this->getSettings();
    $separator = isset($settings['separator']) ? $settings['separator'] : '';
    return array('result' => $field1 . $separator . $field2);
  }

}