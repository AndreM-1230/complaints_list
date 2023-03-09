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
            //var_dump($e);
        }
        return ($arr);
    }

    function sql_length($sql) :int
    {
        global $db;
        $res = $db->query($sql);
        $count = $res->fetchColumn();
        return $count;
    }
    sql_connect(); // соединение с базой.
    // Условия по GET-запросам
    function get_complaints_list_data(): array
    {
        if(!isset($_SESSION['complaints_type']))
            $_SESSION['complaints_type'] = 1;
        if(!isset($_SESSION['selsize']))
            $_SESSION['selsize']=5;
        $firstrow=$_SESSION['selsize']*($_SESSION['page']-1);
        if(!isset($_SESSION['complaints_type']))
            $_SESSION['complaints_type']=1;
        if($_SESSION['complaints_type'] == 2){
            $user = $_SESSION['account'][0]['id'];
            $_SESSION['array_count']= sql_length("SELECT COUNT(*) FROM complaints_list WHERE user = $user");
            $complaints_arr=sqltab("SELECT * FROM complaints_list WHERE user = $user ORDER BY id DESC LIMIT $firstrow, $_SESSION[selsize]");
        }
        else{
            $_SESSION['array_count']= sql_length("SELECT COUNT(*) FROM complaints_list");
            $complaints_arr=sqltab("SELECT * FROM complaints_list ORDER BY id DESC LIMIT $firstrow, $_SESSION[selsize]");
        }
        return $complaints_arr;
    }
    function complaint_list():string
    {
        $complaints_arr=get_complaints_list_data();
        $return ="<div class='page-header' id='banner'>
                        <div class='row'>
                            <div class='col-lg-12'>
                                <h1>Жалобы?</h1>
                            </div>
                        </div>
                  </div>
                  <table class='table
                         table-hover
                         table-borderless
                         align-middle col-lg-12' data-tblname='tbl' style='text-align:center;'><tbody>
            <tr class='table-dark'>
                <td class='col-lg-1'>#</td>
                <td class='col-lg-3'>Содержание:</td>
                <td class='col-lg-1'>Статус:</td>
                <td class='col-lg-1'>Пользователь:</td>
                <td class='col-lg-1'>Фиксер:</td>
                <td class='col-lg-3'>Ответ:</td></tr>";
        foreach($complaints_arr as $value){
            $user = sqltab("SELECT login FROM users WHERE id = $value[user]");
            $fixer = sqltab("SELECT login FROM users WHERE id = $value[admin]");
            $status = sqltab("SELECT status_name FROM status WHERE id = $value[status]");



            $return .="<tr>
                <td>". $value['id'] ."</td>";
            if(strlen($value['complaint_text']) > 15){
                $accordionID = 'acc' . $value['id'] . 'id';
                $data_accordionID = '#' . $accordionID;

                $collapse_accordion = 'coll' . $value['id'] . 'collapse';
                $data_collapse = '#' . $collapse_accordion;

                $heading_accordion = 'head' . $value['id'] . 'heading';

                $data_length =substr($value['complaint_text'], 0, 15);

                $return .= accordion($accordionID,
                    $data_accordionID,
                    $collapse_accordion,
                    $data_collapse,
                    $heading_accordion,
                    $data_length,
                    $value['complaint_text']);
            }
            else{
                $return .="<td>$value[complaint_text]</td>";
            }

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
                $return .="<td>" .  $user[0]['login'] ." </td>";
                if(!isset($fixer[0]['login'])){
                    $return .="<td><form action='take_task.php' method='post'>
                                    <input type='submit'
                                    name='$value[id]'
                                    class='btn btn-outline-warning'
                                    value='Взять'/>
                                </form></td>";
                }
                else{
                    $return .="<td>" .  $fixer[0]['login'] ." </td>";
                }
                if($value['fix_comment'] != NULL){
                    if(strlen($value['fix_comment']) > 15){
                        $accordionID = 'facc' . $value['id'] . 'id';
                        $data_accordionID = '#' . $accordionID;

                        $collapse_accordion = 'fcoll' . $value['id'] . 'collapse';
                        $data_collapse = '#' . $collapse_accordion;

                        $heading_accordion = 'fhead' . $value['id'] . 'heading';

                        $data_length =substr($value['fix_comment'], 0, 15);

                        $return .= accordion($accordionID,
                            $data_accordionID,
                            $collapse_accordion,
                            $data_collapse,
                            $heading_accordion,
                            $data_length,
                            $value['fix_comment']);
                    }
                    else{
                        $return .="<td>" .  $value['fix_comment'] ." </td>";
                    }

                }
                else{
                    if(isset($fixer[0]['login']) and $fixer[0]['login'] == $_SESSION['account'][0]['login']){
                        $return .="<td><input type='button'
                                    id='$value[id]'
                                    name='$value[id]'
                                    class='btn btn-outline-warning'
                                    onclick='comment_form(this)'
                                    value='Ответить'/></td>";
                    }
                    else{
                        $return .="<td></td>";
                    }

                }
            $return .="</tr>";
        }
    $return .="</tbody></table>";
        $return .= Pages($_SESSION['array_count'],$_SESSION['selsize'],$_SESSION['page']);
        return $return;
    }

    function accordion($accordionID,
                       $data_accordionID,
                       $collapse_accordion,
                       $data_collapse,
                       $heading_accordion,
                       $data_length,
                       $text):string
    {
        return "<td style='max-width: 45ch !important;'>
                    <div class='accordion' id='$accordionID' style='max-width: 45ch !important;' >
                        <div class='accordion-item'>
                          <h2 class='accordion-header' id='$heading_accordion'>
                            <button class='accordion-button'
                             type='button' 
                             data-bs-toggle='collapse' 
                             data-bs-target='$data_collapse' 
                             aria-expanded='true' 
                             aria-controls='$collapse_accordion'>
                              $data_length
                            </button>
                          </h2>
                          <div id='$collapse_accordion' class='accordion-collapse collapse' aria-labelledby='$heading_accordion' data-bs-parent='$data_accordionID'>
                            <div class='accordion-body' style='text-align:left;'>$text</div>
                          </div>
                        </div>
                    </div>
                </td>";
    }

    function short_complaint_list() :string
    {   switch ($_SESSION['complaints_type']){
        case 1:
            $btn_all  = 'btn-primary disabled';
            $btn_user = 'btn-outline-primary';
            break;
        case 2:
            $btn_all  = 'btn-outline-primary';
            $btn_user = 'btn-primary disabled';
            break;
        default:
            $btn_all = '';
            $btn_user = '';
            break;
        }
        $complaints_arr=get_complaints_list_data();
        $return ="<div class='btn-group' role='group' aria-label='Basic outlined example'>
                    <input class='btn $btn_all'
                     type='submit'
                      id='com_sel'
                       aria-current='page'
                        value='Показать все'
                         onclick='com_sel(1)'/>
                    <input class='btn $btn_user'
                     type='submit'
                      id='com_sel'
                       aria-current='page'
                        value='Показать мои'
                         onclick='com_sel(2)'/>
                    <input class='btn btn-outline-primary'
                     type='button'
                      data-bs-toggle='modal'
                      name='create_complaint'
                       data-bs-target='#my_personal_form' value='Создать жалобу'/>
                  </div>
                  <table class='table
                         table-hover
                         table-borderless
                         align-middle' data-tblname='tbl' style='text-align:center;'><tbody>
            <tr class='table-dark'><td>#</td>
                <td>Содержание:</td>
                <td>Статус:</td>
                <td>Пользователь:</td>
                <td>Ответ:</td></tr>";
        foreach($complaints_arr as $value){
            $user = sqltab("SELECT login FROM users WHERE id = $value[user]");
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
            $return .="<td>" .  $user[0]['login'] ." </td>";
            if($value['fix_comment'] != NULL){
                $return .="<td>" .  $value['fix_comment'] ." </td>";
            }
            else{
                $return .="<td></td>";
            }
            $return .="</tr>";
        }

        $return .="</tbody></table>";
        $return .= Pages($_SESSION['array_count'],$_SESSION['selsize'],$_SESSION['page']);
        return $return;
    }

    function imagine_load():void
    {
        echo "<div class='d-flex justify-content-center'>
                <div class='spinner-border' role='status'>
                    <span class='visually-hidden'>Загрузка...</span>
                </div>
        </div>";
    }

