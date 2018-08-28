<?php
namespace ItForFree\SimpleMVC\traits;


use \ItForFree\SimpleMVC\User;
use ItForFree\SimpleMVC\exceptions\SmvcAccessException;
use ItForFree\SimpleMVC\exceptions\SmvcUsageException;


/* 
 * Система контроля доступа
 */
trait AccessControl {
     
   
    /**
     * @var string Пояснение по результату работы последнего вызова $this->isEnable($actionName)
     * ВНИМАНИЕ: получайте значение сразу после обращения к указанному методу (и точно не "до").
     */
   public $explanation = 'Решений ещё не принималось';
   
    /**
     * Массив, содержащий имена методов, доступных пользователю с данной ролью
     * (должен переопределяться в контроллерах)
     * @var array 
     */ 
    protected $rules = [];
    
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Запускает метод класса ***Controller полученный через GET-параметр
     * @param type 
     */
    public function callAction($route) 
    {
        $actionName = $this->getControllerActionName($route);

          
        if ($this->isEnabled($route, $actionName)) {
            $methodName =  $this->getControllerMethodName($actionName);
            
            if (!method_exists($this, $methodName)) {
                throw new SmvcUsageException("Метод контроллера для данного действия не найден.");
            }

            
            $this->$methodName();
        } else {
            throw  new SmvcAccessException("Доступ к маршруту $route запрещен.");
        }
    }
    
    /**
     * Проверит разрешено ли текущему пользователю использовать выполнить действие котроллера
     * ВНИМАНИЕ: данный метод работает именно с правилами контроллера, 
     * которые теоритечски (в случае наличия ошибок) могут не соответствовать 
     * имеющимся в нем методам действий.
     * 
     * @param string $actionName имя дествия (как в маршруте, выделяется из маршрута до передачи в метод)
     * @return boolean разрешено ли текущему пользователю выполнять данное действие, 
     *    
     */
    public function IsEnabled($actionName)
    {
        $currentRole = User::get()->role;
        if (!empty($this->rules)) {
//            $rules = $this->rules;
//            $this->explanation = '';
//            // Сначала проверим, есть ли правило конкретно для данного действия
//            if (!empty($rules[$actionName])) {
//                if (!empty($rules[$actionName]['deny'])) {
//                    foreach ($rules[$actionName]['deny'] as $k => $role) {  // перебираем имена ролей пользователей
//                        if ($currentRole == $role) {
//                            $this->explanation = "Для действия $actionName и роли $role найдено персональное запрещающее правило.";
//                            return false;
//                        }
//                    }
//                }   
//                if (!empty($rules[$actionName]['allow'])) {
//                    foreach ($rules[$actionName]['allow'] as $k => $role) { // перебираем имена ролей пользователей
//                        if ($currentRole == $role) {
//                            $this->explanation = "Для действия $actionName и роли $role найдено персональное разрешающее правило.";
//                            return true;
//                        }
//                    }
//                }
//            }
//            
//            $this->explanation = "Конкретных правил для действия $actionName  не найдено. Глобальные правила: ";
//            // Если правил для конкретного действия не оказалось - -смотрим глобальные правила контролеера для всех ролей
//            if (!empty($rules['all'])) {
//                if (!empty($rules['all']['deny'])) {
//                    foreach ($rules['all']['deny'] as $k => $action) { // перебираем имена действий
//                        if ($actionName == $action) {
//                            $this->explanation .= " имя действия найдено в запрещающей секции"; 
//                            return false;
//                        }
//                    }
//                }   
//                if (!empty($rules['all']['allow'])) {
//                    foreach ($rules['all']['allow'] as $k => $action) {  // перебираем имена действий 
//                        if ($actionName == $action) {
//                            $this->explanation .= " имя действия найдено в разрешающей секции."; 
//                            return true;
//                        }
//                    }
//                }
//            }
            
            return $this->IsEnabledInYii2Style($actionName, $currentRole, $this->rules);
            
        } else  {
            $this->explanation = "В контроллере вообще нет правил. Считаем, что доступ открыт";
            return true;
        }
        
        $this->explanation = "В контроллере есть правила, НО: "
            . "роль $currentRole не упоминается явно в персональных правилах для действия, "
            . "а имя действия $actionName  не подпадает под глобальные правила разрешения или запрета. "
            . "Считаем, что доступ открыт.";
        return true; // В данном контроллере правил нет, или разрешено всем, для текущей роли нет запрещающего указания.
    }
    
    /**
     * 
     * Проверит есть ли доступ для данной роли
     * ВНИМАНИ: применится первое относящееся к данной роли правило (чтобы последующие можно было использовать для других ролей как уточнения)
     * Перечисление действий не обязательн, если правило применяется сразу ко всем действиям "условного контроллера".
     * 
     * @param string $actionName    имя действия
     * @param stringe $role         роль, доступ для котрой нао проверить
     * @param string $rules         массив правил подобный примерам yii2 @see https://www.yiiframework.com/doc/guide/2.0/en/security-authorization
     * @param string $guestRoleName имя проли неавторизованного пользователя, по умолчанию guest
     * @return type
     */
    protected function IsEnabledInYii2Style($actionName, $role, $rules, $guestRoleName = 'guest')
    {
        $allow = true;
        $result = $allow;
        $this->explanation = "Правила есть, но ни в одном из них не была указана роль  $role  данного пользователя"
           . " либо не было указано разрешающее это правило или запрещающее, считаем доступ открытым.";
        foreach ($rules as $rule) {
            if (!empty($rule['roles']) && isset($rule['allow'])) {
                
                $roleInList = $this->isRoleInList($role, $rule['roles'], $guestRoleName);
                $actionsIsEmptyOrCurrentExists = empty($rule['actions']) || in_array($actionName, $rule['actions']);
                if ($roleInList && $actionsIsEmptyOrCurrentExists) {
                    $result = $rule['allow'];
                    $this->explanation = "Применено правило: [" . print_r($rule, true) . "]";
                    break;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Проверит, есть ли роль в списке.
     * Поддерживает псевдонимы:
     * ? -- для пользователя с ролью как $guestRoleName (условный гость - -т.е. неавторизованный пользователь)
     * @ -- для пользователя с ролью НЕ как $guestRoleName (условно -- все остальные пользователи, авторизованные, не гости)
     * 
     * @param string $role
     * @param array $roleList
     * @param string $guestRoleName имя проли неавторизованного пользователя, по умолчанию guest
     * @return type
     */
    protected function isRoleInList(string $role, array $roleList, $guestRoleName = 'guest')
    {
        
        $result =  in_array($role, $roleList) 
            || (($role == $guestRoleName) && (in_array('?', $roleList)))
            || ($role != $guestRoleName && $role != '?' && in_array('@', $roleList));
        
        return $result;
    }
    
    /**
     * Возвращает массив с правилами данного контроллера 
     * @return array['action'] = 'user'
     */
    public function getControllerRules()
    {
        return $this->rules;
    }
}
    
