// var openid = $('#openid').val();
// console.log('openid=' + openid);
$('#usertrip').click(function () {
    $(this).blur();
    $('#placeBox').removeClass('hidden');
});
// $('#userName').bind('input propertychange', function () {
//     $('#tripName').html($(this).val());
// });

//姓名验证
function namevali() {
    var userstr = $('#userName').val();
    var username = userstr.replace(/(^\s+)|(\s+$)/g, "");
    console.log(username);
    if (username.length != 0) {
        if (username.length <= 5) {
            phonevali();
        } else {
            layer.open({
                content: '姓名最多输入五个字符',
                btn: '知道了'
            });
        }

    } else {
        layer.open({
            content: '请输入姓名',
            btn: '知道了'
        });
    }
}

//手机号验证
function phonevali() {
    var phone = $('#userPhone').val();
    var resphone = phone.replace(/(^\s+)|(\s+$)/g, "");
    var mobileReg = /^0?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
    if (resphone.length == 0) {
        layer.open({
            content: '请输入手机号',
            btn: '知道了'
        });
        return false;
    } else if (mobileReg.test(resphone) === false) {
        layer.open({
            content: '手机号格式不正确，请核对后重新输入',
            btn: '知道了'
        });
        return false;
    } else if (mobileReg.test(resphone)) {
        tripvali();
    }
}

//目的地验证
function tripvali() {
    var trip = $('#usertrip').val();
    var restrip = trip.replace(/(^\s+)|(\s+$)/g, "");
    if (restrip.length != 0) {
        submituser();
    } else {
        layer.open({
            content: '请选择您的梦想旅行地',
            btn: '知道了'
        });
        return false;
    }
}

//信息提交
function submituser() {
    var userName = $('#userName').val();
    var userPhone = $('#userPhone').val();
    var userTrip = $('#usertrip').attr('pid');

    $.ajax({
        type: 'POST',
        data: {
            name: userName,
            mobile: userPhone,
            dream_destination_id: userTrip
        },
        dataType: 'JSON',
        url: 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=SaveActUserInfo&m=dq_2017',
        success: function (res) {
            if (res.status == 1) {
                var zuid = res.data.zuid;
                console.log('信息记录成功');
                window.location.href = window.location.href + "&rand=" + Math.random();
            } else {
                layer.open({
                    content: res.msg,
                    btn: '我知道了'
                });
            }
        }
    });
}

//目的地信息渲染
function loadtrip() {
    $.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=DreamDestinationList&m=dq_2017',
        success: function (res) {
            if (res.status == 1) {
                var html = '';
                $.each(res.data, function (i, p) {
                    html += '<p class="dreamtrip" pid="' + p.id + '" pname="' + p.name + '" pic="' + p.pic + '" onclick="mydreamplace(this)">' +
                        p.name + '</p>'
                })
                $('#placeBox').html(html);
            }

        }
    })
}

var plane = 0;
//目的地选择
function mydreamplace(t) {
    plane++;
    $('body').animate({scrollTop: 0}, 500);
    var pname = $(t).attr('pname');
    var pid = $(t).attr('pid');
    var pic = $(t).attr('pic');
    window
    if (pname.length <= 3) {
        $('#tripPlace').css('letter-spacing', '0.5em');
    }else{
        $('#tripPlace').css('letter-spacing', '2px');
    }
    if (plane == 1) {
        $('#planefly').addClass('flyout');
        setTimeout(function () {
            $('#planefly').addClass('hidden');
        },2000);
    }
    // $('#tripPlace').html(pname);
    $('#lgqq').addClass('hidden');
    $('#smqq').addClass('hidden');
    $('#usertrip').val(pname).attr('pid', pid).attr('pname', pname);
    $('#container').css('background-image', 'url("' + pic + '")');
    $('#placeBox').addClass('hidden');

}

window.onload = function () {
    // $('#planefly').addClass('animation fly')
}


