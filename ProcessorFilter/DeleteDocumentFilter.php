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
      $datasource->getExecIndexManager()->getClient()->delete(array(
        'index' => $this->getSettings()['index_name'],
        'type' => $this->getSettings()['mapping_name'],
        'id' => $this->getArgumentValue('doc_id', $document),
      ));
      $datasource->getExecIndexManager()->getClient()->indices()->flush();
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array('doc' => NULL);
  }

}