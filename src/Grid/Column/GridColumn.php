<?php

namespace Matt\SyGridBundle\Grid\Column;

class GridColumn extends AbstractGridColumn
{
    private bool $reflected = true;
    private bool $reflectedKey = false;
    private bool $dataGetter = false;

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
    public function isDataGetter(): bool
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
}