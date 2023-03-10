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
        return $res->fetchColumn();
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
        switch ($_SESSION['complaints_type']){
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
        $return ="<div class='page-header' id='banner'>
                        <div class='row'>
                            <div class='col-lg-12'>";
        if($_SESSION['account'][0]['user_stat'] != 0){
            $return .=" <div class='btn-group' role='group' aria-label='Basic outlined example'>
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
                  </div>";
        }else{
            $return .=" <h1>Жалобы?</h1>";
        }

                            $return .="</div>
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
                <td class='col-lg-1'>Пользователь:</td>";
        if($_SESSION['account'][0]['user_stat'] == 0){
                $return .="<td class='col-lg-1'>Фиксер:</td>";
        }
        else{
            $return .="<td class='col-lg-1'>Оценка ответа:</td>";
        }
                $return .="<td class='col-lg-3'>Ответ:</td></tr>";
        foreach($complaints_arr as $value){
            $user = sqltab("SELECT login FROM users WHERE id = $value[user]");
            if(!isset($value['admin'])){
                $fixer = sqltab("SELECT login FROM users WHERE id = $value[admin]");
            }
            $status = sqltab("SELECT status_name FROM status WHERE id = $value[status]");

            $return .="<tr>
                <td>". $value['id'] ."</td>";
            $return .="<td style='max-width: 45ch !important;'>";
            if(strlen($value['complaint_text']) > 30){
                $accordionID = 'acc' . $value['id'] . 'id';
                $data_accordionID = '#' . $accordionID;

                $collapse_accordion = 'coll' . $value['id'] . 'collapse';
                $data_collapse = '#' . $collapse_accordion;

                $heading_accordion = 'head' . $value['id'] . 'heading';

                $data_length =mb_substr($value['complaint_text'], 0, 30);

                $return .= accordion($accordionID,
                    $data_accordionID,
                    $collapse_accordion,
                    $data_collapse,
                    $heading_accordion,
                    $data_length,
                    $value['complaint_text']);
            }
            else{
                $return .=$value['complaint_text'];
            }
            $return .="</td>";

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
            if($_SESSION['account'][0]['user_stat'] == 0){
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
            }
            elseif($value['fix_comment'] != NULL){
                $return .="<td>";

                if(!isset($value['rating'])){
                    if($_SESSION['account'][0]['user_stat'] == 1 and
                        $user[0]['login'] == $_SESSION['account'][0]['login'])
                    {
                        $rating = null;
                        $return .= 'Оцените ответ: ' . rating($rating);
                    }
                }
                else{
                    $return .="$value[rating]";
                }

                $return .= "</td>";
            }

            $return .="<td style='max-width: 45ch !important;'>";


                if($value['fix_comment'] != NULL){
                    if(strlen($value['fix_comment']) > 30){
                        $accordionID = 'facc' . $value['id'] . 'id';
                        $data_accordionID = '#' . $accordionID;

                        $collapse_accordion = 'fcoll' . $value['id'] . 'collapse';
                        $data_collapse = '#' . $collapse_accordion;

                        $heading_accordion = 'fhead' . $value['id'] . 'heading';

                        $data_length =mb_substr($value['fix_comment'], 0, 30);

                        $return .= accordion($accordionID,
                            $data_accordionID,
                            $collapse_accordion,
                            $data_collapse,
                            $heading_accordion,
                            $data_length,
                            $value['fix_comment']);
                    }
                    else{
                        $return .=$value['fix_comment'] . "</br>";
                    }

                    if(isset($value['rating']) and $_SESSION['account'][0]['user_stat'] == 0){
                        $return .="Рейтинг ответа : $value[rating]";
                    }

                }
                else{
                    if(isset($fixer[0]['login']) and $fixer[0]['login'] == $_SESSION['account'][0]['login']){
                        $return .="<input type='button'
                                    id='$value[id]'
                                    name='$value[id]'
                                    class='btn btn-outline-warning'
                                    onclick='comment_form(this)'
                                    value='Ответить'/>";
                    }

                }
            $return .="</td>";
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
        return "<div class='accordion accordion-flush' id='$accordionID' style='max-width: 45ch !important;' >
                        <div class='accordion-item'>
                          <h2 class='accordion-header' id='$heading_accordion'>
                            <button class='accordion-button'
                             type='button' 
                             style='background-color: transparent;'
                             data-bs-toggle='collapse' 
                             data-bs-target='$data_collapse' 
                             aria-controls='$collapse_accordion'>
                              $data_length ...
                            </button>
                          </h2>
                          <div id='$collapse_accordion'
                          class='accordion-collapse collapse'
                          aria-labelledby='$heading_accordion'
                          data-bs-parent='$data_accordionID'
                          style=''>
                            <div class='accordion-body' style='text-align:left;'>$text</div>
                          </div>
                        </div>
                    </div>";
    }



    function rating($id):string
    {   $idf = $id . 'fr';
            return  "<form action='set_rating.php' method='post' id='$idf'></form>
                <div class='star-rating'>
                <div class='star-rating__wrap'>
                <input class='star-rating__input' form='$idf' id='$idf-5' type='submit' name=$id value='5'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-5' title='5'></label>
                <input class='star-rating__input' form='$idf' id='$idf-4' type='submit' name=$id value='4'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-4' title='4'></label>
                <input class='star-rating__input' form='$idf' id='$idf-3' type='submit' name=$id value='3'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-3' title='3'></label>
                <input class='star-rating__input' form='$idf' id='$idf-2' type='submit' name=$id value='2'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-2' title='2'></label>
                <input class='star-rating__input' form='$idf' id='$idf-1' type='submit' name=$id value='1'>
                <label class='star-rating__ico fa fa-star-o fa-lg' for='$idf-1' title='1'></label>
                </div>
                </div>";
    }


