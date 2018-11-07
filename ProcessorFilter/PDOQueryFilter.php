<?php

namespace AdimeoDataSuite\ProcessorFilter;

use AdimeoDataSuite\Model\Datasource;
use AdimeoDataSuite\Model\ProcessorFilter;

class PDOQueryFilter extends ProcessorFilter
{
  function getDisplayName()
  {
    return "PDO query";
  }

  function getFields()
  {
    return array('rows');
  }

  function getSettingFields()
  {
    return array(
      'driver' => array(
        'label' => 'PDO driver (E.g.: mysql, postgresl)',
        'type' => 'string',
        'required' => true
      ),
      'host' => array(
        'label' => 'Host',
        'type' => 'string',
        'required' => true
      ),
      'port' => array(
        'label' => 'Port',
        'type' => 'string',
        'required' => true
      ),
      'dbName' => array(
        'label' => 'Database name',
        'type' => 'string',
        'required' => true
      ),
      'username' => array(
        'label' => 'Username',
        'type' => 'string',
        'required' => true
      ),
      'password' => array(
        'label' => 'Password',
        'type' => 'string',
        'required' => true
      ),
      'retry_on_pdo_exception' => array(
        'label' => 'Retry on PDO Exception',
        'type' => 'boolean',
        'required' => false
      ),
      'sql' => array(
        'label' => 'SQL query (use @varX for variable #X)',
        'type' => 'textarea',
        'required' => true
      )
    );
  }

  function getArguments()
  {
    return array(
      'var1' => 'Variable #1',
      'var2' => 'Variable #2',
      'var3' => 'Variable #3',
      'var4' => 'Variable #4',
      'var5' => 'Variable #5'
    );
  }

  function execute(&$document, Datasource $datasource)
  {
    $settings = $this->getSettings();
    $tries = 0;
    $rows = array();
    $retry = isset($settings['retry_on_pdo_exception']) && $settings['retry_on_pdo_exception'];
    while($tries == 0 || $retry) {
      try {
        $dsn = $settings['driver'] . ':host=' . $settings['host'] . ';port=' . $settings['port'] . ';dbname=' . $settings['dbName'] . ';charset=UTF8;';
        $pdo = $datasource->getPDOPool()->getHandler($dsn, $settings['username'], $settings['password']);
        $sql = $settings['sql'];
        foreach ($this->getArguments() as $k => $v) {
          $sql = str_replace('@' . $k, $this->getArgumentValue($k, $document), $sql);
        }
        $rs = $pdo->query($sql);
        while ($row = $rs->fetch(\PDO::FETCH_ASSOC)) {
          $rows[] = $row;
        }
        $retry = false;
      }
      catch(\PDOException $ex){
        $datasource->getOutputManager()->writeLn(get_class($this) . ' >> PDO Exception has been caught (' . $ex->getMessage() . ')');
        if($tries > 20){
          $retry=  false;
          $datasource->getOutputManager()->writeLn(get_class($this) . ' >> This is over, I choose to die.');
          throw $ex;
        }
        else{
          $datasource->getOutputManager()->writeLn(get_class($this) . ' >> Retrying in 1 second...');
          sleep(1); //Sleep for 1 second
        }
      }
      finally{
        $tries++;
      }
    }
    return array('rows' => $rows);
  }

}