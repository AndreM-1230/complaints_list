<?php
session_start();
if($_SESSION['account'] == null){
    $value = 'Войти';
    $action = 'check_sign.php';
    $text = 'Авторизация';
}
else{
    $value = 'Отправить';
    $action = 'new_complaint.php';
    $text = 'Новая жалоба';
}
echo "<div class='modal fade'
     id='my_personal_form'
     tabindex='-1'
     data-bs-backdrop='static'
     data-bs-keyboard='false'
     aria-labelledby='exampleModalLabel'
     aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <form action= $action id='idf' method='post'>
            </form>
            <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalLabel'>". $text ."</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Закрыть'></button>
            </div>
            <div class='modal-body'>";

            if($_SESSION['account'] == null){
                echo "<div class='input-group mb-3'>
                    <input form='idf' type='text' class='form-control'
                           placeholder='Логин'
                           name='login'
                           value=''
                           aria-label='Логин'
                           required>
                    <span class='input-group-text'></span>
                    <input form='idf' type='text' class='form-control'
                           placeholder='Пароль'
                           name = 'password' value='' aria-label='Пароль' required>
                </div>";
            }
            else{
                echo "<div class='form-group'>
                        <label for='exampleFormControlTextarea1'>Введи текст:</label>
                        <textarea maxlength='200' class='form-control' form='idf' name='text' id='exampleFormControlTextarea1' rows='5' required></textarea>
                    </div>";
            }

            echo "</div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Закрыть</button>
                <input form='idf' type='submit' class='btn btn-primary' value= $value />
            </div>
        </div>
    </div>
</div>";