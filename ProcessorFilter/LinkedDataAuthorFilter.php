<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;
use GuzzleHttp\Client;

class LinkedDataAuthorFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "Linked Data Author (DBpedia)";
  }

  function getFields()
  {
    return array('author_biography', 'author_picture', 'author_year_of_birth');
  }

  function getSettingFields()
  {
    return array();
  }

  function getArguments()
  {
    return array(
      'author_name' => 'Author name',
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    try {
      $author = $this->getArgumentValue('author_name', $document);
      if(!empty($author)) {
        $url = 'http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=PREFIX+dbo%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2F%3E%0D%0A%0D%0ASELECT+DISTINCT+*+WHERE+%7B%0D%0A%3Fauthor+rdf%3Atype+foaf%3APerson.%0D%0A%3Fauthor+rdfs%3Alabel+%3Fnom.%0D%0A%3Fauthor+rdfs%3Acomment+%3Fbio.%0D%0A%3Fauthor+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2Fthumbnail%3E+%3Fimage.%0D%0A%3Fauthor+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2FbirthDate%3E+%3Fbirth_date.%0D%0A%3Fauthor+rdfs%3Alabel+%22' . urlencode($author) . '%22%40fr.%0D%0AFILTER%28langMatches%28lang%28%3Fnom%29%2C%22FR%22%29%29.%0D%0AFILTER%28langMatches%28lang%28%3Fbio%29%2C%22FR%22%29%29.%0D%0A%7D&format=application/json&CXML_redir_for_subjs=121&CXML_redir_for_hrefs=&timeout=1000&debug=on';

        $client = new Client();
        $response = $client->request('GET', $url);
        $r = json_decode($response->getBody(), TRUE);
        if (isset($r['results']['bindings'][0])) {
          $info = array(
            'author_biography' => isset($r['results']['bindings'][0]['bio']['value']) ? $r['results']['bindings'][0]['bio']['value'] : '',
            'author_picture' => isset($r['results']['bindings'][0]['image']['value']) ? $r['results']['bindings'][0]['image']['value'] : '',
            'author_year_of_birth' => isset($r['results']['bindings'][0]['birth_date']['value']) ? date('Y', strtotime($r['results']['bindings'][0]['birth_date']['value'])) : '',
          );
          return $info;
        }
      }
    } catch (\Exception $ex) {
      $datasource->getOutputManager()->writeLn('Exception ==> ' . $ex->getMessage());
    }
    return array();
  }

}