<?php
declare(strict_types=1);

namespace MarkHelp\Bags;

class Bag
{
    protected $params = [];

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
        foreach($params as $name => $value) {
            $this->setParam($name, $value);
        }
        return $this;
    }

    public function all() : array
    {
        return $this->params;
    }
}