<?php

namespace Matt\SyGridBundle\Grid\Formatter;

class GridFormatter
{
    private string $return;

    /**
     * @param string $return
     */
    public function setReturn(string $return): void
    {
        $this->return = $return;
    }

    /**
     * @return string
     */
    public function getReturn(): string
    {
        return $this->return;
    }
}