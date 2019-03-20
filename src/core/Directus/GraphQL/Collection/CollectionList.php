<?php

namespace Directus\GraphQL\Collection;

use Directus\GraphQL\Types;
use Directus\Application\Application;

class CollectionList {

  protected $param;
  protected $limit;
  protected $offset;
  protected $container;

  public function __construct(){
    $this->param = ['fields' => '*.*.*.*.*.*'];
    $this->limit = ['limit' => Types::int()];
    $this->offset = ['offset' => Types::int()];
    $this->container = Application::getInstance()->getContainer();
  }
}