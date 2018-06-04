<?php

namespace Snayper911_Template;

use Snayper911_Template\Interfaces\TemplateInterface;
use Snayper911_Template\Exception\NotTemplateException;



class PhpRenderer implements TemplateInterface
{
    //Маршрут збереження шаблонів
    private $path;

    //Батьківський блок
    private $extend;

    //Массив блоків
    private $blocks = [];

    //Стек блоків
    private $blockNames;


    /**
    * Конструктор встановлює маршрут збереження шаблонів та ініціалізує стек збереження блоків
    *
    * @param String $path
    *
    * @return void
    */
    public function __construct(String $path)
    {
        $this->path = $path;
        $this->blockNames = new \SplStack();
    }

    /**
    * Метод генерує шаблон і ініціалізує перемінні
    *
    * @param String $name
    * @param array $params
    *
    * @return string
    */
    public function render($name, array $params = []): string
    {
        $level = ob_get_level();
        $templateFile = $this->path . '/' . $name . '.php';
        $this->extend = null;
        try {
            ob_start();
            extract($params, EXTR_OVERWRITE);
            if (!include_once $templateFile) {
                throw new NotTemplateException('Немає даного шаблона - ' . $name);
            }
            $content = ob_get_clean();
        } catch (\Throwable|\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        if (!$this->extend) {
            return $content;
        }
        return $this->render($this->extend);
    }

    /**
    * Метод встановлює батьківський блок
    *
    * @param String $view
    *
    * @return string
    */
    public function extend(String $view): void
    {
        $this->extend = $view;
    }

    /**
    * Метод встановлює блок
    *
    * @param String $name
    * @param String $content
    *
    * @return void
    */
    public function block(String $name, String $content): void
    {
        if ($this->hasBlock($name)) {
            return;
        }
        $this->blocks[$name] = $content;
    }

    /**
    * Метод перевіряє чи існує блок, якщо так - дає старт блоку і повертає істину
    *
    * @param String $name
    *
    * @return bool
    */
    public function ensureBlock(String $name): bool
    {
        if ($this->hasBlock($name)) {
            return false;
        }
        $this->beginBlock($name);
        return true;
    }

    /**
    * Метод відкриває початок блоку
    *
    * @param String $name
    *
    * @return void
    */
    public function startBlock(String $name): void
    {
        $this->blockNames->push($name);
        ob_start();
    }

    /**
    * Метод закриває блок
    *
    * @return void
    */
    public function endBlock(): void
    {
        $content =  ob_get_clean();
        $name = $this->blockNames->pop();
        if ($this->hasBlock($name)) {
            return;
        }
        $this->blocks[$name] = $content;
    }

    /**
    * Метод рендерить блок
    *
    * @param String $name
    *
    * @return string
    */
    public function renderBlock(String $name): string
    {
        $block = $this->blocks[$name] ?? null;
        if ($block instanceof \Closure) {
            return $block();
        }
        return $block ?? '';
    }

    /**
    * Метод перевіряє чи існує блок
    *
    * @param String $name
    *
    * @return bool
    */
    private function hasBlock(String $name): bool
    {
        return array_key_exists($name, $this->blocks);
    }



    /**
    * Метод фільтрації перемінної через htmlspecialchars
    *
    * @param String $string
    *
    * @return bool
    */
    public function encode(String $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE);
    }


    /*public function path($name, array $params = []): string
    {
        return $this->router->generate($name, $params);
    }*/

}