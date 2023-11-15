<?php

namespace ItForFree\SimpleMVC\Traits;

use ItForFree\SimpleMVC\Exceptions\SmvcAccessException;
use ItForFree\SimpleMVC\Exceptions\SmvcUsageException;
use ItForFree\SimpleMVC\Config;

/* 
 * Система контроля доступа
 */
trait AccessControl {
     
   
    /**
     * @var string Пояснение по результату работы последнего вызова $this->isEnable($actionName)
     * ВНИМАНИЕ: получайте значение сразу после обращения к указанному методу (и точно не "до").
     */
    public string $explanation = 'Решений ещё не принималось';
   
    /**
     * Массив, содержащий имена методов, доступных пользователю с данной ролью
     * (должен переопределяться в контроллерах)
     */ 
    protected array $rules = [];
    
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Запускает метод класса ***Controller полученный через GET-параметр
     */
    public function callAction(string $route): void  
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
    public function IsEnabled(string $actionName): bool
    {
        $currentRole = Config::getObject('core.user.class')->role;
        if (!empty($this->rules)) {
            
            return $this->IsEnabledInYii2Style($actionName, $currentRole, $this->rules);
            
        } else  {
            $this->explanation = "В контроллере вообще нет правил. Считаем, что доступ открыт";
            return true;
        }
    }
    
    /**
     * 
     * Проверит есть ли доступ для данной роли.
     * ВНИМАНИ: применится первое относящееся к данной роли правило (чтобы последующие можно было использовать для других ролей как уточнения)
     * Перечисление действий не обязательн, если правило применяется сразу ко всем действиям "условного контроллера".
     * 
     * Если правила есть ($rules не пустой массив), но доступ для данной роли явно не прописан, то считается что он закрыт.
     * 
     * @param string $actionName    имя действия
     * @param string $role         роль, доступ для котрой нао проверить
     * @param array $rules         массив правил подобный примерам yii2 @see https://www.yiiframework.com/doc/guide/2.0/en/security-authorization
     * @param string $guestRoleName имя проли неавторизованного пользователя, по умолчанию guest
     */
    protected function IsEnabledInYii2Style(string $actionName, string $role, 
	    array $rules, string $guestRoleName = 'guest'): bool
    {
        $allow = false;
        $result = $allow;
        $this->explanation = "Правила есть, но ни в одном из них не была указана роль  $role  данного пользователя"
           . " либо не было указано разрешающее это правило или запрещающее, считаем доступ закрытым.";
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
     * @param string $guestRoleName имя проли неавторизованного пользователя, по умолчанию guest
     */
    protected function isRoleInList(string $role, array $roleList, string $guestRoleName = 'guest'): bool
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
    public function getControllerRules(): array
    {
        return $this->rules;
    }
}
    
