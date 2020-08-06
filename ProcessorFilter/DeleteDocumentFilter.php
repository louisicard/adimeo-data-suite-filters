<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class DeleteDocumentFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Delete document filter";
  }

  function getFields()
  {
    return array('doc');
  }

  function getSettingFields()
  {
    return array(
      'index_name' => array(
        'label' => 'Index name',
        'type' => 'choice',
        'bound_to' => 'index',
        'required' => true
      ),
      'mapping_name' => array(
        'label' => 'Mapping name',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'doc_id' => 'Document ID',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $datasource->getExecIndexManager()->getServerClient()->delete($this->getSettings()['index_name'], $this->getArgumentValue('doc_id', $document), $datasource->getExecIndexManager()->isLegacy() ? $this->getSettings()['mapping_name'] : null);
      $datasource->getExecIndexManager()->flush();
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    $document = [];
    return array('doc' => NULL);
  }

}