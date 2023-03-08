function com_sel(com_sel) {
    location.href = './select_complaints_type.php?com_sel='+ com_sel;
    console.log(com_sel);
}

function comment_form(data){
    let data_id = $(data).attr('id');
    let text = "<div class='modal fade show'\n" +
        "     id='my_comment_form'\n" +
        "     tabindex='-1'\n" +
        "     data-bs-backdrop='static'\n" +
        "     data-bs-keyboard='false'\n" +
        "     aria-labelledby='exampleModalLabel'\n" +
        "     aria-modal='true'\n" +
        "     role='dialog'\n" +
        "     style='display: block'>\n" +
        "    <div class='modal-dialog'>\n" +
        "        <div class='modal-content'>\n" +
        "            <form action='comment.php' id='idfc' method='post'>\n" +
        "            </form>\n" +
        "            <div class='modal-header'>\n" +
        "                <h5 class='modal-title' id='exampleModalLabel'>Ответ на жалобу №" +  data_id + "</h5>\n" +
        "                <button type='button' class='btn-close' onclick='del_modal()'></button>\n" +
        "            </div>\n" +
        "            <div class='modal-body'>" +
        "            <div class='form-group'>\n" +
        "                        <label for='exampleFormControlTextarea1'>Введи текст:</label>\n" +
        "                        <textarea class='form-control' form='idfc' name='text' id='exampleFormControlTextarea1' rows='5' required></textarea>\n" +
        "                    </div></div>\n" +
        "            <div class='modal-footer'>\n" +
        "                <button type='button' class='btn btn-secondary' onclick='del_modal()'>Закрыть</button>\n" +
        "                <input form='idfc' type='text' name='data_id' class='form-control' value=" + data_id + " hidden />\n" +
        "                <input form='idfc' type='submit' class='btn btn-primary' value= 'Отправить' />\n" +
        "            </div>\n" +
        "        </div>\n" +
        "    </div>\n" +
        "</div>"
    $(text).prependTo('body');
}

function del_modal(){
    $( "#my_comment_form" ).remove();
}