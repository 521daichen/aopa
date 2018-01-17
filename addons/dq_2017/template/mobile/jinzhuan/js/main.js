/**
 * Created by duoduo on 2017/8/20.
 */
var swiper = new Swiper('.swiper-container', {
    direction: 'vertical',
    onlyExternal : true, // 禁止滑动
    observer: true,
    observeParents: true,
    effect : 'fade',
    // mousewheelControl : true,
});
var openid=$('#openid').val();
var way=$('#way').val();
console.log('openid='+openid);

//手机号验证
function phonevali() {
    var phone = $('#userPhone').val();
    var resphone = phone.replace(/(^\s+)|(\s+$)/g,"");
    var mobileReg = /^0?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
    if (resphone.length==0) {
        alert('手机号码不能为空');
        $('#wetips').css('display','block');
        return false;
    } else if(mobileReg.test(resphone) === false) {
       alert('手机号码格式不正确');
        $('#wetips').css('display','block');
        return false;
    }else if(mobileReg.test(resphone)){
        idcardvali();
    }
}
//姓名验证
function  namevali(){
    $('.btnpgthree').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btnsignfocus.png');
    var userstr = $('#userName').val();
    var username = userstr.replace(/(^\s+)|(\s+$)/g,"");
    console.log(username);
    if(username.length==0){
        alert('请输入姓名');
    }else{
        phonevali();
    }
}
//身份证验证
function  idcardvali(){
    var userstr= $('#idCard').val();
    var resuser = userstr.replace(/(^\s+)|(\s+$)/g,"");
    var reg = /(^\d{15}$)|(^\d{17}(\d|X|x)$)/; //(^\d{18}$)
    var years=Number(resuser.slice(6,10));
    if(resuser.length==0){
        alert('请输入身份证号');
    }else if(reg.test(resuser) == false || years<=1915){
        alert("身份证输入格式有误，请核对后重新输入");
    }else if(reg.test(resuser)){
        submituser();
    }
}
//信息提交
function submituser(){
    $("input[name='tips']:checked").val(10);
    var userName=$('#userName').val();
    var userPhone=$('#userPhone').val();
    var idcard=$('#idCard').val();
    var openid=$('#openid').val();
    var way=$('#way').val();
    var check = $("input[name='tips']");
    var tips={};
    var boci=$('#boci').val();
    for(var i=0;i<check.length;i++){
        tips[$(check[i]).attr('id')]=$(check[i]).val();
    }
    console.log(tips);
    $.ajax({
        type:'POST',
        data:{openid:openid,name:userName,mobile:userPhone,idcard:idcard,ADTAG:way,trip:tips.trip,bank:tips.bank,car:tips.car},
        url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=SaveActUserInfo&m=dq_2017',
        success:function(res){
            var resdata=JSON.parse(res);
            if(resdata.status==1){
                console.log('信息记录成功');
                var index = swiper.realIndex;
                var next = index+1;
                swiper.slideTo(next,1000,true);
                getRank(resdata.data);
                var sharedata=resdata.data;
                onMenuShareAppMessage(sharedata);
                onMenuShareTimeline(sharedata);
                document.querySelector('#dq4k').href='http://wx.sagabuy.com/app/index.php?i=4&c=entry&do=prediction4k&m=member&pkid='+openid+'&ADTAG='+way+'&boci='+boci;
            }else if(resdata.status == 0){
                //数据库操作失败
               alert(resdata.msg);
            }
        }
    });
}
function getRank(iddata){
    $.ajax({
        type:'POST',
        data:{zuid:iddata.zuid},
        url:'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanRankingList&m=dq_2017',
        success:function(data){
            var res = JSON.parse(data);
            var barwidth=0;
            var qpleft=0;
            if(res.status==0){
                // 未中奖
                barwidth = (Number(res.weizhongjiangzhanbi)*0.7*100).toFixed(2) ;
                barwidth+="%";
                qpleft = (Number(res.weizhongjiangzhanbi)*0.7*0.7*100).toFixed(2) ;
                qpleft+='%';
                $('#qprank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#cheerCount').html(res.myzan);
                $('#failbarzhanbi').animate({'width':barwidth},'5000');
                $('#succbarzhanbi').css({'width':0});
                $('#qpzhanbi').animate({'margin-left': qpleft},'5000');

            }else if(res.status==1){
                barwidth = (Number(res.zhongjiangzhanbi)*100).toFixed(2) ;
                barwidth+="%";
                qpleft = ((Number(res.zhongjiangzhanbi)*0.3+0.7)*100).toFixed(2) ;
                qpleft+='%';
                $('#qprank').html(res.myrank);
                $('#decrank').html(res.chaoguo);
                $('#cheerCount').html(res.myzan);
                $('#failbarzhanbi').animate({'width':'105%','border-top-left-radius': 0,'border-bottom-left-radius': 0},1800);
                $('#succbarzhanbi').delay(1500).animate({'width':barwidth},850);
                $('#qpzhanbi').animate({'marginLeft': '70%'},1800).animate({'marginLeft': qpleft},850);
            }

        }
    });

};
function share(data){
    var uid= data.shareid;
    var zuid= data.zuid;
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&zuid='+zuid+'&boci='+boci+'&shareid='+uid+'&ADTAG='+way;

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

function onMenuShareTimeline(data) {
    var uid= data.shareid;
    var zuid= data.zuid;
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&zuid='+zuid+'&boci='+boci+'&zuid='+zuid+'&shareid='+uid+'&ADTAG='+way;

    //分享到朋友圈
    wx.onMenuShareTimeline({
        title: '我离两万就差你们一顶', // 分享标题
        link: myLocation, // 分享链接
        imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
        success: function (res) {
            share(data);
        }
    });
}
function onMenuShareAppMessage(data){
    var uid= data.shareid;
    var zuid= data.zuid;
    var way=$('#way').val();
    var boci=$('#boci').val();
    var myLocation = 'http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=ZanEntry&m=dq_2017&zuid='+zuid+'&boci='+boci+'&shareid='+uid+'&ADTAG='+way;

    // 分享给朋友
    wx.onMenuShareAppMessage({
        title: '我离两万就差你一顶', // 分享标题
        link: myLocation, // 分享链接
        imgUrl: 'http://2nd.sagabuy.com/addons/dq_2017/template/mobile/fourYearSrc/images/share.png', // 分享图标
        desc: '赛格两千万等你来瓜分', // 分享描述
        success: function (res) {
            share(data);
        }
    });
}

$('.jumpToNext').click(function(){
    var index = swiper.realIndex;
    var next = index+1;
    swiper.slideTo(next,1000,true);
});
$('#iplay').click(function(){
    $('.btnpgtwo').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btngamefocus.png');
    $('#beforeman').addClass('hidden');
    $('#beforeqp').addClass('hidden');
    $('#afterman').removeClass('hidden').addClass('jump');

    setTimeout(function(){
        var index = swiper.realIndex;
        var next = index+1;
        swiper.slideTo(next,1000,true);
    },800);
});
function next(){
    var index = swiper.realIndex;
    var next = index+1;
    swiper.slideTo(next,1000,true);
}
function preview(){
    var index = swiper.realIndex;
    var next = index-1;
    swiper.slideTo(next,1000,true);
}
$('#rule').click(function(){
    clearTimeout(timer);
   $(this).addClass('stop');
});
$('#showhelp').click(function(){
    $('.btnpgfour').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btnsharefocus.png');
    $('#findhelp').removeClass('hidden');
});
$('#findhelp').click(function(){
    $('.btnpgfour').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btnshare.png');
    $(this).addClass('hidden');
});
$('#btnpgone').click(function(){
   $('.btnpgone').attr('src','../addons/dq_2017/template/mobile/fourYearSrc/images/btnupfocus.png');
   next();
});
wx.ready(function() {
    wx.hideMenuItems({
        menuList: ["menuItem:editTag","menuItem:delete","menuItem:copyUrl","menuItem:originPage", "menuItem:readMode","menuItem:openWithQQBrowser","menuItem:openWithSafari","menuItem:share:email",
            "menuItem:share:qq","menuItem:share:weiboApp","menuItem:favorite","menuItem:share:facebook","menuItem:share:QZone"
        ] // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3
    });
});
