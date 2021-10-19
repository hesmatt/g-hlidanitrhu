<?php

namespace Matt\SyGridBundle\Grid\Formatter;

class GridFormatter
{
    private string $callback;

    /**
     * @param string $callback
     * @return GridFormatter
     */
    public function setCallback(string $callback): GridFormatter
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return string
     */
    public function getCallback(): string
    {
        return $this->callback;
    }
}