/**
 * Created by duoduo on 2017/8/10.
 */
//页面
$('#sharebtn').click(function () {
    $('#share').removeClass('hidden')
});
$('#redNo').click(function () {
    $('#hongbaoNo').addClass('hidden');
});
$('#share').click(function () {
    $(this).addClass('hidden');
});
function flyevl(o){
    $(o).addClass('hidden');
    $('#boarding').addClass('slidetop');
    $('#boardingCheck').addClass('bectop');
    $('#evlbotm,#evltop').fadeOut();
}
window.onload = function () {
    var pname = $('#tripPlace').html();
    if (pname.length <= 3) {
        $('#tripPlace').css('letter-spacing', '0.5em');
    }else{
        $('#tripPlace').css('letter-spacing', '2px');
    }
}
$('#close').click(function(){
    $('#hongbao').addClass('hidden');
})
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
        $('#hongbao').addClass('hidden');
        $.ajax({
            type: 'POST',
            data: {
                mobile: resphone
            },
            dataType: 'JSON',
            url: 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=UpdateMobileByOpenId&m=dq_2017&zuid=9',
            success: function (res) {
                if (res.status == 1) {
                    console.log('信息记录成功');
                    layer.open({
                        content: '提交成功 <br/>请添加个人微信号“CWAGchang”发送“领奖+姓名+电话” <br/>继续邀请好友赢取免费往返机票',
                        btn: '我知道了'
                    });
                } else {
                    layer.open({
                        content: res.msg,
                        btn: '我知道了'
                    });
                }
            }
        });
    }
}
