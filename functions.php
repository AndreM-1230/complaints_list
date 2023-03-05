<?php
    function sql_connect(): void
    {
        global $db;
        try {
            $db = new PDO("mysql:host="
                .$_ENV['db_connection']['host'].
                ";dbname=".$_ENV['db_connection']['db'],
                $_ENV['db_connection']['username'],
                $_ENV['db_connection']['password']);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("set names utf8");
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    function sqltab ($sql): array
    {
        global $db;
        $arr = array();
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
            $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e){
            var_dump($e);
        }
        return ($arr);
    }

    sql_connect(); // соединение с базой.
    // Условия по GET-запросам

    function complaint_list():string
    {
        $complaints_arr=sqltab("SELECT * FROM complaints_list");
        $return ="<table class='table
                         table-hover
                         table-borderless
                         align-middle' data-tblname='tbl' style='text-align:center;'><tbody>
            <tr class='table-dark'><td>Номер жалобы:</td>
                <td>Содержание:</td>
                <td>Статус:</td>
                <td>Пользователь:</td>
                <td>Фиксер:</td>
                <td>Ответ:</td></tr>";
        foreach($complaints_arr as $value){
            $user = sqltab("SELECT login FROM users WHERE id = $value[user]");
            $fixer = sqltab("SELECT login FROM users WHERE id = $value[admin]");
            $status = sqltab("SELECT status_name FROM status WHERE id = $value[status]");
            $return .="<tr>
                <td>". $value['id'] ."</td>
                <td>" .  $value['complaint_text'] ." </td>";
            switch ($status[0]['status_name']){
                case 'Ожидает':
                    $return .="<td class='table-light'>" .  $status[0]['status_name'] ." </td>";
                    break;
                case 'В работе':
                    $return .="<td class='table-info'>" .  $status[0]['status_name'] ." </td>";
                    break;
                case 'Выполнено':
                    $return .="<td class='table-success'>" .  $status[0]['status_name'] ." </td>";
                    break;
            }
                $return .="<td>" .  $user[0]['login'] ." </td>
                <td>" .  $fixer[0]['login'] ." </td>";
                if($value['fix_comment'] != NULL){
                    $return .="<td>" .  $value['fix_comment'] ." </td>";
                }
                else{
                    $return .="<td></td>";
                }
            $return .="</tr>";
        }
    $return .="</tbody></table>";

        return $return;
    }

