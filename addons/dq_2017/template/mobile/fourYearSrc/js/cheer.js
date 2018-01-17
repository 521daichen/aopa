/**
 * Created by duoduo on 2017/8/11.
 */
window.onload= function(){
    getRank();
    setInterval(gettime,1000);
};
function plus(recount){
    var count=recount;
    var sp= count.toString().split("");
    while(sp.length<6){
        sp.unshift('0');
    }
    console.log(sp);
    var swan=Number(sp[0])
    var wan = Number(sp[1]);
    var qian = Number(sp[2]);
    var bai = Number(sp[3]);
    var shi = Number(sp[4]);
    var gei = Number(sp[5]);
    var num0=0;
    var num1=0;
    var num2=0;
    var num3=0;
    var num4=0;
    var num5=0;
    var t5;
    var t0;
    var t1;
    var t2;
    var t3;
    var t4;
    if(swan>0){
        t5 = setInterval(function(){
            num5 ++;
            $('#swan').html(num0);
            if(num5===swan) {
                clearInterval(t5);
            }
        },270);
    }else{
        $('#swan').html(0);
    }
    if(wan>0){
        t0 = setInterval(function(){
            num0 ++;
            $('#wan').html(num0);
            if(num0===wan) {
                clearInterval(t0);
            }
        },220);
    }else{
        $('#wan').html(0);
    }
    if(qian>0){
        t1 = setInterval(function(){
            num1++;
            $('#qian').html(num1);
            if(num1===qian) {
                clearInterval(t1);

            }
        },170);
    }else{
        $('#qian').html(0);
    }
    if(bai>0){
        t2 = setInterval(function(){
            num2++;
            $('#bai').html(num2);
            if(num2===bai) {
                clearInterval(t2);
            }
        },120);
    }else{
        $('#bai').html(0);
    }
    if(shi>0){
        t3 = setInterval(function(){
            num3++;
            $('#shi').html(num3);
            if(num3===shi) {
                clearInterval(t3);
            }
        },70);
    }else{
        $('#shi').html(0);
    }
    if(gei!==0){
        t4 = setInterval(function(){
            num4++;
            $('#gei').html(num4);
            if(num4===gei) {
                clearInterval(t4);
            }
        },20);
    }else{
        $('#gei').html(0);
    }
}
function gettime(){
    // var EndTime= new Date('2017/09/07 18:00:00');
    var EndTime= new Date('2017/09/14 18:00:00');
    // var EndTime= new Date('2017/09/21 18:00:00');
    // var EndTime= new Date('2017/09/28 18:00:00');
    var NowTime = new Date();
    var t =EndTime.getTime() - NowTime.getTime();
    var d=0;
    var h=0;
    var m=0;
    var s=0;
    if(t>=0){
        d=Math.floor(t/1000/60/60/24);
        h=Math.floor(t/1000/60/60%24);
        m=Math.floor(t/1000/60%60);
        s=Math.floor(t/1000%60);
    }
    $('#day').html(d);
    if(h<10){
        $('#hour').html('0'+h);
    }else{
        $('#hour').html(h);
    }
    if(m<10){
        $('#min').html('0'+m);
    }else{
        $('#min').html(m);
    }
    if(s<10){
        $('#sec').html('0'+s);
    }else{
        $('#sec').html(s);
    }
}
$('#helpcheer').one('click',function (){
    $('.btnhelpone').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btngamefocus.png')
    var shareid=$('#shareid').val();
    var zuid=$('#zuid').val();
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+shareid+'&ADTAG='+way;
    var count = Number($('#cheerCount').html());
    $.ajax({
        type:'POST',
        data:{zuid:zuid,url:myLocation},
        url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanFun&m=dq_2017',
        success:function(data){
            var res = JSON.parse(data);
            console.log(res);
            if(res.status==0){
                layer.open({
                    content: '您已经投过票啦',
                    btn: '知道了'
                });
                $('#helpcheer').addClass('grayscale');
            }else{
                layer.open({
                    content: '您已投票成功，稍后票数会自动增加',
                    btn: '知道了'
                });
                getRank();
                $('#helpcheer').addClass('grayscale');
            }
        }
    })
});
$('#iosknow').click(function(){
    $('#ios').addClass('hidden');
    getRank();
});
function getRank(){
    var zuid = $('#zuid').val();
    $.ajax({
        type:'POST',
        data:{zuid:zuid},
        url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanRankingList&m=dq_2017',
        success:function(data){
            var res = JSON.parse(data);
            if(res.status==0){
                // 未中奖
                plus(res.myzan);
                barwidth = (Number(res.weizhongjiangzhanbi)*0.8*100).toFixed(2) ;
                if(barwidth<5){
                    barwidth = 5
                }
                barwidth+="%";
                $('#myrank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#card').animate({'width':barwidth},'5000');

            }else if(res.status==1){
                plus(res.myzan);
                barwidth = (Number(res.zhongjiangzhanbi)*0.8*100).toFixed(2) ;
                if(barwidth<5){
                    barwidth = 5
                }
                barwidth+="%";
                $('#myrank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#card').animate({'width':barwidth},'5000');
            }

        }
    });
};
function share(){
    var uid= $('#uid').val();
    var zuid= $('#zuid').val();
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;

    $.ajax({
        type:'POST',
        data:{zuid:zuid,url:myLocation},
        url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanShareUrl&m=dq_2017',
        success:function(data){
            var res = JSON.parse(data);
            console.log(res);
            if(res.status==0){
                console.log(res.message);
            }else{
                console.log(res.message);
            }
        }
    })
}

function onMenuShareTimeline() {
    var uid= $('#uid').val();
    var zuid= $('#zuid').val();
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;

    //分享到朋友圈
    wx.onMenuShareTimeline({
        title: '赛格四周年 真的感恩 真送两万 我离两万就差你一票', // 分享标题
        link: myLocation, // 分享链接
        imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
        success: function (res) {
            share();
        }
    });
}
function onMenuShareAppMessage(){
    var uid= $('#uid').val();
    var zuid= $('#zuid').val();
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;

    // 分享给朋友
    wx.onMenuShareAppMessage({
        title: '赛格四周年 真的感恩 真送两万 我离两万就差你一票', // 分享标题
        link: myLocation, // 分享链接
        imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
        desc: '第二波活动已开启，200份2万元购物卡有我一份，就差你一票！', // 分享描述
        success: function (res) {
            share();
        }
    });
}
wx.ready(function(){
    onMenuShareAppMessage();
    onMenuShareTimeline();
    wx.hideMenuItems({
        menuList: ["menuItem:editTag","menuItem:delete","menuItem:copyUrl","menuItem:originPage", "menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email",
            "menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone"
        ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });
});
