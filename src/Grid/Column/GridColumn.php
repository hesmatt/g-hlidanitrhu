<?php

namespace Matt\SyGridBundle\Grid\Column;

class GridColumn extends AbstractGridColumn
{
    private bool $reflected = true;
    private bool $reflectedKey = false;
    private bool $dataGetter = false;
    private bool $searchable = true;
    private bool $foreign = false;

    /**
     * @param bool $reflected
     * @return GridColumn
     */
    public function setReflected(bool $reflected): GridColumn
    {
        $this->reflected = $reflected;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReflected(): bool
    {
        return $this->reflected;
    }

    /**
     * @return bool
     */
    public function hasDataGetter(): bool
    {
        return $this->dataGetter;
    }

    /**
     * @param bool $dataGetter
     */
    public function setDataGetter(bool $dataGetter): void
    {
        $this->dataGetter = $dataGetter;
    }

    /**
     * @param bool $reflectedKey
     * @return GridColumn
     */
    public function setReflectedKey(bool $reflectedKey): GridColumn
    {
        $this->reflectedKey = $reflectedKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReflectedKey(): bool
    {
        return $this->reflectedKey;
    }

    /**
     * @param bool $searchable
     */
    public function setSearchable(bool $searchable): void
    {
        $this->searchable = $searchable;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $foreign
     */
    public function setForeign(bool $foreign): void
    {
        $this->foreign = $foreign;
    }

    /**
     * @return bool
     */
    public function isForeign(): bool
    {
        return $this->foreign;
    }
}