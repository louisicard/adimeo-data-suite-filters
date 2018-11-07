<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class HTMLTextExtractorFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "HTML text extractor";
  }

  function getFields()
  {
    return array('output');
  }

  function getSettingFields()
  {
    return array();
  }

  function getArguments()
  {
    return array('html_source' => 'HTML source');
  }

  function execute(&$document, Datasource $datasource)
  {
    $html = $this->getArgumentValue('html_source', $document);
    try{
      $tidy = tidy_parse_string($html, array(), 'utf8');
      $body = tidy_get_body($tidy);
      $html = $body->value;
    } catch (\Exception $ex) {

    }
    $html = html_entity_decode($html, ENT_COMPAT | ENT_HTML401, 'utf-8');
    $output = html_entity_decode(trim(str_replace('&nbsp;', ' ', htmlentities(preg_replace('!\s+!', ' ', trim(preg_replace('#<[^>]+>#', ' ',$html))), null, 'utf-8'))));
    return array('output' => $output);
  }

}