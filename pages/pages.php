<?php
if(!isset($_SESSION['page']))
    $_SESSION['page'] = 1;
function Pages($array_count, $selsize, $pages): string
{
    $return = '';
    $page_size = [5,15,30];
    $return .= "   <form action='/pages/pages_serv.php' method='post' id='page_id'></form>
                        <nav class='navbar col-lg-6'>

                            <select class='form-select form-select-sm w-25' id='selsize' onchange='selsize()'>";
    //ВЫБОР КОЛИЧЕСТВА ОТОБРАЖАЕМЫХ СТРОК ТАБЛИЦЫ
    foreach ($page_size as $value) {
        if($value == $selsize){
            $return .= "<option selected>". $value ."</option>";
        }
        else{
            $return .= "<option>". $value ."</option>";
        }
    }
    $return .= " </select>
                <ul class='pagination'>";
    for($i=0;$i<($array_count/$selsize);$i++){
        //$array_count - КОЛИЧЕСТВО СТРОК В ТАБЛИЦЕ
        //$selsize - КОЛИЧЕСТВО ОТОБРАЖАЕМЫХ СТРОК
        //$pages - ТЕКУЩАЯ СТРАНИЦА
        $page=$i+1;
        if($i<=2){
            if($page==$pages){
                $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
            else{
                $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
        }
        else if($i+4>=$pages && $i-2<=$pages){
            if($i+4==$pages && $pages !=7){$return .= "...";}
            if($page==$pages){
                $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
            else{
                $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
            if($i-2==$pages && $i+4 < $array_count/$selsize){$return .= "...";}
        }
        else if($i+3>=$array_count/$selsize){
            if($page==$pages){
                $return .= "<li>
                                <input class='btn btn-primary' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
            else{
                $return .= "<li>
                                <input class='btn btn-default' name='page' type='submit' form='page_id' value=$page />
                            </li>";}
        }
    }
    $return .= "</ul>
            </nav>
        ";
    return $return;
}