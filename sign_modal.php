<?php

?>
<div class="modal fade" id="signModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action='check_sign.php' id="idf" method='post'>
            </form>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Авторизация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input form="idf" type="text" class="form-control"
                           placeholder="Логин"
                           name="login"
                           value=""
                           aria-label="Логин"
                           required>
                    <span class="input-group-text"></span>
                    <input form="idf" type="text" class="form-control"
                           placeholder="Пароль"
                           name="password"
                           value=""
                           aria-label="Пароль"
                           required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <input form="idf" type="submit" class="btn btn-primary" value="Войти"/>
            </div>
        </div>
    </div>
</div>

