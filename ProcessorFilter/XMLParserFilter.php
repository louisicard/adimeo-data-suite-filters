<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class XMLParserFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "XML Parser";
  }

  function getFields()
  {
    return array('xpath');
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
      $doc = new \DOMDocument();
      $doc->loadXML($xml);

      $xpath = new \DOMXPath($doc);
      $result = $xpath->query("//namespace::*");
      foreach ($result as $node) {
        if($node->nodeName == 'xmlns'){
          $xpath->registerNamespace('vendor', $node->nodeValue);
        }
      }

      return array('xpath' => $xpath);
    }catch(\Exception $ex){
      return array('xpath' => null);
    }
  }

}