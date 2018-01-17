$('.find').click(function(){
    $('#xp').removeClass('hidden');
});
$('#xp').click(function(){
    $(this).addClass('hidden');
});
function namevali() {
    loading.show();
    var userstr = $('#userName').val();
    var username = userstr.replace(/(^\s+)|(\s+$)/g, "");
    if (username.length != 0) {
        if (username.length <= 5) {
            phonevali();
        } else {
            dialogBtn.setContent('姓名最多输入五个字符');
            dialogBtn.setOperate('知道了', function () {
                dialogBtn.hide();
            });
            loading.hide();
            dialogBtn.show();
        }
    } else {

        dialogBtn.setContent('请输入姓名');
        dialogBtn.setOperate('知道了', function () {
            dialogBtn.hide();
        });
        loading.hide();
        dialogBtn.show();
    }
}

function phonevali() {
    var phone = $('#userPhone').val();
    var resphone = phone.replace(/(^\s+)|(\s+$)/g, "");
    var mobileReg = /^0?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
    if (resphone.length == 0) {
        loading.hide();
        dialogBtn.setContent('请输入手机号');
        dialogBtn.setOperate('知道了', function () {
            dialogBtn.hide();
        });
        dialogBtn.show();
        return false;
    } else if (mobileReg.test(resphone) === false) {
        loading.hide();
        dialogBtn.setContent('手机号格式不正确，请核对后重新输入');
        dialogBtn.setOperate('知道了', function () {
            dialogBtn.hide();
        });
        dialogBtn.show();
        return false;
    } else if (mobileReg.test(resphone)) {
        cashiervali();
    }
}
function cashiervali() {
    var userstr = $('#userCashier').val();
    var userCashier = userstr.replace(/(^\s+)|(\s+$)/g, "");
    if (userCashier.length !=0) {
        transactionvali();
    } else{
        loading.hide();
        dialogBtn.setContent('请输入三位收款台号码');
        dialogBtn.setOperate('知道了', function () {
            dialogBtn.hide();
        });
        dialogBtn.show();
    }
}
function transactionvali() {
    var userstr = $('#userTrans').val();
    var userTrans = userstr.replace(/(^\s+)|(\s+$)/g, "");
    if (userTrans.length != 0) {
        submituser();
        loading.hide();
    } else{
        loading.hide();
        dialogBtn.setContent('请输入小票上的交易号');
        dialogBtn.setOperate('知道了', function () {
            dialogBtn.hide();
        });
        dialogBtn.show();
    }
}
//信息提交
function submituser() {
    var userName = $('#userName').val();
    var userPhone = $('#userPhone').val();
    var userTrans = $('#userTrans').val();
    var userCashier=$('#userCashier').val();
    $.ajax({
        type: 'POST',
        data: {
            name: userName,
            mobile: userPhone,
            transaction:userTrans,
            cashier:userCashier

        },
        dataType: 'JSON',
        url: 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=NewYearSaveActUserInfo&m=dq_2017',
        success: function (res) {
            loading.hide();
            if (res.status == 1) {
                console.log('信息记录成功');
                window.location.href = window.location.href + "&rand=" + Math.random();
            } else {
                dialogBtn.setContent(res.msg);
                dialogBtn.setOperate('知道了', function () {
                    dialogBtn.hide();
                });
                dialogBtn.show();
            }
        }
    });
}
function orshare() {
    var url = window.location.href;
    var sharetitle='2018晒幸福  赢iphonex  满额再送代金券';
    var sharedec = '会员服饰类现金消费满2018元/尊享类现金部分满8018元 晒购物小票参与线上活动，分享至朋友圈，即送50元全馆通用券，更有机会抽取iPhoneX!';
    wx.onMenuShareAppMessage({
        title: sharetitle, // 分享标题
        link: url, // 分享链接
        imgUrl: 'http://mswechat.mebaby.cn/addons/dq_2017/template/mobile/newyear/images/share.png', // 分享图标
        desc: sharedec, // 分享描述
        success: function (res) {
            console.log('成功分享到朋友');
        }

    });
    wx.onMenuShareTimeline({
        title: sharetitle, // 分享标题
        link: url, // 分享链接
        imgUrl: 'http://mswechat.mebaby.cn/addons/dq_2017/template/mobile/newyear/images/share.png', // 分享图标
        success: function (res) {
            console.log('成功分享到朋友圈');
        }
    });
}

wx.ready(function () {
    wx.hideMenuItems({
        menuList: ["menuItem:editTag", "menuItem:delete", "menuItem:copyUrl", "menuItem:originPage", "menuItem:readMode", "menuItem:openWithQQBrowser", "menuItem:openWithSafari", "menuItem:share:email",
            "menuItem:share:qq", "menuItem:share:weiboApp", "menuItem:favorite", "menuItem:share:facebook", "menuItem:share:QZone"
        ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });
    orshare();
});