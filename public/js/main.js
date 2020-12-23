$(document).ready(function () {
    $('.btn_request').click(function () {
        $session = $(this).attr('data-session');
        $day = $(this).attr('weekday');

        if ($session == 1) {
            $('#tiet_day').val(1);
            $('#so_tiet').val(5)
        } else if ($session == 2) {
            $('#tiet_day').val(6);
            $('#so_tiet').val(5)
        } else {
            $('#tiet_day').val(11);
            $('#so_tiet').val(3)
        }


        $('#week_day').val($day);
    })
})
