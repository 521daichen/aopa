function showshare(){
    $('#shareto').removeClass('hidden');
}
$('#shareto').click(function(){
    $(this).addClass('hidden');
});
function sendcard(){
    $.ajax({
        type: 'GET',
        dataType: 'JSON',
        url: 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=NewYearCardSignInfo&m=dq_2017',
        success: function (res) {
            if(res.status==1){
                wx.addCard({
                    cardList: [
                        {
                            cardId:"pk_mF1mbrcKJ_VMhdTbSY_Az8H_Y", //cardExt:"{'timestamp':'{$wxtimestamp}','signature':'{$wxsignature}'}"
                            cardExt:'{"code": "","openid":"", "timestamp": "'+ res.timestamp+ '", "nonce_str":"'+ res.nonce_str+ '","signature":"' +res.signature +'","outer_id": 1}'                            //cardExt: cardListStr
                        }
                    ], // 需要添加的卡券列表
                    success: function (res) {
                        var cardList = res.cardList;
                        window.location.href = window.location.href + "&rand=" + Math.random();

                    },
                    cancel:function(res){
                    },
                    fail:function(res){
                    }
                });
            }else{
                dialogBtn.setContent(res.msg);
                dialogBtn.setOperate('知道了', function () {
                    dialogBtn.hide();
                });
                dialogBtn.show();
            }
        }
    })
}
function orshare() {
    var url = 'http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=NewYearEntry&m=dq_2017';
    var sharetitle='2018晒幸福  赢iphonex  满额再送代金券!';
    var sharedec = '会员服饰类现金消费满2018元/尊享类现金部分满8018元 晒购物小票参与线上活动，分享至朋友圈，即送50元全馆通用券，更有机会抽取iPhoneX!';
    wx.onMenuShareAppMessage({
        title: sharetitle, // 分享标题
        link: url, // 分享链接
        imgUrl: 'http://mswechat.mebaby.cn/addons/dq_2017/template/mobile/newyear/images/share.png', // 分享图标
        desc: sharedec, // 分享描述
        success: function (res) {
            $('#shareto').addClass('hidden');
            $('#sharesucc').removeClass('hidden');
        }

    });
    wx.onMenuShareTimeline({
        title: sharetitle, // 分享标题
        link: url, // 分享链接
        imgUrl: 'http://mswechat.mebaby.cn/addons/dq_2017/template/mobile/newyear/images/share.png', // 分享图标
        success: function (res) {
            $('#shareto').addClass('hidden');
            $('#sharesucc').removeClass('hidden');
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