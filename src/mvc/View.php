<?php

namespace ItForFree\SimpleMVC\mvc;
use ItForFree\SimpleMVC\Config;

/**
 * Элементарный класс для работы с представлениями
 * -- позволят отделить HTML от PHP 
 */
class View 
{
    /**
     * @var string Стандартный путь к корневому каталогу шаблонов (view) 
     */
    public $templateBasePath = '/';
    
    /**
     * @var array Массив, содержащий все переменные программы для их 
     * транспортировки из области видимости контроллеров в представления
     */
    protected $vars = [];
   
    /**
     * @var string Путь к файлу шаблона внутрь которого и подставляется конкретное представление?
     *  (относительно $this->layoutsBasePath).
     * 
     */
    public $layoutPath = '/';
    
    /**
     * @var string  полный путь к базовой директории шаблонов
     */
    public $layoutsBasePath = '/';
    
    /**
     * Создаёт класс представления
     * 
     * @param string $layoutPath путь к шаблону относительно $this->layoutsBasePath
     */
    public function __construct(string $layoutPath = 'default.php') {
        $this->templateBasePath = 
		rpath(Config::get('core.mvc.views.base-template-path'));
        $this->layoutsBasePath = rpath(Config::get('core.mvc.views.base-layouts-path'));
        $this->layoutPath = $this->layoutsBasePath . $layoutPath;
    }

    /**
     * Делает значение переменной 
     * доступной в представлении (view) по данному имени
     * 
     * @param string $name  имя будущей переменной в представлени
     * @param mixed $value  значение
     */
    public function addVar(string $name, mixed $value): void
    {
        $this->vars[$name] = $value;
    }

    /**
     * Формирует окончательное представление страницы. 
     * Запишет содержимое файла представления (вместе с подставленными переменными) 
     * в переменную $CONTENT_DATA (имя специально не в нотации) и подставит её в шаблон.
     * 
     * @param string $viewFilePath         Путь к файду представления относительно базовой папки представлений
     * @param string $layoutPath  Относитлеьный путь к файлу шаблона -- передавайте только если требуется переопределить шаблон, который передаётся в конструктор представления
     */
    public function render(string $viewFilePath, string $layoutPath = ''): void
    {
        if($layoutPath) {
           $layoutPath = $this->layoutsBasePath . $layoutPath; 
        } else {
            $layoutPath = $this->layoutPath;
        }
        
        // Далее начинаем формировать представление
        extract($this->vars); // распаковываем переменные, переданные в представление (VIEW)
        
        ob_start(); // перехватываем поток вывода
        include($this->templateBasePath . $viewFilePath); 
        $CONTENT_DATA = ob_get_contents(); // записываем перехваченное в переменную
        ob_end_clean(); // отключаем перехват
        
        include($layoutPath); // подключаем шаблон, куда и будет подставлено 
    }
    
    /**
     * Формирует частичную печать страницы. 
     * Собирает базовый HTML
     * и индивидуальный для каждой страницы
     * 
     * @param string Путь к целевой странице
     */
    public function renderPartition(string $path): void
    {
        extract($this->vars);  
        include($this->templateBasePath . $path);
    }   
}