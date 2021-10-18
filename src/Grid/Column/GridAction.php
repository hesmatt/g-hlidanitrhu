<?php

namespace Matt\SyGridBundle\Grid\Column;

class GridAction extends AbstractGridColumn
{
    private \Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter;

    /**
     * @param \Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter
     */
    public function setFormatter(\Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @return \Matt\SyGridBundle\Grid\Formatter\GridFormatter
     */
    public function getFormatter(): \Matt\SyGridBundle\Grid\Formatter\GridFormatter
    {
        return $this->formatter;
    }
}