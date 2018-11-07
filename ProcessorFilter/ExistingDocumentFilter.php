<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class ExistingDocumentFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Existing document finder";
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
      $json = '{
          "query": {
              "ids": {"values":["' . $this->getArgumentValue('doc_id', $document) . '"]}
          }
      }';
      $res = $datasource->getExecIndexManager()->search($this->getSettings()['index_name'], json_decode($json, TRUE));
      if(isset($res['hits']['hits'][0])){
        return array('doc' => $res['hits']['hits'][0]);
      }
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array('doc' => NULL);
  }

}