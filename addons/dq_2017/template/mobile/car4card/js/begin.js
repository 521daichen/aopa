//车牌输入框
function cpadd(a) {
    //省份及字母点击事件
    if (a == '陕') {
        $('.zimuA').css('display', 'none');
    }
    Cts = $("#cpvalue").val();
    if (Cts.indexOf(a) > 0) {
    } else {
        if (Cts.length > 0) {
            $("#cpvalue").val(Cts + a);
            $("#zimu").css("display", "none");
            $("#num").css("display", "block");
            $('#num').focus();

        } else {
            $("#cpvalue").val(a);
            $("#sheng").css("display", "none");
            $("#zimu").css("display", "block");
        }
    }
}

function cp_noreg() {
    // 取消输入点击事件
    $("#cp").addClass('opxs');
    $(".proys").addClass('onexit');
    setTimeout(function () {
        $("#cp").removeClass('prompt');
        $("#cp").addClass('d_n');
        $("#cp").removeClass('opxs');
        $(".proys").removeClass("onexit");
    }, 350);
    $('#cpvalue').val('');
    $('#num').val('');
    $('#sheng').css('display', 'block');
    $('.zimuA').css('display', 'block');
    $('#zimu').css('display', 'none');
    $('#num').css('display', 'none')
}

function toupper() {
    var num = $('#num').val();
    $('#num').val(num.toUpperCase()); //转换大小写
}

function cp_n() {
    // 确定绑定
    var numlength = $("#num").val();
    var reg = /^([A-Z0-9]{5})|([A-Z0-9]{4}[\u4e00-\u9fa5]{1}$)/;
    if (reg.test(numlength) && numlength.length == 5) {
        var platenumber = $("#cpvalue").val() + " " + numlength;
        $('#userCar').val(platenumber);
        $('#cp').removeClass('prompt');
        cardplate = 1;
    } else {
        alert("车牌号须为字母或数字(5位)");
    }

}

//车牌输入框结束


