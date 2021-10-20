<?php

namespace Matt\SyGridBundle\Grid\Language;

class GridLanguage
{
    private array $translations;

    /**
     * @param array $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}