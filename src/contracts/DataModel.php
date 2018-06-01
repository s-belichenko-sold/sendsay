<?php
/**
 * Created by PhpStorm.
 * Author: Stanislav Belichenko, E-mail: s.belichenko@studio-sold.ru, Skype: s.belichenko.sold
 * Company: «SOLD», E-mail: studio@studio-sold.ru
 * Date: 01.06.2018
 * Time: 12:02
 */

namespace StudioSold\Sendsay\Contracts;


interface DataModel
{
    /**
     * Добавляет нового подписчика или обновляет существующего. Ссылки на документацию см. в конкретных реализациях
     * интерфейса
     *
     * @param string $email
     * @param mixed  $data
     * @param bool   $confirm
     * @param int    $template_no_confirm
     * @param int    $template_confirm
     * @param string $if_exists
     * @param string $addr_type
     *
     * @return array
     * @throws \Exception
     */
    public function member_set(
        $email,
        $data = null,
        $confirm = false,
        $template_no_confirm = null,
        $template_confirm = null,
        $if_exists = 'overwrite',
        $addr_type = 'email'
    );
}