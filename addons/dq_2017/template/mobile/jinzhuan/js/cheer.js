/**
 * Created by duoduo on 2017/8/11.
 */
var swiper = new Swiper('.swiper-container', {
    direction: 'vertical',
    onlyExternal : true,   //禁止滑动
    observer: true,
    observeParents: true,
    // mousewheelControl : true,
    effect : 'fade',
});
$('.jumpToNext').click(function(){
    var index = swiper.realIndex;
    var next = index+1;
    swiper.slideTo(next,1000,false);
});
function next(){
    var index = swiper.realIndex;
    var next = index+1;
    swiper.slideTo(next,1000,false);
}
$('#helpcheer').click(function (){
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
                next();
                getRank();
                $('#helpcheer').addClass('grayscale');
            }else{
                layer.open({
                    content: '您已投票成功',
                    btn: '知道了'
                });
                next();
                getRank();
                $('#helpcheer').addClass('grayscale');
            }
            //
            //
            // if(res.status==0){
            //     next();
            //     getRank();
            // }else{
            //     next();
            //     getRank();
            // }
        }
    })
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
                // barwidth = (Number(res.weizhongjiangzhanbi)*0.7*100).toFixed(2) ;
                // barwidth+="%";
                // qpleft = (Number(res.weizhongjiangzhanbi)*0.7*0.7*100).toFixed(2) ;
                // qpleft+='%';
                // $('#qprank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#cheerCount').html(res.myzan);
                // $('#failbarzhanbi').animate({'width':barwidth},'5000');
                // $('#succbarzhanbi').css({'width':0});
                // $('#qpzhanbi').animate({'margin-left': qpleft},'5000');

            }else if(res.status==1){
                // barwidth = (Number(res.zhongjiangzhanbi)*100).toFixed(2) ;
                // barwidth+="%";
                // qpleft = ((Number(res.zhongjiangzhanbi)*0.3+0.7)*100).toFixed(2) ;
                // qpleft+='%';
                // $('#qprank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#cheerCount').html(res.myzan);
                // $('#failbarzhanbi').animate({'width':'105%','border-top-left-radius': 0,'border-bottom-left-radius': 0},1800);
                // $('#succbarzhanbi').delay(1500).animate({'width':barwidth},850);
                // $('#qpzhanbi').animate({'marginLeft': '70%'},1800).animate({'marginLeft': qpleft},850);
            }

        }
    });
};
// function share(){
//     var uid= $('#uid').val();
//     var zuid= $('#zuid').val();
//     var way=$('#way').val();
//     var boci=$('#boci').val();
//     var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;
//
//     $.ajax({
//         type:'POST',
//         data:{zuid:zuid,url:myLocation},
//         url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanShareUrl&m=dq_2017',
//         success:function(data){
//             var res = JSON.parse(data);
//             console.log(res);
//             if(res.status==0){
//                 console.log(res.message);
//             }else{
//                 console.log(res.message);
//             }
//         }
//     })
// }

// function onMenuShareTimeline() {
//     var uid= $('#uid').val();
//     var zuid= $('#zuid').val();
//     var way=$('#way').val();
//     var boci=$('#boci').val();
//     var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;
//
//     //分享到朋友圈
//     wx.onMenuShareTimeline({
//         title: '我离两万就差你们一顶', // 分享标题
//         link: myLocation, // 分享链接
//         imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
//         success: function (res) {
//             share();
//         }
//     });
// }
// function onMenuShareAppMessage(){
//     var uid= $('#uid').val();
//     var zuid= $('#zuid').val();
//     var way=$('#way').val();
//     var boci=$('#boci').val();
//     var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;
//
//     // 分享给朋友
//     wx.onMenuShareAppMessage({
//         title: '我离两万就差你一顶', // 分享标题
//         link: myLocation, // 分享链接s
//         imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
//         desc: '赛格两千万等你来瓜分', // 分享描述
//         success: function (res) {
//             share();
//         }
//     });
// }
wx.ready(function(){
    // onMenuShareAppMessage();
    // onMenuShareTimeline();
    wx.hideMenuItems({
        menuList: ["menuItem:editTag","menuItem:delete","menuItem:copyUrl","menuItem:originPage", "menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email",
            "menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone"
        ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });
});
