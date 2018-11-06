<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class SmartMapper extends ProcessorFilter {
  
  public function getDisplayName() {
    return "Smart mapper";
  }

  function getSettingFields()
  {
    return array(
      'force_index' => array(
        'label' => 'Force indexing all fields',
        'type' => 'boolean',
        'required' => false
      )
    );
  }
  
  
  public function getFields() {
    return array('smart_array');
  }

  public function getArguments() {
    return array('source_array' => 'Source array');
  }

  function execute(&$document, Datasource $datasource) {
    return array('smart_array' => $this->getArgumentValue('source_array', $document));
  }

}
