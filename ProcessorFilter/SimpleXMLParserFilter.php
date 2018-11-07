<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class SimpleXMLParserFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Simple XML Parser";
  }

  function getFields()
  {
    return array('doc');
  }

  function getSettingFields()
  {
    return array();
  }

  function getArguments()
  {
    return array(
      'xml' => 'XML source',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try{
      $xml = $this->getArgumentValue('xml', $document);
      if(file_exists($xml)){
        $xml = file_get_contents($xml);
      }
      $xmlDoc = simplexml_load_string(str_replace('xmlns=', 'ns=', $xml));
      if($xmlDoc) {
        foreach ($xmlDoc->getDocNamespaces() as $strPrefix => $strNamespace) {
          $xmlDoc->registerXPathNamespace($strPrefix, $strNamespace);
        }

        return array('doc' => $xmlDoc);
      }
    }catch(\Exception $ex){

    }
    return array('doc' => null);
  }

}