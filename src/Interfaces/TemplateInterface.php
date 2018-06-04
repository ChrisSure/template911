<?php

namespace Snayper911_Template\Interfaces;


interface TemplateInterface
{

	  /**
    * Конструктор встановлює маршрут збереження шаблонів та ініціалізує стек збереження блоків
    *
    * @param String $path
    *
    * @return void
    */
    public function __construct(String $path);
    

    /**
    * Метод генерує шаблон і ініціалізує перемінні
    *
    * @param String $name
    * @param array $params
    *
    * @return string
    */
    public function render($name, array $params = []): string;


    /**
    * Метод встановлює батьківський блок
    *
    * @param String $view
    *
    * @return string
    */
    public function extend(String $view): void;


    /**
    * Метод встановлює блок
    *
    * @param String $name
    * @param String $content
    *
    * @return void
    */
    public function block(String $name, String $content): void;


     /**
    * Метод перевіряє чи існує блок, якщо так - дає старт блоку і повертає істину
    *
    * @param String $name
    *
    * @return bool
    */
    public function ensureBlock(String $name): bool;


    /**
    * Метод відкриває початок блоку
    *
    * @param String $name
    *
    * @return void
    */
    public function startBlock(String $name): void;


    /**
    * Метод закриває блок
    *
    * @return void
    */
    public function endBlock(): void;


    /**
    * Метод рендерить блок
    *
    * @param String $name
    *
    * @return string
    */
    public function renderBlock(String $name): string;

	
}


