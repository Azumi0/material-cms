<?php

namespace materialhelpers;

use Yii;
use yii\base\View;
use yii\twig\ViewRenderer as TwigRenderer;

class ViewRenderer extends TwigRenderer
{
    /**
     * @var array paths of files
     *
     * Example:
     *
     * ```php
     * [
     *     '@app\views',
     *     '@app\views\layouts',
     * ]
     * ```
     */
    public $paths = [];

    /**
     * @inheritdoc
     */
    public function render($view, $file, $params)
    {
        $this->twig->addGlobal('this', $view);
        $loader = new \Twig_Loader_Filesystem(array_merge([ dirname($file) ], $this->paths));
        if ($view instanceof View) {
            $this->addFallbackPaths($loader, $view->theme);
        }

        $this->addAliases($loader, Yii::$aliases);
        $this->twig->setLoader($loader);

        // Change lexer syntax (must be set after other settings)
        if (!empty($this->lexerOptions)) {
            $this->setLexerOptions($this->lexerOptions);
        }

        return $this->twig->render(pathinfo($file, PATHINFO_BASENAME), $params);
    }

}