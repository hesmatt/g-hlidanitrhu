<?php

namespace Matt\SyGridBundle\Grid\Column;

abstract class AbstractGridColumn
{
    protected ?\Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter = null;
    protected ?string $title = null;
    protected string $key;

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
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
     */
    public function setFormatter(?\Matt\SyGridBundle\Grid\Formatter\GridFormatter $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @return \Matt\SyGridBundle\Grid\Formatter\GridFormatter|null
     */
    public function getFormatter(): ?\Matt\SyGridBundle\Grid\Formatter\GridFormatter
    {
        return $this->formatter;
    }
}