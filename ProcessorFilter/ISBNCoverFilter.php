<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class ISBNCoverFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "ISBN Cover finder (Google images)";
  }

  function getFields()
  {
    return array('url');
  }

  function getSettingFields()
  {
    return array(
      'value' => array(
        'label' => 'Value',
        'type' => 'string',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'isbn' => 'ISBN',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $isbn = $this->getArgumentValue('isbn', $document);
      if(!empty($isbn) && is_string($isbn)) {
        $url = 'https://www.google.fr/search?tbm=isch&source=hp&q=' . urlencode($isbn);
        $html = $this->getUrlResponse($url);
        $options = array(
          'hide-comments' => true,
          'tidy-mark' => false,
          'indent' => true,
          'indent-spaces' => 4,
          'new-blocklevel-tags' => 'article,header,footer,section,nav,figure',
          'new-inline-tags' => 'video,audio,canvas,ruby,rt,rp,time',
          'vertical-space' => false,
          'output-xhtml' => true,
          'wrap' => 0,
          'wrap-attributes' => false,
          'break-before-br' => false,
        );
        $dom = new \DOMDocument();
        try{
          $dom->loadHTML(mb_convert_encoding(tidy_repair_string($html, $options, 'utf8'), 'HTML-ENTITIES', 'UTF-8'));
        }catch(\Exception $ex){}
        $xml = simplexml_import_dom($dom);
        $links = $xml->xpath('//a[@class="rg_l"]');
        if(count($links) > 0){
          $img = (string)$links[0]->attributes()['href'];
          $img = explode('?', $img)[1];
          $params = array();
          foreach(explode('&', $img) as $part){
            $params[explode('=', $part)[0]] = explode('=', $part)[1];
          }
          if(isset($params['imgurl'])){
            return array('url' => $params['imgurl']);
          }
        }
      }
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array();
  }

  private function getUrlResponse($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
      'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36'
    ));
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
  }

}