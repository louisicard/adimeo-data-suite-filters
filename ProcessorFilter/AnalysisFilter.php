<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class AnalysisFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Analysis filter";
  }

  function getFields()
  {
    return array('tokens');
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
      'analyzer' => array(
        'label' => 'Analyzer',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'text' => 'Text to analyze',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $text = $this->getArgumentValue('text', $document);
      $r = $datasource->getExecIndexManager()->analyze($this->getSettings()['index_name'], $this->getSettings()['analyzer'], $text);
      return [
        'tokens' => isset($r['tokens']) ? $r['tokens'] : []
      ];
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array('doc' => NULL);
  }

}