<?php namespace Laradic\Themes\Contracts;

interface ThemeViewFinder
{

    public function getHints();

    /**
     * Find the key
     * @param $key
     * @return mixed
     */
    public function find($key);

    /** @return ThemeFactory */
    public function getThemes();

    /**
     * @param ThemeFactory $themes
     * @return $this
     */
    public function setThemes(ThemeFactory $themes);
}
