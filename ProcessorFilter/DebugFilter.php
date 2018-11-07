<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class DebugFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Debug filter";
  }

  function getFields()
  {
    return array();
  }

  function getSettingFields()
  {
    return array(
      'fields_to_dump' => array(
        'label' => 'Fields to dump',
        'type' => 'string',
        'required' => false
      ),
      'no_index' => array(
        'label' => 'Prevent indexing',
        'type' => 'boolean',
        'required' => false
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

    if(isset($settings['fields_to_dump'])){
      $fields = explode(',', $settings['fields_to_dump']);
      $datasource->getOutputManager()->writeLn('');
      $datasource->getOutputManager()->writeLn('####################################################');
      foreach($fields as $field){
        if(isset($document[$field])){
          $datasource->getOutputManager()->writeLn('FIELD: ' . $field);
          $datasource->getOutputManager()->dumpArray($document[$field]);
          $datasource->getOutputManager()->writeLn('');
          $datasource->getOutputManager()->writeLn('----------------------------------------------------');
        }
      }
      $datasource->getOutputManager()->writeLn('####################################################');
      $datasource->getOutputManager()->writeLn('');
    }

    if(isset($settings['no_index']) && $settings['no_index']){
      $document = array();
    }
    return array();
  }

}