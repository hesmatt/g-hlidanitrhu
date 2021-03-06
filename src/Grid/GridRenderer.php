<?php

namespace Matt\SyGridBundle\Grid;

class GridRenderer
{
    private \Twig\Environment $environment;
    private string $id = 'syGrid';
    private string $variableName = 'grid';
    private ?string $serverUrl = null;
    private ?string $customStyle = null;
    private bool $paging = true;
    private bool $search = true;
    private int $limit = 20;
    private ?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager = null;
    private \Matt\SyGridBundle\Grid\Cache\GridCaching $cacheManager;
    private ?Language\GridLanguage $language = null;

    public function __construct()
    {
        $this->cacheManager = new \Matt\SyGridBundle\Grid\Cache\GridCaching();
    }

    /**
     * @param \Twig\Environment $environment
     */
    public function setEnvironment(\Twig\Environment $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string|null $serverUrl
     */
    public function setServerUrl(?string $serverUrl): void
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     * @param bool $paging
     */
    public function setPaging(bool $paging): void
    {
        $this->paging = $paging;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param string $customStyle
     */
    public function setCustomStyle(string $customStyle): void
    {
        $this->customStyle = $customStyle;
    }

    /**
     * @param bool $search
     */
    public function setSearch(bool $search): void
    {
        $this->search = $search;
    }

    /**
     * @param string $variableName
     * Sets variable name of the GRID that can later be referenced in and via JS
     */
    public function setVariableName(string $variableName): void
    {
        $this->variableName = $variableName;
    }

    /**
     * @param string|null $language
     * @throws \Exception
     * Sets the language from given locale, if not set the language defaults to us_US (English)
     */
    public function setLanguage(string $language): void
    {
        if (\file_exists(__DIR__ . "/../Resources/translations/{$language}.yaml")) {
            $gridLanguage = new Language\GridLanguage();
            $gridLanguage->setTranslations(\Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . "/../Resources/translations/{$language}.yaml"));
            $this->language = $gridLanguage;
        } else {
            throw new \Exception("Language {$language} does not exist!");
        }
    }

    /**
     * @param string|null $title
     * @param string $key
     * @param callable|null $dataGetter
     * @return Column\GridColumn
     * Adds a single column into sourceManagers column list
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addColumn(?string $title, string $key, ?callable $dataGetter = null): \Matt\SyGridBundle\Grid\Column\GridColumn
    {
        $column = new \Matt\SyGridBundle\Grid\Column\GridColumn();
        $column->setTitle($title);
        $column->setKey($key);

        if ($dataGetter !== null) {
            $column->setDataGetter(true);
            $this->cacheManager->cacheColumnDataGetter(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass) . "." . $key, $dataGetter);
        }

        $this->sourceManager->columns[] = $column;
        return $column;
    }

    /**
     * @param string $title
     * @param string $key
     * @param Formatter\GridFormatter $formatter
     * @return Column\GridAction
     * Adds a single action into sourceManagers action list
     */
    public function addAction(string $title, string $key, Formatter\GridFormatter $formatter): \Matt\SyGridBundle\Grid\Column\GridAction
    {
        $action = new \Matt\SyGridBundle\Grid\Column\GridAction();
        $action->setTitle($title);
        $action->setKey($key);
        $action->setFormatter($formatter);

        $this->sourceManager->actions[] = $action;
        return $action;
    }

    /**
     * @param ?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager
     */
    public function setSourceManager(?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    /**
     * @return string|null
     */
    public function getServerUrl(): ?string
    {
        //TODO: Remove the hardcoded sourceType and add a custom sourceType depending on the users choice, however, we do not have more choices yet.
        $escapedSourceClass = \Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass);
        return $this->serverUrl ?? "/sygrid/data/get?source={$escapedSourceClass}&sourceType=Entity";
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * Renders and returns the main of the grid, including styles, data and loaders.
     */
    public function render(): string
    {
        $this->cacheNeededParameters();
        return $this->environment->render('@SyGrid/Grid/grid.html.twig',
            [
                'serverUrl' => $this->serverUrl ?? $this->getServerUrl(),
                'gridId' => $this->id,
                'paging' => $this->paging,
                'limit' => $this->limit,
                'columns' => $this->sourceManager->columns,
                'actions' => $this->sourceManager->actions,
                'customStyle' => $this->customStyle,
                'search' => $this->search,
                'language' => $this->language,
                'variableName' => $this->variableName
            ]);
    }

    private function cacheNeededParameters()
    {
        $this->cacheManager->cacheColumns(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass), $this->sourceManager->columns);
        $this->cacheManager->cacheSourceParameters(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass),$this->sourceManager->getCacheableParams());
    }
}