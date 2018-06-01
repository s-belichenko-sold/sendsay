<?php
/**
 * Created by PhpStorm.
 * Author: Stanislav Belichenko, E-mail: s.belichenko@studio-sold.ru, Skype: s.belichenko.sold
 * Company: «SOLD», E-mail: studio@studio-sold.ru
 * Date: 01.06.2018
 * Time: 11:55
 */

namespace StudioSold\Sendsay\DataModels;


use StudioSold\Sendsay\Contracts\DataModel;
use StudioSold\Sendsay\SendsayAPI;

/**
 * Присутствующие в текущем каталоге модели данных используются на данный момент
 * одновременно, более современной является соседняя (КД, Ключи Данных). Текущая называется (АВО, Анкета-Вопрос-Ответ)
 *
 * @link https://sendsay.ru/api/api.html#%D0%A4%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D1%8B-%D1%85%D1%80%D0%B0%D0%BD%D0%B5%D0%BD%D0%B8%D1%8F
 */
class QuestionnaireQuestionAnswer extends SendsayAPI implements DataModel
{
    /**
     * Добавляет нового подписчика или обновляет существующего.
     *
     * @link  [https://sendsay.ru/api/api.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0-%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D1%8B-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0-%D0%90%D0%92%D0%9E][Документация]
     *
     * @param string $email               - емэйл подписчика
     * @param array  $data                - массив с данными подписчика (согласно схеме данных АВО)
     * @param bool   $confirm             - необходимость подтверждения внесения в базу
     * @param int    $template_no_confirm - номер шаблона письма, которое высылается, если пользователь был добавлен
     *                                    без необходимости подтверждения
     * @param int    $template_confirm    - номер шаблона письма, которое высылается, если пользователь был добавлен
     *                                    с необходимостью подтверждения (тут будет ссылка для подтверждения)
     * @param string $if_exists           - правило изменения ответов анкетных данных (error|update|overwrite)
     * @param string $addr_type           - тип адреса подписчика (email|msisdn)
     *
     * @return array
     * @throws \Exception
     */
    public function member_set(
        $email,
        $data = null,
        $confirm = false,
        $template_no_confirm = null,
        $template_confirm = null, // TODO: right now this value is not use
        $if_exists = 'overwrite',
        $addr_type = 'email'
    ) {
        $this->params = $this->auth + array(
                'action'         => 'member.set',
                'addr_type'      => $addr_type,
                'email'          => $email,
                'if_exists'      => $if_exists,
                'newbie.confirm' => $confirm,
            );

        if (isset($data)) {
            $this->param('obj', $data);
            $this->param('return_fresh_obj', true);
        }

        $this->param('newbie.letter.confirm', $template_confirm);
        $this->param('newbie.letter.no-confirm', $template_no_confirm);

        return $this->send();
    }
}