<?php
declare(strict_types=1);

namespace MarkHelp\Bags;

class Bag
{
    private $params = [];

    public function setParam(string $name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function param(string $name)
    {
        return $this->params[$name] ?? null;
    }

    public function addParams(array $params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function all() : array
    {
        return $this->params;
    }
}