<?php

namespace ItForFree\SimpleMVC\mvc;

use ItForFree\SimpleMVC\Config as Config;
use ItForFree\SimpleMVC\Application;
use ItForFree\rusphp\Log\SimpleEchoLog;
//use ItForFree\SimpleMVC\mvc\Model;

/**
 * Базовый класс для модолей: используя конфиг приложения,
 * как минимум подключается к базе данных и даёт потомкам работать с соединением.
 */
class Model 
{
   
    /**
     * @var string Имя обрабатываемой таблицы 
     */
    public $tableName = '';
    
    /**
     *  @var string Имя поля по котору сортируем
     */
    public $orderBy = '';
    
    /**
    * @var int ID сущности в базе данных
    */
    public $id = null;

    /**
     * 
     * @param array $data необязательный массив для инициаллизации свойств объекта модели
     */
    public function __construct(?array $data = null)
    {
        // $this->setPdoSettings();
        if (is_array($data)) {
            $this->setObjectVars($this, $data);
        }
    }
    
    /**
     * Магический метод для перехвата обращения к свойствам
     * 
     * @staticvar type $pdo
     */
    public function  __get (string $name): \PDO
    {
	static $pdo = null;
	if ($name === 'pdo') {
	    if ($pdo) {
		return $pdo;
	    } else {
		$pdo = $this->setPdoSettings();
	    }
	    
	    return $pdo;
	}
    }
    
    /**
     * Присваивает свойствам объекта, соответствующие по имена ключей значений из массива
     * 
     * @param object $object объект, свойства которого требуется заполнить значениями из массива $vars
     * @param array $vars  ассоциативный массив значений
     */
    private function setObjectVars(object $object, array $vars): void 
    {
        $has = get_object_vars($object);
        foreach ($has as $name => $oldValue) {
            $object->$name = isset($vars[$name]) ? $vars[$name] : $object->$name; 
        }
    } 
    
    /**
     *  Устанавливает настройки доступа к БД и сохраяет объект PDO в одноименное свойство модели
     *  ($this->pdo)
     */
    protected function setPdoSettings(): \PDO
    {
        $dbSettings = Application::getConfigElement('core.db');
        $pdo = new \PDO($dbSettings['dns'], 
                $dbSettings['username'],
                $dbSettings['password'],
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	return $pdo;
    }
   
    /**
     * Получает из БД все поля одной строки таблицы, с соответствующим Id
     * Возвращает объект класса модели.
     * 
     * @param int    $id         id строки (кортежа)
     * @param string $tableName  имя таблицы (необязатлеьный параметр)
     */
    public function getById(int $id, string $tableName = ''): ?Model
    {  
        $tableName = !empty($tableName) ? $tableName : $this->tableName;
        
        $sql = "SELECT * FROM $tableName where id = :id";      
        $modelClassName = static::class;
        
        $st = $this->pdo->prepare($sql); 
        
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        
        if ($row) { 
            return new $modelClassName( $row );
        } else {
            return null;
        }
    }
   
    /**
     * Извлечет данные и вернет массив моделей из базы данных.
     * 
     * @param int $numRows ограничение на число строк
     */
    public function getList(int $numRows=1000000): array  
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName
                ORDER BY  $this->orderBy LIMIT :numRows";
        
        $modelClassName = static::class;
       
        $st = $this->pdo->prepare($sql);
        $st->bindValue( ":numRows", $numRows, \PDO::PARAM_INT );
        $st->execute();
        $list = array();
        
        while ($row = $st->fetch()) {
            $example = new $modelClassName($row);
            $list[] = $example;
        }

        $sql = "SELECT FOUND_ROWS() AS totalRows"; //  получаем число выбранных строк
        $totalRows = $this->pdo->query($sql)->fetch();
	
        return (array("results" => $list, "totalRows" => $totalRows[0]));
    }
    
    /**
     * Метод для пейджинации данных из БД
     * 
     * @param int $pageNumber  номер страницы
     * @param int $limit       число элементов на странице
     * @return array           массив  кортежей из БД
     */
    public function getPage(int $pageNumber = 1, int $limit = 2): array
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName
                ORDER BY  $this->orderBy LIMIT :limit OFFSET :offset";

        $modelClassName = static::class;
        $offset = ($pageNumber - 1)*$limit;

        $st = $this->pdo->prepare($sql);
        $st->bindValue( ":limit", intval($limit), \PDO::PARAM_INT );
        $st->bindValue( ":offset", intval($offset), \PDO::PARAM_INT );

        $st->execute();
        
        while ( $row = $st->fetch() ) {
            $example = new $modelClassName( $row );
            $list[] = $example;
        }
       // Получаем общее количество статей, которые соответствуют критерию
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $this->pdo->query( $sql )->fetch();
        return array("results" => $list, "totalRows" => $totalRows[0]);
    }
  
    /**
     * Инициллизирует поля модели из массива 
     * и вернет вновь созданный экземпляр класса.
     * 
     * @todo проверить нужность.
     * 
     * @param array $arr массив значений
     */
    public function loadFromArray(array $arr): Model
    {
        $modelClassName = static::class;
        return new $modelClassName($arr);
    }
    
    /**
     * Удаляем запись для данной модели из базы данных.
     */
    public function delete(): void
    {
        $st = $this->pdo->prepare("DELETE FROM $this->tableName WHERE id = :id LIMIT 1" );
        $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
        $st->execute();
    }   
}
