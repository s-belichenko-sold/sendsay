<?php

namespace StudioSold\Sendsay;

/**
 * Библиотека Sendsay API.
 *
 * @version 1.6.2
 * @author  Alex Milekhin (me@alexmil.ru)
 * @link    [https://sendsay.ru/api/api.html][Документация]
 */
abstract class SendsayAPI
{
    /**
     * @var array $auth   - массив с авторизационными данными
     * @var       $params - параметры запроса
     * @var bool  $debug  - вывод отладочной информации
     */
    protected $auth  = array();
    protected $params;
    public    $debug = false;


    /**
     * Конструктор класса.
     *
     * @param string $login    - общий логин
     * @param string $sublogin - личный логин
     * @param string $password - пароль
     * @param bool   $debug    - вывод отладочной информации
     */
    public function __construct($login, $sublogin, $password, $debug = false)
    {
        $this->debug = $debug;
        $this->auth['one_time_auth'] = array(
            'login'    => $login,
            'sublogin' => $sublogin,
            'passwd'   => $password
        );
    }

    /**
     * Проверяет доступность сервера Sendsay.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D0%B1%D0%B5%D0%B7-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
     *
     * @return bool
     * @throws \Exception
     */
    public function ping()
    {
        $this->params['action'] = 'ping';

        $result = $this->send();

        return isset($result['pong']);
    }

    /**
     * Пинг с авторизацией.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D1%81-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B5%D0%B9][Документация]
     *
     * @return bool
     * @throws \Exception
     */
    public function pong()
    {
        $this->params = $this->auth + array(
                'action' => 'pong'
            );

        $result = $this->send();

        return isset($result['ping']);
    }

    /**
     * Возвращает список асинхронных запросов.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B0%D1%81%D0%B8%D0%BD%D1%85%D1%80%D0%BE%D0%BD%D0%BD%D1%8B%D1%85-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%BE%D0%B2][Документация]
     *
     * @param array $filter - фильтр; массив должен содержать хотя бы один параметр
     *
     * @return array
     * @throws \Exception
     */
    public function track_list($filter)
    {
        $this->params = $this->auth + array(
                'action' => 'track.list',
                'filter' => $filter
            );

        return $this->send();
    }

    /**
     * Возвращает описание асинхронного запроса.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B0%D1%81%D0%B8%D0%BD%D1%85%D1%80%D0%BE%D0%BD%D0%BD%D0%BE%D0%B3%D0%BE-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%B0][Документация]
     *
     * @param int $id - код запроса
     *
     * @return array
     * @throws \Exception
     */
    public function track_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'track.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Возвращает список форматов и шаблонов.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%BE%D0%B2%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function format_list()
    {
        $this->params = $this->auth + array(
                'action' => 'format.list'
            );

        return $this->send();
    }

    /**
     * Создаёт или изменяет формат или шаблон.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B8%D0%BB%D0%B8-%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
     *
     * @param array  $obj - данные формата (см. докумендацию)
     * @param string $id  - код формата
     *
     * @return array
     * @throws \Exception
     */
    public function format_set($obj, $id = null)
    {
        $this->params = $this->auth + array(
                'action' => 'format.set',
                'obj'    => $obj
            );

        $this->param('id', $id);

        return $this->send();
    }

    /**
     * Считывает формат или шаблон.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
     *
     * @param string $id - код формата
     *
     * @return array
     * @throws \Exception
     */
    public function format_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'format.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Удаляет формат или шаблон.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
     *
     * @param string $id - код формата
     *
     * @return array
     * @throws \Exception
     */
    public function format_delete($id)
    {
        $this->params = $this->auth + array(
                'action' => 'format.delete',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Возвращает список анкет.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_list()
    {
        $this->params = $this->auth + array(
                'action' => 'anketa.list'
            );

        return $this->send();
    }

    /**
     * Возвращает данные анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $id - код анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'anketa.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Удаляет анкету.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $id - код анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_delete($id)
    {
        $this->params = $this->auth + array(
                'action' => 'anketa.delete',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Создаёт анкету.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $name - название анкеты
     * @param string $id   - код анкеты
     * @param string $copy - код копируемой анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_create($name, $id = null, $copy = null)
    {
        $this->params = $this->auth + array(
                'action' => 'anketa.create',
                'name'   => $name
            );

        $this->param('id', $id);
        $this->param('copy_from', $copy);

        return $this->send();
    }

    /**
     * Изменяет название анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D1%85%D1%80%D0%B0%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $id   - код анкеты
     * @param string $name - название анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_set($id, $name)
    {
        $this->params = $this->auth + array(
                'action' => 'anketa.set',
                'id'     => $id,
                'name'   => $name
            );

        return $this->send();
    }

    /**
     * Добавляет вопрос в анкету.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%94%D0%BE%D0%B1%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BD%D0%BE%D0%B2%D0%BE%D0%B3%D0%BE-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $anketa    - код анкеты
     * @param array  $questions - один или несколько вопросов анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_add($anketa, $questions)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.add',
                'anketa.id' => $anketa,
                'obj'       => $questions
            );

        return $this->send();
    }

    /**
     * Изменяет вопросы анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $anketa    - код анкеты
     * @param array  $questions - один или несколько вопросов анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_set($anketa, $questions)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.set',
                'anketa.id' => $anketa,
                'obj'       => $questions
            );

        return $this->send();
    }

    /**
     * Удаляет вопрос из анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $anketa    - код анкеты
     * @param mixed  $questions - один (string) или несколько (array) вопросов анкеты
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_delete($anketa, $questions)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.delete',
                'anketa.id' => $anketa,
                'id'        => $questions
            );

        return $this->send();
    }

    /**
     * Изменяет порядок вопросов анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%B7%D0%B8%D1%86%D0%B8%D0%B8-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $anketa - код анкеты
     * @param mixed  $order  - коды вопросов анкеты в нужном порядке
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_order($anketa, $order)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.order',
                'anketa.id' => $anketa,
                'order'     => $order
            );

        return $this->send();
    }

    /**
     * Изменяет порядок ответов.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%B7%D0%B8%D1%86%D0%B8%D0%B8-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0][Документация]
     *
     * @param string $anketa   - код анкеты
     * @param string $question - код вопроса
     * @param array  $order    - коды ответов в нужном порядке
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_response_order($anketa, $question, $order)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.response.order',
                'anketa.id' => $anketa,
                'id'        => $question,
                'order'     => $order
            );

        return $this->send();
    }

    /**
     * Удаляет ответ из вопроса анкеты.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
     *
     * @param string $anketa   - код анкеты
     * @param string $question - код вопроса
     * @param string $answer   - код ответа
     *
     * @return array
     * @throws \Exception
     */
    public function anketa_quest_response_delete($anketa, $question, $answer)
    {
        $this->params = $this->auth + array(
                'action'    => 'anketa.quest.response.delete',
                'anketa.id' => $anketa,
                'quest.id'  => $question,
                'id'        => $answer
            );

        return $this->send();
    }

    /**
     * Проверяет список адресов на синтаксическую верность, доступность и возвращает нормализованый вариант написания.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%BA%D0%B0-%D0%B0%D0%B4%D1%80%D0%B5%D1%81%D0%BE%D0%B2][Документация]
     *
     * @param array $list    - список емэйлов
     * @param int   $smtp    - проверять доступность по smtp (1) или нет (0)
     * @param int   $timeout - таймаут в секундах
     *
     * @return array
     * @throws \Exception
     */
    public function email_test($list, $smtp = 0, $timeout = 15)
    {
        $this->params = $this->auth + array(
                'action'       => 'email.test',
                'smtp.test'    => $smtp,
                'smtp.timeout' => $timeout,
                'list'         => $list
            );

        return $this->send();
    }

    /**
     * Запрашивает ответы подписчика.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D1%8B-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param string $email - емэйл подписчика
     *
     * @return array
     * @throws \Exception
     */
    public function member_get($email)
    {
        $this->params = $this->auth + array(
                'action' => 'member.get',
                'email'  => $email
            );

        return $this->send();
    }

    /**
     * Удаляет пользователя из списка рассылки.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param  mixed $data - список удаляемых емэйлов (array) или код группы (string)
     * @param  bool  $sync -  флаг асинхронного запуска
     *
     * @return array
     * @throws \Exception
     */
    public function member_delete($data, $sync = false)
    {
        $this->params = $this->auth + array(
                'action' => 'member.delete',
                'sync'   => $sync
            );

        if (is_array($data)) {
            $this->params['list'] = $data;
        } else {
            $this->params['group'] = $data;
        }

        return $this->send();
    }

    /**
     * Извлекает список выпусков в архиве.
     * Входные параметры — необязательные фильтры.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%BE%D0%B2-%D0%B2-%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B5][Документация]
     *
     * @param  string $from   - начиная с даты (формат YYYY-MM-DD)
     * @param  string $to     - заканчивая датой (формат YYYY-MM-DD)
     * @param  array  $groups -  массив с идентификаторами групп
     * @param  string $format - формат выпуска
     *
     * @return array
     * @throws \Exception
     */
    public function issue_list($from = '1900-01-01', $to = null, $groups = array(), $format = 'email')
    {
        $this->params = $this->auth + array(
                'action' => 'issue.list',
                'from'   => $from,
                'group'  => $groups,
                'format' => $format
            );

        $this->param('upto', $to);

        return $this->send();
    }

    /**
     * Извлекает информацию о выпуске в архиве.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%B0-%D0%B2-%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B5][Документация]
     *
     * @param  int $id - уникальный идентификатор выпуска
     *
     * @return array
     * @throws \Exception
     */
    public function issue_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'issue.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Извлекает статистику активности подписчиков.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%BE%D0%B2][Документация]
     *
     * @param  array  $filter -  фильтр; может содержать следующие параметры:
     *                        gid — код группы
     *                        from — событие произошло начиная с даты (включительно; формат ГГГГ-ММ-ДД)
     *                        to — событие произошло не позже даты (включительно; формат ГГГГ-ММ-ДД)
     *                        issue.from — событие произошло из выпуска вышедшего начиная с даты (включительно; формат
     *                        ГГГГ-ММ-ДД) issue.to — событие произошло из выпуска вышедшего не позже даты
     *                        (включительно;
     *                        формат ГГГГ-ММ-ДД) из следующих параметров можно указать только один (исключение: можно
     *                        совместить with_deliver и with_errs) with_deliver => 1 — включить получивших выпуск
     *                        with_errs => 1 — включить подписчиков с ошибками доставки with_remove => 1 — включить
     *                        отписавшихся with_read => 1 — включить прочитавших выпуск with_links => 1 — включить
     *                        перешедших по ссылкам
     * @param  mixed  $result -  способ возврата результата; тип (response|save) или список получателей (array)
     * @param  string $format - формат вывода (csv|xlsx)
     * @param  int    $limit  -   число строк на странице
     * @param  int    $page   -   текущая страница
     *
     * @return array
     * @throws \Exception
     */
    public function stat_activity($filter = array(), $result = 'save', $format = 'csv', $limit = 20, $page = 1)
    {
        $this->params = $this->auth + $filter + array(
                'action'   => 'stat.activity',
                'sort'     => 'date',
                'desc'     => 1,
                'result'   => is_array($result) ? 'email' : $result,
                'page'     => $page,
                'pagesize' => $limit
            );

        switch ($this->params['result']) {
            case 'email':
                $this->params['email'] = $result;
                $this->params['result.format'] = $format;
                break;
            case 'save':
                $this->params['result.format'] = $format;
                break;
        }

        return $this->send();
    }

    /**
     * Запрашивает статистику по выпускам.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%BE%D0%B2][Документация]
     *
     * @param  string $from      - начиная с даты (формат YYYY-MM-DD)
     * @param  string $to        - заканчивая датой (формат YYYY-MM-DD)
     * @param  array  $groups    -  список идентификаторов групп
     * @param  string $groupby   - способ группировки по времени
     * @param  string $total     - итог по всем записям (none — не нужен | yes  — нужен | only — только итог)
     * @param  bool   $withempty - вывод статистики по группам подписчиков без единого выпуска
     * @param  mixed  $result    - способ возврата результата; тип (response|save) или список получателей (array)
     * @param  string $format    - формат вывода (csv|xlsx)
     *
     * @return array
     * @throws \Exception
     */
    public function stat_issue(
        $from = null,
        $to = null,
        $groups = array(),
        $groupby = 'YM',
        $total = 'none',
        $withempty = false,
        $result = 'save',
        $format = 'csv'
    ) {
        $this->params = $this->auth + array(
                'action'    => 'stat.issue',
                'group'     => $groups,
                'groupby'   => $groupby,
                'total'     => $total,
                'withempty' => $withempty,
                'result'    => is_array($result) ? 'email' : $result
            );

        $this->param('issue.from', $from);
        $this->param('issue.upto', $to);

        switch ($this->params['result']) {
            case 'email':
                $this->params['email'] = $result;
                $this->params['result.format'] = $format;
                break;
            case 'save':
                $this->params['result.format'] = $format;
                break;
        }

        return $this->send();
    }

    /**
     * Универсальная функция извлечения статистики.
     * Позволяет получить информацию про переходы, открытия писем, тиражи выпусков и результаты доставки.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%BD%D0%B8%D0%B2%D0%B5%D1%80%D1%81%D0%B0%D0%BB%D1%8C%D0%BD%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param  array  $select -  список полей и функций для выборки
     * @param  array  $filter -  фильтр результатов
     * @param  array  $order  -  сортировка результата
     * @param  mixed  $result - способ возврата результата; тип (response|save) или список получателей (array)
     * @param  string $format - формат вывода (csv|xlsx)
     * @param  int    $skip   -  число пропускаемых от начала строк данных отчёта
     * @param  int    $count  -   число выбираемых строк
     *
     * @return array
     * @throws \Exception
     */
    public function stat_uni(
        $select,
        $filter = array(),
        $order = array(),
        $result = 'save',
        $format = 'csv',
        $skip = 0,
        $count = null
    ) {
        $this->params = $this->auth + array(
                'action' => 'stat.uni',
                'skip'   => $skip,
                'select' => $select,
                'order'  => $order,
                'filter' => $filter,
                'result' => is_array($result) ? 'email' : $result
            );

        $this->param('first', $count);

        switch ($this->params['result']) {
            case 'email':
                $this->params['email'] = $result;
                $this->params['result.format'] = $format;
                break;
            case 'save':
                $this->params['result.format'] = $format;
                break;
        }

        return $this->send();
    }

    /**
     * Возвращает список групп.
     *
     * @link  [https://sendsay.ru/api/api.html#C%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B3%D1%80%D1%83%D0%BF%D0%BF][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function group_list()
    {
        $this->params = $this->auth + array(
                'action' => 'group.list'
            );

        return $this->send();
    }

    /**
     * Создаёт группу.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
     *
     * @param  string $name      - название группы
     * @param  string $type      - тип группы (list|filter)
     * @param  string $id        - код группы
     * @param  string $addr_type - тип адресов (email|msisdn)
     *
     * @return array
     * @throws \Exception
     */
    public function group_create($name, $type = 'list', $id = null, $addr_type = 'email')
    {
        $this->params = $this->auth + array(
                'action'    => 'group.create',
                'name'      => $name,
                'type'      => $type,
                'addr_type' => $addr_type
            );

        $this->param('id', $id);

        return $this->send();
    }

    /**
     * Удаляет участников группы-списка.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9E%D1%87%D0%B8%D1%81%D1%82%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA][Документация]
     *
     * @param  string $id   - код группы
     * @param  mixed  $list -  подписчики, которых надо удалить (all | string — емэйл подписчика | array — список
     *                      емэйлов)
     * @param  bool   $sync -  асинхронность запуска
     *
     * @return array
     * @throws \Exception
     */
    public function group_clean($id, $list = 'all', $sync = false)
    {
        $this->params = $this->auth + array(
                'action' => 'group.clean',
                'id'     => $id,
                'sync'   => $sync
            );

        if ($list === 'all') {
            $this->params['all'] = true;
        } elseif (is_string($list)) {
            $this->params['email'] = $list;
        } elseif (is_array($list)) {
            $this->params['list'] = $list;
        }

        return $this->send();
    }

    /**
     * Изменяет название группы.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
     *
     * @param  string $id   - код группы
     * @param  string $name - название группы
     *
     * @return array
     * @throws \Exception
     */
    public function group_set($id, $name)
    {
        $this->params = $this->auth + array(
                'action' => 'group.set',
                'id'     => $id,
                'name'   => $name
            );

        return $this->send();
    }

    /**
     * Считывает группу.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D1%80%D0%BE%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
     *
     * @param  mixed $id     - код группы (string) или список групп (array)
     * @param  bool  $filter -  возвращать фильтр группы
     *
     * @return array
     * @throws \Exception
     */
    public function group_get($id, $filter = false)
    {
        $this->params = $this->auth + array(
                'action'      => 'group.get',
                'id'          => $id,
                'with_filter' => $filter
            );

        return $this->send();
    }

    /**
     * Создаёт копию подписчиков группы.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B-%D0%A0%D0%B0%D1%81%D1%88%D0%B8%D1%80%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA][Документация]
     *
     * @param  mixed  $from  - код группы (string) или список подписчиков (array)
     * @param  string $to    - код группы
     * @param  bool   $clean -  очистить группу перед внесением
     * @param  bool   $sync  -  асинхронность вызова
     *
     * @return array
     * @throws \Exception
     */
    public function group_snapshot($from, $to, $clean = true, $sync = false)
    {
        $this->params = $this->auth + array(
                'action' => 'group.snapshot',
                'to'     => array('id' => $to, 'clean' => $clean),
                'from'   => array('sync' => $sync)
            );

        if (is_string($from)) {
            $this->params['from']['email'] = $from;
        } elseif (is_array($from)) {
            $this->params['from']['list'] = $from;
        }

        return $this->send();
    }

    /**
     * Возвращает правила фильтрации группы.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D0%B0-%D1%84%D0%B8%D0%BB%D1%8C%D1%82%D1%80%D0%B0%D1%86%D0%B8%D0%B8-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B][Документация]
     *
     * @param  string $id -  код группы
     *
     * @return array
     * @throws \Exception
     */
    public function group_filter_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'group.filter.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Изменяет правила фильтрации группы.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D0%B0-%D1%84%D0%B8%D0%BB%D1%8C%D1%82%D1%80%D0%B0%D1%86%D0%B8%D0%B8-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B][Документация]
     *
     * @param  string $id     - код группы
     * @param  array  $filter - правила фильтрации
     *
     * @return array
     * @throws \Exception
     */
    public function group_filter_set($id, $filter)
    {
        $this->params = $this->auth + array(
                'action' => 'group.filter.set',
                'id'     => $id,
                'filter' => $filter
            );

        return $this->send();
    }

    /**
     * Удаляет группу.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
     *
     * @param  string $id - код группы
     *
     * @return array
     * @throws \Exception
     */
    public function group_delete($id)
    {
        $this->params = $this->auth + array(
                'action' => 'group.delete',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Возвращает общую статистику по группе.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9E%D0%B1%D1%89%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%BF%D0%BE-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D0%B5][Документация]
     *
     * @param  array  $groups - коды групп; если пусто - по всем
     * @param  mixed  $result -  способ возврата результата; тип (response|save) или список получателей (array)
     * @param  string $format - формат вывода (csv|xlsx)
     *
     * @return array
     * @throws \Exception
     */
    public function stat_group_common($groups = array(), $result = 'save', $format = 'csv')
    {
        $this->params = $this->auth + array(
                'action' => 'stat.group.common',
                'group'  => $groups,
                'result' => is_array($result) ? 'email' : $result
            );

        switch ($this->params['result']) {
            case 'email':
                $this->params['email'] = $result;
                $this->params['result.format'] = $format;
                break;
            case 'save':
                $this->params['result.format'] = $format;
                break;
        }

        return $this->send();
    }

    /**
     * Импотирует список подписчиков.
     * В случае указания ссылки на список подписчиков, файл должен быть в UTF-8, а поля разделяться запятыми
     * (CSV-формат). Первой строкой или элементом массива идёт заголовок.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%92%D0%BD%D0%B5%D1%81%D0%B5%D0%BD%D0%B8%D0%B5-%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%BE%D0%B2][Документация]
     *
     * @param  mixed  $data      -  список подписчиков
     *                           string — ссылка на файл с подписчиками;
     *                           integer — идентификатор уже загруженных данных;
     *                           array — массив подписчиков
     * @param  array  $group     - группа импорта подписчиков
     * @param  string $exist     - действие если адрес существует (overwrite|ignore|error)
     * @param  string $addr_type - тип вносимых адресов
     *
     * @return array
     * @throws \Exception
     */
    public function member_import($data, $group = null, $exist = 'overwrite', $addr_type = 'email')
    {
        $this->params = $this->auth + array(
                'action'      => 'member.import',
                'addr_type'   => $addr_type,
                'if_exists'   => $exist,
                'charset'     => 'utf-8',
                'users.list'  => $data,
                'auto_group'  => ['id' => $group],
                'clean_group' => 0
            );

        return $this->send();
    }

    /**
     * Чтение черновика.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param  int $id -  код черновика
     *
     * @return array
     * @throws \Exception
     */
    public function issue_draft_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'issue.draft.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Создает или изменяет параметры и содержимое черновиков. Вызов не может быть применён к предустановленным
     * черновикам.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B8%D0%BB%D0%B8-%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param  array $params - параметры черновика
     *                       name — название черновика
     *                       format — формат черновика (html|sms|text)
     *                       division — идентификатор подразделения, имеющего доступ к черновику
     *                       from — емэйл отправителя
     *                       sender — имя отправителя
     *                       reply.email — обратный адрес для ответа
     *                       reply.name — имя для обратного адреса для ответа
     *                       to.name — имя получателя
     *                       subject — тема письма
     *                       text — содердимое черновика
     * @param  int   $id     -  код черновика
     *
     * @return array
     * @throws \Exception
     */
    public function issue_draft_set($params, $id = null)
    {
        $this->params = $this->auth + array(
                'action'           => 'issue.draft.set',
                'obj'              => $params,
                'return_fresh_obj' => true
            );

        $this->param('id', $id);

        return $this->send();
    }

    /**
     * Удаляет черновик.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
     *
     * @param  mixed $ids - код черновика (int) или список черновиков (array) к удалению
     *
     * @return array
     * @throws \Exception
     */
    public function issue_draft_delete($ids)
    {
        $this->params = $this->auth + array(
                'action' => 'issue.draft.delete',
                'id'     => $ids
            );

        return $this->send();
    }

    /**
     * Асинхронно отправляет выпуск.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9E%D1%82%D0%BE%D1%81%D0%BB%D0%B0%D1%82%D1%8C-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA][Документация]
     *
     * @param  string $group      - способ выпуска (код группы | masssending - экспресс-выпуск | personal -
     *                            транзакционное письмо)
     * @param  mixed  $from       -  код шаблона (int) или емэйл отправителя (string)
     * @param  string $sender     - имя отправителя (string) или массив экстра данных (array)
     * @param  string $subject    - тема письма
     * @param  string $text       - содержимое письма
     * @param  string $sendwhen   - когда выпустить (now - сейчас | save - отложить на хранение)
     * @param         $laterTime
     * @param         $email
     * @param  array  $users_list -  список получателей
     * @param  array  $relink     - параметры преобразования ссылок для учёта перехода по ним
     * @param  string $format     - формат содержимого (html|text)
     *
     * @return array
     * @throws \Exception
     */
    public function issue_send(
        $group,
        $from,
        $sender = '',
        $subject = '',
        $text = '',
        $sendwhen = 'now',
        $laterTime = null,
        $email = null,
        $users_list = null,
        $relink = array(),
        $format = 'html'
    ) {

        $params = array(
            'action'       => 'issue.send',
            'group'        => $group,
            'letter'       => array(
                'draft.id'   => is_numeric($from) ? $from : null,
                'from.email' => $from,
                'from.name'  => $sender,
                'subject'    => $subject,
                'message'    => array($format => $text)
            ),
            'sendwhen'     => $sendwhen,
            'relink'       => is_null($relink) ? 0 : 1,
            'relink.param' => is_null($relink) ? array() : array_merge(array('link' => 1, 'image' => 0, 'test' => 1),
                $relink),
            'email'        => $email
        );

        if ($sendwhen == 'later') {
            $params['later.time'] = $laterTime;
        }

        $this->params = $this->auth + $params;

        if (is_array($sender)) {
            $this->params['extra'] = $sender;
        }

        $this->param('users.list', $users_list);

        return $this->send();
    }

    /**
     * Возвращает список последовательностей.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BEc%D1%82%D0%B5%D0%B9][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_list()
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.list'
            );

        return $this->send();
    }

    /**
     * Создаёт последовательность.
     *
     * @link  [https://sendsay.ru/api/api.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
     *
     * @param  string $name     -  название последовательности
     * @param  bool   $onlyonce -  однократность последовательности
     * @param  bool   $closed   -  закрытость для новых участников
     * @param  bool   $rog      -  возобновлять прохождение при увеличении количества шагов
     * @param  bool   $pause    -  отстановка последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_create($name, $onlyonce = false, $closed = false, $rog = false, $pause = false)
    {
        $this->params = $this->auth + array(
                'action'            => 'sequence.create',
                'name'              => $name,
                'onlyonce'          => $onlyonce,
                'parrallel'         => 0,
                'closed'            => $closed,
                'resume_on_growing' => $rog,
                'pause'             => $pause
            );

        return $this->send();
    }

    /**
     * Возвращает параметры последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D1%80%D0%BE%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
     *
     * @param  int $id - код последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Изменяет параметры последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
     *
     * @param  int    $id       -  код последовательности
     * @param  string $name     - название последовательности
     * @param  bool   $onlyonce - однократность последовательности
     * @param  bool   $closed   -  закрытость для новых участников
     * @param  bool   $rog      - возобновлять прохождение при увеличении количества шагов
     * @param  bool   $pause    -  отстановка последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_set($id, $name = null, $onlyonce = null, $closed = null, $rog = null, $pause = null)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.set',
                'id'     => $id
            );

        $this->param('name', $name);
        $this->param('pause', $pause);
        $this->param('closed', $closed);
        $this->param('onlyonce', $onlyonce);
        $this->param('resume_on_growing', $rog);

        return $this->send();
    }

    /**
     * Удаляет последовательность.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
     *
     * @param  int $id - код последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_delete($id)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.delete',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Получает список шагов последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%88%D0%B0%D0%B3%D0%BE%D0%B2][Документация]
     *
     * @param  int $id - код последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_steps_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.steps.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Задаёт шаги последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%88%D0%B0%D0%B3%D0%BE%D0%B2][Документация]
     *
     * @param  int   $id    - код последовательности
     * @param  array $steps - шаги последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_steps_set($id, $steps)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.steps.set',
                'id'     => $id,
                'list'   => $steps
            );

        return $this->send();
    }

    /**
     * Запрашивает статистику последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int $id - код последовательности
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_stats($id)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.stats',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Возвращает список участников последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%83%D1%87%D0%B0%D1%81%D1%82%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int    $id    - код последовательности
     * @param  string $group - способ группировки (member|step) или не группировать (NULL)
     * @param  array  $steps - список интересующих шагов
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_list($id, $group = null, $steps = null)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.list',
                'id'     => $id
            );

        $this->param('groupby', $group);
        $this->param('steps', $steps);

        return $this->send();
    }

    /**
     * Отправляет подписчика на последовательность.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9D%D0%B0%D1%87%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int   $id    - код последовательности
     * @param  mixed $users -  список емэйлов (array) или код группы (string)
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_start($id, $users)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.start',
                'id'     => $id
            );

        if (is_array($users)) {
            $this->param('list', $users);
        } else {
            $this->param('group', $users);
        }

        return $this->send();
    }

    /**
     * Приостанавливает прохождение подписчиком последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D1%80%D0%B8%D0%BE%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int   $id    - код последовательности
     * @param  mixed $users - список емэйлов (array) или код группы (string)
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_pause($id, $users)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.pause',
                'id'     => $id
            );

        if (is_array($users)) {
            $this->param('list', $users);
        } else {
            $this->param('group', $users);
        }

        return $this->send();
    }

    /**
     * Возобновляет прохождение подписчиком последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%92%D0%BE%D0%B7%D0%BE%D0%B1%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int   $id    - код последовательности
     * @param  mixed $users - список емэйлов (array) или код группы (string)
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_resume($id, $users)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.resume',
                'id'     => $id
            );

        if (is_array($users)) {
            $this->param('list', $users);
        } else {
            $this->param('group', $users);
        }

        return $this->send();
    }

    /**
     * Завершает прохождение подписчиками последовательности.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D1%80%D0%B5%D1%80%D0%B2%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
     *
     * @param  int   $id    - код последовательности
     * @param  mixed $users - список емэйлов (array) или код группы (string)
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_stop($id, $users)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.stop',
                'id'     => $id
            );

        if (is_array($users)) {
            $this->param('list', $users);
        } else {
            $this->param('group', $users);
        }

        return $this->send();
    }

    /**
     * Возвращает список последовательностей, где числится указанный подписчик.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D1%87%D0%B0%D1%81%D1%82%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F-%D0%B2-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8F%D1%85][Документация]
     *
     * @param  string $email - емэйл подписчика
     * @param  mixed  $id    - код последовательности (int)
     *
     * @return array
     * @throws \Exception
     */
    public function sequence_member_membership($email, $id = null)
    {
        $this->params = $this->auth + array(
                'action' => 'sequence.member.membership',
                'email'  => $email
            );

        $this->param('id', $id);

        return $this->send();
    }

    /**
     * Загружает картинку на сервер.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B0%D1%82%D1%8C-%D1%84%D0%B0%D0%B9%D0%BB][Документация]
     *
     * @param  string $from - расположение загружаемого файла
     * @param  string $to   - директория загрузки файла с именем файла (несуществующие каталоги не создаются)
     *
     * @return array
     * @throws \Exception
     */
    public function put_file($from, $to)
    {
        $this->params = $this->auth + array(
                'action'   => 'rfs.file.put',
                'domain'   => 'image',
                'encoding' => 'base64',
                'data'     => base64_encode(file_get_contents($from)),
                'path'     => $to
            );

        return $this->send();
    }

    /**
     * Создаёт каталог.
     *
     * @link  [https://sendsay.ru/api/api.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3][Документация]
     *
     * @param $path - string  полный путь с названием каталога (несуществующие каталоги создаются)
     *
     * @return array
     * @throws \Exception
     */
    public function mkdir($path)
    {
        $this->params = $this->auth + array(
                'action' => 'rfs.dir.make',
                'domain' => 'image',
                'path'   => $path
            );

        return $this->send();
    }

    /**
     * Удаляет каталог.
     * Примечание: католог должен быть пустым.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3][Документация]
     *
     * @param  string $path - полный путь с названием каталога
     *
     * @return array
     * @throws \Exception
     */
    public function rm($path)
    {
        $this->params = $this->auth + array(
                'action' => 'rfs.dir.delete',
                'domain' => 'image',
                'path'   => $path
            );

        return $this->send();
    }

    /**
     * Возвращает список настроек.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function sys_settings_get()
    {
        $this->params = $this->auth + array(
                'action' => 'sys.settings.get'
            );

        return $this->send();
    }

    /**
     * Сохраняет настройки.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BC%D0%B5%D0%BD%D1%8F%D1%82%D1%8C-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8][Документация]
     *
     * @param  array $options - массив изменяемых параметров
     *
     * @return array
     * @throws \Exception
     */
    public function sys_settings_set($options)
    {
        $this->params = $this->auth + array(
                'action' => 'sys.settings.set',
                'list'   => $options
            );

        return $this->send();
    }

    /**
     * Сохраняет настройки.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9F%D0%BE%D0%BC%D0%B5%D0%BD%D1%8F%D1%82%D1%8C-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8][Документация]
     *
     * @param  array $options - массив изменяемых параметров
     *
     * @return array
     * @throws \Exception
     */
    public function sys_storage_get($options)
    {
        $this->params = $this->auth + array(
                'action' => 'sys.settings.set',
                'list'   => $options
            );

        return $this->send();
    }

    /**
     * Возвращает список пользователей.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D0%B5%D0%B9][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function user_list()
    {
        $this->params = $this->auth + array(
                'action' => 'user.list'
            );

        return $this->send();
    }

    /**
     * Создаёт пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
     *
     * @param  string $login    - саблогин
     * @param  string $password - пароль
     * @param  string $email    - адрес получателя письма с данными пользователя
     *
     * @return array
     * @throws \Exception
     */
    public function user_create($login, $password, $email = null)
    {
        $this->params = $this->auth + array(
                'action'   => 'user.create',
                'sublogin' => $login,
                'password' => $password
            );

        $this->param('email', $email);

        return $this->send();
    }

    /**
     * Удаляет пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
     *
     * @param  string $login - саблогин
     *
     * @return array
     * @throws \Exception
     */
    public function user_delete($login)
    {
        $this->params = $this->auth + array(
                'action'   => 'user.delete',
                'sublogin' => $login
            );

        return $this->send();
    }

    /**
     * Изменяет пароль и статус пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8F-%D0%B8-%D1%81%D1%82%D0%B0%D1%82%D1%83%D1%81%D0%B0-%D0%BB%D1%8E%D0%B1%D0%BE%D0%B3%D0%BE-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
     *
     * @param  string $login        - саблогин
     * @param  int    $status       - состояние пользователя (−1 — заставить сменить пароль | 0 — активировать | 1 —
     *                              заблокировать)
     * @param  string $old_password - старый пароль
     * @param  string $new_password - новый пароль
     * @param  string $email        - адрес получателя письма с данными пользователя
     *
     * @return array
     * @throws \Exception
     */
    public function user_set($login, $status, $old_password = null, $new_password = null, $email = null)
    {
        $this->params = $this->auth + array(
                'action'   => 'user.set',
                'sublogin' => $login,
                'status'   => $status
            );

        $this->param('email', $email);
        $this->param('password.old', $old_password);
        $this->param('password.new', $new_password);

        return $this->send();
    }

    /**
     * Изменяет пароль текущего пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8F-%D1%81%D0%B5%D0%B1%D0%B5][Документация]
     *
     * @param  string $old_password - старый пароль
     * @param  string $new_password - новый пароль
     *
     * @return array
     * @throws \Exception
     */
    public function sys_password_set($old_password, $new_password)
    {
        $this->params = $this->auth + array(
                'action'       => 'sys.password.set',
                'password.old' => $old_password,
                'password.new' => $new_password
            );

        return $this->send();
    }

    /**
     * Отправляет сообщение в техподдержку.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%9E%D0%B1%D1%80%D0%B0%D1%89%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2-%D1%81%D0%B0%D0%BF%D0%BF%D0%BE%D1%80%D1%82][Документация]
     *
     * @param  string $email - емэйл для связи
     * @param  string $text  - текст сообщения
     *
     * @return array
     * @throws \Exception
     */
    public function sys_message($email, $text)
    {
        $this->params = $this->auth + array(
                'action' => 'sys.message',
                'email'  => $email,
                'text'   => $text
            );

        return $this->send();
    }

    /**
     * Запрашивает лог активности аккаунта.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%96%D1%83%D1%80%D0%BD%D0%B0%D0%BB-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B][Документация]
     *
     * @param  string $from -  дата события от (формат ГГГГ-ММ-ДД ЧЧ:ММ:СС)
     * @param  string $to   - дата события по (формат ГГГГ-ММ-ДД ЧЧ:ММ:СС)
     *
     * @return array
     * @throws \Exception
     */
    public function sys_log($from = null, $to = null)
    {
        $this->params = $this->auth + array(
                'action' => 'sys.log'
            );

        $this->param('from', $from);
        $this->param('upto', $to);

        return $this->send();
    }

    /**
     * Запрашивает права доступа пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D1%80%D0%B0%D0%B2][Документация]
     *
     * @param  string $login -  логин пользователя
     *
     * @return array
     * @throws \Exception
     */
    public function rights_get($login)
    {
        $this->params = $this->auth + array(
                'action' => 'rights.get',
                'user'   => $login
            );

        return $this->send();
    }

    /**
     * Уставнавливает права доступа пользователя.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%BF%D1%80%D0%B0%D0%B2][Документация]
     *
     * @param  string $login  - логин пользователя
     * @param  array  $rights - список устанавливаемых прав
     *
     * @return array
     * @throws \Exception
     */
    public function rights_set($login, $rights)
    {
        $this->params = $this->auth + array(
                'action' => 'rights.set',
                'user'   => $login,
                'list'   => $rights
            );

        return $this->send();
    }

    /**
     * Возвращает список внешних авторизаций.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B8%D1%85-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B9][Документация]
     *
     * @return array
     * @throws \Exception
     */
    public function authext_list()
    {
        $this->params = $this->auth + array(
                'action' => 'authext.list'
            );

        return $this->send();
    }

    /**
     * Считывает параметры внешней авторизации.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
     *
     * @param  string $id - код внешней авторизации
     *
     * @return array
     * @throws \Exception
     */
    public function authext_get($id)
    {
        $this->params = $this->auth + array(
                'action' => 'authext.get',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Создаёт внешнюю авторизацию.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
     *
     * @param  string $login - логин внешней авторизации
     * @param  string $token - токен внешней авторизации (refresh token)
     *
     * @return array
     * @throws \Exception
     */
    public function authext_create($login, $token)
    {
        $this->params = $this->auth + array(
                'action' => 'authext.create',
                'type'   => 8, // Google Analytics
                'login'  => $login,
                'token'  => $token
            );

        return $this->send();
    }

    /**
     * Изменяет внешнюю авторизацию.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
     *
     * @param  string $id    - код внешней авторизации
     * @param  string $login - логин внешней авторизации
     * @param  string $token - токен внешней авторизации (refresh token)
     *
     * @return array
     * @throws \Exception
     */
    public function authext_set($id, $login = null, $token = null)
    {
        $this->params = $this->auth + array(
                'action' => 'authext.set',
                'id'     => $id,
                'type'   => 8 // Google Analytics
            );

        $this->param('login', $login);
        $this->param('token', $token);

        return $this->send();
    }

    /**
     * Удаляет внешнюю авторизацию.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
     *
     * @param  string $id - код внешней авторизации
     *
     * @return array
     * @throws \Exception
     */
    public function authext_delete($id)
    {
        $this->params = $this->auth + array(
                'action' => 'authext.delete',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Возвращает информацию об авторизации в Google Analytics.
     *
     * @link  [https://sendsay.ru/api/api.html#%D0%98%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%86%D0%B8%D1%8F-%D0%BE%D0%B1-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8-%D0%B2-Google-Analitics][Документация]
     *
     * @param  string $id - код внешней авторизации
     *
     * @return array
     * @throws \Exception
     */
    public function authext_ga_props($id)
    {
        $this->params = $this->auth + array(
                'action' => 'authext.ga.props',
                'id'     => $id
            );

        return $this->send();
    }

    /**
     * Форматирует JSON-строку для отладки.
     *
     * @param  string $json - исходная JSON-строка
     *
     * @return string
     */
    private function json_dump($json)
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = "\t";
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {
            $char = substr($json, $i, 1);

            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            } elseif (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $result .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;

                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    /**
     * Добавляет значение к массиву параметров запроса.
     *
     * @param  string $name  - название параметра
     * @param  mixed  $value - значение параметра
     */
    protected function param($name, $value = null)
    {
        if ($value !== null) {
            $this->params[$name] = $value;
        }
    }

    /**
     * Отправляет данные в Sendsay.
     *
     * @param string $redirect
     *
     * @return array
     * @throws \Exception
     */
    protected function send($redirect = '')
    {
        if ($this->debug) {
            echo '<pre>Запрос:' . "\n" . $this->json_dump(print_r(json_encode($this->params, JSON_UNESCAPED_UNICODE),
                    true)) . "\n";
        }

        $curl = curl_init('https://api.sendsay.ru/' . $redirect . '?apiversion=100&json=1');

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            'request=' . urlencode(json_encode($this->params, JSON_UNESCAPED_UNICODE)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $json = json_decode($result, true);

        if ($this->debug) {
            echo 'Ответ:' . "\n" . $this->json_dump($result) . '</pre>';
        }

        if (array_key_exists('errors', $json)) {
            if (!$this->debug) {
                $message = implode(';', array_column($json['errors'], 'id'));
            } else {
                $errors_id = implode(';', array_column($json['errors'], 'id'));
                $errors_text = implode(';', array_column($json['errors'], 'explain'));

                $message = json_encode([
                    'http_code'   => $info['http_code'],
                    'errors_id'   => $errors_id,
                    'errors_text' => $errors_text
                ], JSON_UNESCAPED_UNICODE);
            }

            throw new \Exception($message);
        }

        if (!$json) {
            $message = json_encode([
                'http_code'   => $info['http_code'],
                'errors_id'   => 'error/bad_json',
                'errors_text' => $result
            ], JSON_UNESCAPED_UNICODE);
            throw new \Exception($message);

        }

        if (array_key_exists('REDIRECT', $json)) {
            return $this->send($json['REDIRECT']);
        }

        return $json;
    }
}
