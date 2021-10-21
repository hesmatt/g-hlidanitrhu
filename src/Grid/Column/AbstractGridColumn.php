<?php

namespace Matt\SyGridBundle\Grid\Column;

abstract class AbstractGridColumn
{
    protected ?\Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter = null;
    protected ?string $title = null;
    protected string $key;

    /**
     * @param string $title
     * @return AbstractGridColumn
     */
    public function setTitle(string $title): AbstractGridColumn
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $key
     * @return AbstractGridColumn
     */
    public function setKey(string $key): AbstractGridColumn
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param \Matt\SyGridBundle\Grid\Formatter\GridFormatter|null $formatter
     * @return AbstractGridColumn
     */
    public function setFormatter(?\Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter): AbstractGridColumn
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @return \Matt\SyGridBundle\Grid\Formatter\GridFormatter|null
     */
    public function getFormatter(): ?\Matt\SyGridBundle\Grid\Formatter\GridFormatter
    {
        return $this->formatter;
    }
}