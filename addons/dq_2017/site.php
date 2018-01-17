<?php
/**
 * 店庆模块微站定义
 *
 * @author 橙橙同学
 * @url 
 */

defined('IN_IA') or exit('Access Denied');
class Dq_2017ModuleSite extends WeModuleSite {


    //拉卡券begin

    /**
     * 获得 api_ticket
     * daichen
     */
    public function doMobileGetApiTicket(){
        global $_W;
        //取缓存
        $dc_ticket=cache_load('dc_api_ticket');
        $cache_ticket = !empty($dc_ticket['exp'])?$dc_ticket['exp']:0;
        if($cache_ticket<time()){
            $access_token=$this->getAccessToken($_W['uniacid']);
            $userinfo = ihttp_get("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=wx_card");
            $ticketJson=$userinfo['content'];
            $ticketArr=json_decode($ticketJson,true);
            $ticket=$ticketArr['ticket'];
            //缓存时间为当前时间加7000秒  实际为7200秒
            $cacheTime=time()+6000;
            $cacheTicket=array(
                'ticket'=>$ticket,
                'exp'=>$cacheTime,
            );
            cache_write('dc_api_ticket', $cacheTicket);
            return $cacheTicket['ticket'];
        }
        return $dc_ticket['ticket'];
    }


    /**
     * 获取随机字符串 dc
     * daichen
     */
    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }


    /**
     * 获取addcard配置
     * daichen
     */
    public function doMobileGetCardSignInfo($cardId){
        global $_W;
        $timestamp=$_W['timestamp'];
        $api_ticket=$this->doMobileGetApiTicket();

//        $card_id=htmlspecialchars($_POST['cardId']);

        $card_id = $cardId;
        $nonce_str=$this->generateNonceStr();
        $card = array(
            $timestamp,
            $api_ticket,
            $card_id,
            $nonce_str
        );
        sort($card,SORT_STRING);
        $return='';
        foreach($card as $k=>$v){
            $return.= $v;
        }
        $sign=sha1($return);
        $res=array(
            'timestamp'=>$timestamp,
            'signature'=>$sign,
            'nonce_str'=>$nonce_str,
        );
        return $res;
//        echo json_encode($res);
    }

    public function doMobileThrowCardByJs()
    {
        global $_W,$_GPC;
        $cardId = $_GPC['cardid'];
        if(!empty($cardId)){

            $cardId = pdo_fetchcolumn('select card_id from '.tablename('wechatcard_cardlist').'where `id`=:id and `uniacid`=:uniacid',
                array(
                    ':id'=>$cardId,
                    ':uniacid'=>$_W['uniacid']
                ));

            $cardSignInfo = $this->doMobileGetCardSignInfo($cardId);
            include $this->template('js_card/membercard');
        }else{
            $this->commonTips('请求错误','','info');
        }
    }


    //拉卡券end















    /**
     * 发送模板消息
     *
     * @param $data
     *
     * @return bool
     */
    public  function send($data)
    {

        $token   = $this->getAccessToken(2);

        $url     = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $token;
        $request = $this->http_post($url, $data);
        $req     = json_decode($request, true);
        if ($req['errcode'] == 0 && $req['errmsg'] == 'ok') {
            echo '发送成功'.json_encode($data);
        } else {
            echo '发送失败'.json_encode($data);
        }
    }

    public function doMobileSendOpenCardInfo()
    {
        $user = array(
            'openid'=>'ok_mF1iGbKqPETS8rSBH09gnBsOM'
        );
        $this->sendWxMessageOpenCard($user);
    }



    public function sendWxMessageOpenCard($user)
    {
        global $_W;
        $openid = $user['openid'];
        $msg = "咨询电话： 029-87481826\n\n";
        $data = array(
            'touser'      => $openid,
            'template_id' => 'CJtvxg1dnhtiBowHS5TDXRTR63Kn0BolO3ytpuO5I6w',
            'url'         => $_W['siteroot'].'app/index.php?i=2&c=entry&do=GetWechatCard&m=orange_member&cardid=1',
            'topcolor'    => '#69008C',
            'data'        => array(
                'first'  => array(
                    'value' => "邀请您开通微信会员卡：\n",
                    'color' => '#000000'
                ),
                'keyword1'   => array(
                    'value' => '民生百货会员卡',
                    'color' => '#69008C'
                ),
                'keyword2'    => array(
                    'value' => 'pk_mF1uf7O1ALWKDIRsMwxgEHeyA',
                    'color' => '#69008C'
                ),
                'remark' => array(
                    'value' => $msg,
                    'color' => '#000000'
                ),
            )
        );
        $this->send(json_encode($data));
    }



    function http_post($url,$param){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }


    public function sendWxMessage($pricename,$user)
    {
        global $_W;
        $openid = $user['openid'];
        $msg = "咨询电话： 029-87481826\n\n";
        $data = array(
            'touser'      => $openid,
            'template_id' => 'guLuv36qS6xazxxFvK7CF0qsy4TpU3ndsDsShq4dWF8',
            'url'         => '',
            'topcolor'    => '#69008C',
            'data'        => array(
                'first'  => array(
                    'value' => "尊敬的{$user['name']}您好，恭喜您在《晒幸福》活动中中奖，领奖信息如下：\n",
                    'color' => '#000000'
                ),
                'keyword1'   => array(
                    'value' => $pricename,
                    'color' => '#69008C'
                ),
                'keyword2'    => array(
                    'value' => '民生百货解放路店4F会员中心',
                    'color' => '#69008C'
                ),
                'keyword3'   => array(
                    'value' => '2018年1月9日 至 2018年1月14日',
                    'color' => '#69008C'
                ),
                'remark' => array(
                    'value' => $msg,
                    'color' => '#000000'
                ),
            )
        );
        $this->send(json_encode($data));
    }



    public function doMobileSendData()
    {
//        $arr1 = pdo_fetchall('select * from ims_newyear_userinfo where `mobile` in (
//            "18700581009",
//            "13571918539",
//            "15399045908",
//            "13891375361",
//            "15829063574",
//            "13659191776",
//            "18629236333",
//            "13759900209",
//            "15202966482",
//            "18591997587"
//        );');

//
//        $arr1 = pdo_fetchall('select * from ims_newyear_userinfo where `mobile` in (
//            "13186184263"
//    );');

//        foreach ($arr1 as $value)
//        {
//            $this->sendWxMessage('美迪惠尔面膜',$value);
//        }
//
//        $arr2 = pdo_fetchall('select * from ims_newyear_userinfo where `mobile` in (
//               "13991116650",
//                "18629096595",
//                "18740496960",
//                "13679216448",
//                "13347423965",
//                "18829239085",
//                "18792437100",
//                "13186085597",
//                "13659143092",
//                "18646460766"
//        );');
//
//        foreach ($arr2 as $value)
//        {
//            $this->sendWxMessage('仿真狗狗抱枕',$value);
//        }
//
        $arr3 = pdo_fetchall('select * from ims_newyear_userinfo where `mobile` in (
        "15029218562",
        "18192918053",
        "15102983817",
        "13186017963",
        "13991115971",
        "18409271456"
        );');


        foreach ($arr3 as $value)
        {
            $this->sendWxMessage('花田巷子酒一对',$value);
        }

//        $arr4 = pdo_fetchall('select * from ims_newyear_userinfo where `mobile` in (
//          "15353527896"
//        );');
//
//        foreach ($arr1 as $value)
//        {
//            $this->sendWxMessage('iPhone X一部',$value);
//        }



    }




    public function doMobileVoice()
    {
        global $_W;
        include $this->template('voice/voice');

    }







    public function ZanFilter(){
        //浏览器客户端过滤
        global $_W;
        $openid =$_W['openid'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 非微信浏览器禁止浏览
            echo "请在微信中打开本页面";
//            include $this->template('fourYears2w/wechat');
            exit();
        }
    }
    public function doMobileActRule(){
        include $this->template('fourYears2w/rule');
    }

    public function doMobileerrPage(){
        include $this->template('fourYears2w/error');
    }


    /**
     * 取汉字的第一个字的首字母
     * @param type $str
     * @return string|null
     */
    public function _getFirstCharter($str){
        if(empty($str)){return '';}
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }

    public function commonApiCheck()
    {
        global $_W;
        $openid =$_W['openid'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            echo json_encode(array('status'=>-1,'message'=>'非法请求'));
            exit();
        }
    }

    //结束
    public function doMobileResultone(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        $frequency = $this->doMobilefindfrequency();
        if($frequency > 1){
            $userInfo = pdo_fetchall("SELECT openid,name,mobile,idNum FROM ims_dq_zan_lucker_1 ORDER BY convert(name using gbk) ASC");
            //获得我的信息
            $myinfo = pdo_fetch("SELECT openid,name,mobile,idNum,zcount FROM ims_dq_2wuserinfo_1 Where openid='" . $openid . "'");
            if(!empty($myinfo)) {
                //如果我的信息不为空  那么获得我的赞
                //设置isluck 判断我是否中奖
                $userzcount = $myinfo['zcount'];
                unset($myinfo['zcount']);
                //判断我是否在所有中奖的人列表
                $luck = in_array($myinfo, $userInfo);
                if ($luck) {
                    $isluck = 1;
                } else {
                    $isluck = 0;
                }
            }else{
                //999未参与
                //如果没有参与那么设置isluck 999
                $isluck = '999';
            }
            if(is_array($userInfo)){
                foreach ($userInfo as $key=>$value){
                    $user[$key+1]['mobile'] = substr_replace($value['mobile'],'****',3,4);
                    $user[$key+1]['name'] = mb_substr($value['name'], 0, 1) . '*' . mb_substr($value['name'], 2);
                    $len = strlen($value['name'])/3;
                    if($len>2){
                        $user[$key+1]['name'] = mb_substr($value['name'], 0, 1) . '**';
                    }else{
                        $user[$key+1]['name'] = mb_substr($value['name'], 0, 1) . '*';
                    }
                    $user[$key+1]['place'] = getplace($value['idNum']);
                }
            }
            include $this->template('fourYears2w/resultone');
        }else{
            include $this->template('fourYears2w/resultall');
        }
    }
    /**
     * 入口
     */
    public function doMobilefourYears2w()
    {
        //过滤
        $this->ZanFilter();
        global  $_W;
        $openid = $_W['openid'];
        //sql优化：const型
        $userId = pdo_fetch("SELECT id FROM ims_dq_2wuserinfo Where openid='".$openid."'");
        if(empty($userId)){

            //总数 计算第多少名乘客
            $count = pdo_fetch("select count(*) as c from ims_dq_2wuserinfo");
            $dreams = $this->doMobileDreamDestinationList();

            include $this->template('fourYears2w/dq2017');
        }else{
            header('location:http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=zanentry&m=dq_2017&zuid='.$userId['id']);
        }
    }

    public function doMobileDreamDestinationList()
    {
        $dream_des_list = pdo_fetchall("select * from ".tablename('dq_dreamdestination')." order by name asc ");

        foreach ($dream_des_list as $dream) {

            $settlesRes[$dream['desc']][] = $dream;
        }
        return $settlesRes;
    }

    public function doMobileUpdateMobileByOpenId()
    {
        $this->commonApiCheck();
        global $_W,$_GPC;
        $openid = $_W['openid'];
        $mobile = $_GPC['mobile'];
        if(!empty($mobile)){
            $data = array(
              'mobile'=>$mobile
            );
            $res = pdo_update('dq_2wuserinfo',$data,array('openid'=>$openid));
            if($res)
            {
                echo json_encode(array('status' => 1, 'msg' => '提交成功'));
            }
            else{
                echo json_encode(array('status' => -1, 'msg' => '更新失败请联系客服'));
            }
        }
        else{
            echo json_encode(array('status' => -1, 'msg' => '参数不能为空'));
        }

    }


    /**
     * @author: 橙橙同学
     * @fun:梦想目的地列表
     */
    public function doMobileDreamDestinationListBK()
    {
        $dream_des_list = pdo_fetchall("select * from ".tablename('dq_dreamdestination'));

        foreach ($dream_des_list as $dream) {
            $nameFirstChar = $this->_getFirstCharter($dream['name']);

            $arr1 = array('A','B','C','D','E','F','G');
            $arr2 = array('H','I','J','K','L','M','N');
            $arr3 = array('O','P','Q','R','S','T');
            $arr4 = array('U','V','W','X','Y','Z');

            if(in_array($nameFirstChar,$arr1)){
                $settlesRes['A-G'][] = $dream;
            }

            if(in_array($nameFirstChar,$arr2)){
                $settlesRes['H-N'][] = $dream;
            }

            if(in_array($nameFirstChar,$arr3)){
                $settlesRes['O-T'][] = $dream;
            }

            if(in_array($nameFirstChar,$arr4)){
                $settlesRes['U-Z'][] = $dream;
            }
        }
        ksort($settlesRes);
        return $settlesRes;


        //echo json_encode(array('status' => 1,'data'=>$settlesRes,'msg' => '获取梦想目的地列表成功'));
    }

    protected function getSeats()
    {
        $arr1 = array('A','B','C','D','E');
        $rand = rand(0,4);
        $randNumber = rand(1,5);
        return '0'.$randNumber.$arr1[$rand];

    }

    /**
     * 收集信息
     */
    public function doMobileSaveActUserInfo(){
        $this->commonApiCheck();

        global $_W,$_GPC;
        $openid = $_W['openid'];
        //sql优化：ref
        if(isset($_GPC['dream_destination_id']) && isset($_GPC['name']) &&isset($_GPC['mobile'])){

            $name = htmlspecialchars($_GPC['name']);
//            $mobile = htmlspecialchars($_GPC['mobile']);
//            $dream_destination = htmlspecialchars($_GPC['dream_destination']);
            $dream_destination_id = $_GPC['dream_destination_id'];
            $mobile = htmlspecialchars($_GPC['mobile']);

            $dreamInfo = pdo_fetch("select * from ims_dq_dreamdestination where id=".$dream_destination_id);

            if($dreamInfo['desc'] == '国际')
            {
                $rand = rand(13,19);
                $planGate = 'F'.$rand;
            }else{
                $randPre = array('H','N');
                $randKey = rand(0,1);
                $rand = rand(1,20);

                if($rand<10)
                {
                    $rand = '0'.$rand;
                }
                $planGate = $randPre[$randKey].$rand;
            }

            $seat = $this->getSeats();



            $userId = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where openid='" . $openid . "'");

            if (!empty($userId)) {
                echo json_encode(array('status' => -1, 'msg' => '您已经提交过梦想目的地，请勿重复提交'));
            }else{
//                $isReg = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where mobile='" . $mobile . "'");
//                if($isReg)
//                {
//                    echo json_encode(array('status' => -1, 'msg' => '您的手机号已经注册，请勿重复提交'));
//                }
//                else{
                    $data = array(
                        'openid' => $openid,
                        'dreamdestination'=>$dream_destination_id,
//                        'dreamdestination'=>
                        'ip' => $this->get_client_ip(),
                        'create_time' => time(),
                        'mobile' => $mobile,
                        'name' => $name,
                        //status用来做触发抽奖事件标记
                        'status'=>0,
                        'plangate'=>$planGate,
                        'seat'=>$seat,

                    );
                    pdo_insert('dq_2wuserinfo', $data);
                    $zuid = pdo_insertid();
                    if ($zuid) {
                        echo json_encode(array('status' => 1, 'msg' => '成功', 'data' => array('zuid' => $zuid)));
                    } else {
                        echo json_encode(array('status' => -1, 'msg' => '失败'));
                    }
//                }
            }
        }else{
            echo json_encode(array('status' => -1, 'msg' => '信息不能为空'));
        }
    }

    public function get_client_ip() {
        if(getenv('HTTP_CLIENT_IP')){
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR')) {
            $client_ip = getenv('REMOTE_ADDR');
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }

    protected function getAvtPath($number){
        $str = '../addons/dq_2017/template/mobile/fourYearSrc/avt/icon%s.png';
        return sprintf($str,$number);
    }

    public function getPicPath($picDir,$image)
    {
        $str = '../addons/dq_2017/template/mobile/fourYearSrc/dreamCity/%s/%s.jpg';
        return sprintf($str,$picDir,$image);
    }

    //获得转发文案
    public function getShareText()
    {
        $res = pdo_fetch("select * from ims_dq_sharetext order by rand() limit 1");
        return $res;
    }

    /**
     *
     * 赞入口
     */
    public function doMobileZanEntry(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        //记录进入点赞程序的用户
        $user = pdo_fetch("SELECT * FROM ims_dq_zan_user Where openid='".$openid."'");
        $ip = $this->get_client_ip();
        $client = strtolower($_SERVER['HTTP_USER_AGENT']);
        //如果该用户没有信息 插入记录
        if(empty($user)){
            $userInfo = array(
                'openid'=>$openid,
                'client'=>$client,
                'createtime'=>time(),
                'ip'=>$ip
            );
            //插入赞用户信息
            $insertRes = pdo_insert('dq_zan_user',$userInfo);
            //如果插入信息失败 则让用户刷新重新操作
            if(!$insertRes){
                //系统繁忙
                exit();
            }
        }
        //获取此链接的ZUID
        $zuid = $_GET['zuid'];
        //查询链接创建者信息
        $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where id='".$zuid."'");

        $name = $zuserInfo['name'];
        if(empty($zuserInfo) || $zuid == ''){
            //echo "此助力链接非法 未录入信息";
            include $this->template('fourYears2w/error');
        }else{

            $shareInfo = $this->getShareText();
            $customerNumber = pdo_fetch("SELECT COUNT(*) as c FROM ims_dq_2wuserinfo WHERE id<=".$zuid);
            //梦想目的地信息
            $dreamInfo = pdo_fetch("select * from ims_dq_dreamdestination where id=".$zuserInfo['dreamdestination']);

            //梦想同行记录
            $dreamLogs = pdo_fetchall("select * from ims_dq_zan_log where zuid=".$zuid." order by create_time desc limit 0,10 ");


            //如果此助力链接所属者的openid等于当前用户openid 自己的助力页面
            if($zuserInfo['openid'] == $openid){
                //如果zuid是当前用户自己的 或者为空 代表是自己的助力页面
                include $this->template('fourYears2w/dq2017show');
            }else{
                include $this->template('fourYears2w/dq2017cheer');
            }
        }
    }

    /**
     * 微信订阅消息
     */
    public function doMobileSubscribeNow(){
        $url = 'mswechat.mebaby.cn';
        $unurl = urlencode($url);
        $realUrl = 'https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=wx34e1a7c405001045&scene=1000&template_id=mZXHWKWbnKkDNNQEcfMsZrr_s7bhJCEcugEs9q5c6T8&redirect_url=http%3A%2F%2Fmswechat.mebaby.cn&reserved=test#wechat_redirect';
        echo "<script>window.location.href='".$realUrl."'</script>";
    }


    /**
     * @author: 橙橙同学
     * @fun:我想与他""列表
     */
    public function getDoing()
    {

        $list = pdo_fetchall("select * from ims_dq_zan_doing");
        $rand_key = array_rand($list,1);
        //随机返回
        return $list[$rand_key];
    }


    /**
     * zuid的赞
     */
    public function doMobileZanUserInfo(){
        $frequency = $this->doMobilefindfrequency();
        $zuid = htmlspecialchars($_POST['zuid']);
        $ZanUser = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
        if(!empty($ZanUser)){
            $return  = array(
                'status'=>1,
                'data'=>array(
                    'name'=>$ZanUser['name'],
                    'zcount'=>$ZanUser['zcount']
                ),
                'msg'=>'成功',
            );
            echo json_encode($return);
        }else{
            $return  = array(
                'status'=>0,
                'msg'=>'此人未参与活动！',
            );
            echo json_encode($return);
        }
    }
    /**
     * 赞行为接口
     */
    public function doMobileZanFuns(){

        $this->commonApiCheck();

        $clientip = $this->get_client_ip();
        global $_W;
        $openid = $_W['openid'];
        $zuid = htmlspecialchars($_POST['zuid']);

        if(empty($openid) || empty($zuid)){
            $return = array(
                'status'=>-1,
                'msg'=>'非法！'
            );
            echo json_encode($return);
            exit();
        }
        //姓名 昵称
        $hname = htmlspecialchars($_POST['name']);


//        if(strlen($hname)>5)
//        {
//            $return = array(
//                'status'=>0,
//                'msg'=>'昵称太长了啦！'
//            );
//            echo json_encode($return);
//            exit();
//        }
//


        if($hname == '')
        {
            $hname = '匿名';
        }

        $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where id='".$zuid."'");

        if(empty($zuserInfo)){
            $return = array(
                'status'=>-1,
                'msg'=>'非法！'
            );
            echo json_encode($return);
            exit();
        }


        //如果当前用户的openid 等于 该zuid所对应的openid
        if($zuserInfo['openid'] == $openid){
            $return = array(
                'status'=>-1,
                'msg'=>'自己不能和自己同行！'
            );
            echo json_encode($return);
        }else{
            //如果赞LOG中已经存在openid的一一对应 那么代表已经助力过了
            $isZan = pdo_fetch("SELECT * FROM ims_dq_zan_log Where zopenid='".$zuserInfo['openid']."' and hopenid='".$openid."' limit 1");
            if(!empty($isZan)){
                //如果当前用户赞过了此人 就不能再赞
                $return = array(
                    'status'=>-1,
                    'msg'=>'您已经与此人同行！'
                );
                echo json_encode($return);
            }else{
                //json化  openid 为当前点击来的用户 zuid为链接的主人id
                $dataInfo = array(
                    'openid'=>$openid,
                    'zuid'=>$zuid,
                    'clientip'=>$clientip,
                    'hname'=>$hname
                );
                //拿到信息
                $openid = $dataInfo['openid'];
                $zuid = $dataInfo['zuid'];
                $clientip = $dataInfo['clientip'];
                $hname = $dataInfo['hname'];
                try{
                    pdo_begin();
                    //查询此人的被赞信息
                    $zanInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where id='".$zuid."'");
                    //更新助力记录

                    $zanC = $zanInfo['zcount']+1;
                    $zanData = array(
                        'zcount'=> $zanC
                    );
                    $userRes = pdo_update('dq_2wuserinfo', $zanData, array('id' => $zuid));

                    if ($userRes == false) {
                        throw new Exception('更新用户记录失败');
                    }

                    //帮助助力的人信息
                    $huidInfo = pdo_fetch("SELECT * FROM ims_dq_zan_user Where openid='".$openid."'");
                    //赞日志记录
                    if($clientip == ''){
                        $clientip = $huidInfo['ip'];
                    }

                    $doingArr = $this->getDoing();

                    if(empty($doingArr)){
                        $doing = '吃火锅';
                        $dopic = '';
                    }
                    else{
                        $doing = $doingArr['name'];
                        $dopic = $doingArr['pic'];
                    }




                    $avt = $this->getAvtPath(rand(1,8));


                    // hname 和 zname 一起去 doing
                    $logData = array(
                        'zuid'=>$zuid,
                        'hopenid'=>$openid,
                        'zopenid'=>$zanInfo['openid'],
                        'huid'=>$huidInfo['id'],
                        'create_time'=>time(),
//                        'update_time'=>time(),
                        'clientip'=>$clientip,
                        'hname'=>$hname,
                        'doing'=>$doing,
                        'zname'=>$zanInfo['name'],
                        'dopic'=>$dopic,
                        'hheadimage'=>$avt
                    );

                    $logId = pdo_insert('dq_zan_log', $logData);
                    if ($logId == false) {
                        throw new Exception('记录赞日志失败');
                    }
                    pdo_commit();
                    $return = array(
                        'status'=>1,
                        'msg'=>'同行成功',
                        'data'=>array(
                            'hname'=>$hname,
                            'zname'=>$zanInfo['name'],
                            'doing'=>$doing,
                            'pic'=>$dopic,

                        )
                    );
                    echo json_encode($return);
                }catch (Exception $e) {
                    $return = array(
                        'status'=>-1,
                        'msg'=>$e->getMessage(),
                    );
                    echo json_encode($return);
                    pdo_rollback();
                    exit();
                }
            }
        }
    }


    public function doMobileMathMethod($times,$tickets_times,$store_num,$store_amount){

        $rand=20;

        $fre = $tickets_times*(-$rand-$times*($rand-1))+$times*$rand+($store_num/$store_amount)*$rand;

        $temp = mt_rand('0','100');

        if ($temp <=$fre)
        {
            if($temp>=5){
                return 1;
            }
            else {
                return 2;
            }
        }
        else{
            return 0;
        }
    }

    public function doMobiley1y()
    {
        include $this->template('y1y');
    }


    public function doMobileSelect()
    {

        include $this->template('select');
    }



    /**
     * @author:橙橙同学
     * @func:领取红包 抽奖
     */
    public function doMobileReceiveRedPackage()
    {
        $this->commonApiCheck();

        global $_W,$_GPC;
        $openid = $_W['openid'];

        if(empty($openid))
        {
            $return = array(
                'status'=>-1,
                'msg'=>'非法请求'
            );
            echo json_encode($return);
        }else{

            $u = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo Where openid='".$openid."'");


            if(!empty($u))
            {
                if($u['isgift'] == 1)
                {
                    $return = array(
                        'status' =>-1,
                        'msg' => '用户已经抽过红包了'
                    );
                    echo json_encode($return);
                }
                else{
                    //正常 抽奖！更新用户抽奖状态 变为已抽奖
                    $data = array(
                        'isgift'=>1,
                    );
                    pdo_update("dq_2wuserinfo",$data,array('openid'=>$openid));
                    // 获得当前时间 精确到小时
                    $now = date('Y-m-d H'.":00:00");
                    $timeStamp = strtotime($now);

                    //查询红包

//                    $timeStamp =  "1513159211";
                    //小于当前时间戳的红包总量
//                    $redPackageCount = pdo_fetch("SELECT COUNT(*) as c from ims_dq_zan_redpackage where status=0 `timestamp`<=".$timeStamp);
                    //小于当前时间戳的未领红包总量
                    $redPackageRealCount = pdo_fetch("SELECT COUNT(*) as c from ims_dq_zan_redpackage where status=0 and `timestamp`<=".$timeStamp);

                    if($redPackageRealCount['c']>0){

                        $rand = rand(1,10);
                        $config = pdo_fetch("SELECT value from ims_dq_sys where dq_option='chance'");
                        //[1,2,3,4,5,6,7,8,9,10],
                        $chanceArr = explode(',',$config['value']);

                        if(in_array($rand,$chanceArr))
                        {
                            //中奖
                            $redPackage = pdo_fetch("SELECT * from ims_dq_zan_redpackage where status=0  and `timestamp`<=".$timeStamp." ORDER BY RAND() LIMIT 1 ");

                            if(empty($redPackage)){
                                $return = array(
                                    'status' => 0,
                                    'msg' => '未中奖',
                                    'extra'=>'系统原因 库存不足'
                                );
                                echo json_encode($return);
                                exit();
                            }
                            //记录中奖日志，更新中奖人信息，更新红包状态
                            pdo_begin();
                            try{

                                $data = array(
//                                    'isgift'=>1,
                                    'gift'=>$redPackage['amount'],
                                    'gift_id'=>$redPackage['id']
                                );

                                $uUpdateRes = pdo_update("dq_2wuserinfo",$data,array('openid'=>$openid));
                                if(!$uUpdateRes){
                                    throw  new Exception('更新用户信息失败');
                                }

                                $redUpdateArr = array(
                                    'status'=>1
                                );

                                $rUpdateRes = pdo_update("dq_zan_redpackage",$redUpdateArr,array('id'=>$redPackage['id']));
                                if(!$rUpdateRes){
                                    throw  new Exception('更新红包信息失败');
                                }

                                pdo_commit();
                                $return = array(
                                    'status' => 1,
                                    'data'=>array(
                                        'amount'=>$redPackage['amount']
                                    ),
                                    'msg' => '恭喜你中奖啦'
                                );
                                echo json_encode($return);
                            }catch (Exception $e){
                                pdo_rollback();
                                $return = array(
                                    'status' => 0,
                                    'msg' => '未中奖',
                                    'extra'=>$e->getMessage()
                                );
                                echo json_encode($return);
                            }

                        }
                        else{
                            $return = array(
                                'status' => 0,
                                'msg' => '未中奖'
                            );
                            echo json_encode($return);
                        }
                    }else{
                        $return = array(
                            'status' => 0,
                            'msg' => '未中奖',
                        );
                        echo json_encode($return);
                    }
                }
            } else {
                $return = array(
                    'status' => -1,
                    'msg' => '用户不存在'
                );
                echo json_encode($return);
            }
        }
    }


    function getRedGift()
    {
        $count = 0;
        $redP = array();
        $total = 2000;

        $num = 800;
        $avg =$total/$num;
        $j = 0;
        $k=0;

        for($i=0;$i<=1800;$i++)
        {
            if($count>=$total){
                echo 1;
                break;
            }

            $rand1 = rand(1,10);

//            if(in_array($rand1,array(1,2))){
//                $rand = 4;
//                $count = $count + $rand;
//            }
//
//            if(in_array($rand1,array(3,4,5))){
//                $rand = 3;
//                $count = $count + $rand;
//            }

            if(in_array($rand1,array(4,5,6,7,8,9,10))){
                $rand = 2;
                $count = $count + $rand;
            }else{
                $count = rand(3,10);
            }


            $redP[$i]['amount'] = $rand;

            //循环一次结束 小时+1
            $timeS = 1513296000+(3600*$k);

            //如果$j<=40 则生成该小时区间的红包 直到生成40个
            if($j<=100){
                if(($timeS>=1513296000) && ($timeS<=1513350000))
                {
                    $d = date('Y-m-d H',($timeS));
                    $j++;
                    $redP[$i]['time'] = $d;
                    $redP[$i]['timestamp'] = $timeS;
                }
            }else{

                $j=0;
                $k = $k+1;

                /*
                //J大于40 则
                if(($timeS>=1513299600) && ($timeS<=1513350000))
                {
                    $d = date('Y-m-d H',$timeS);
                    $redP[$i]['time'] = $d;
                    $redP[$i]['timestamp'] = $timeS;

                }
                */
            }
        }



        /*
        for($i=0;$i<=700;$i++)
        {
            if($count>=$total){
                echo 1;
                break;
            }
            $rand = rand($min,$avg*2);
            $count = $count + $rand;
            $redP[$i]['amount'] = $rand;
            $timeS = 1513209600+(3600*$k);
            if($j>=30){
                if(($timeS>=1513209600) && ($timeS<=1513267200))
                {
                    $d = date('Y-m-d H',$timeS);
                    $redP[$i]['time'] = $d;
                    $redP[$i]['timestamp'] = $timeS;
                    $j=0;
                    $k = $k+1;
                }
            }else{
                if(($timeS>=1513209600) && ($timeS<=1513267200))
                {
                    $d = date('Y-m-d H',($timeS));
                    $j++;
                    $redP[$i]['time'] = $d;
                    $redP[$i]['timestamp'] = $timeS;
                }
            }

        }
        */

        return $redP;
    }

    function doMobiletestBound()
    {
        $data = $this->getRedGift();

        foreach ($data as $key=>$value)
        {
            pdo_insert('dq_zan_redpackage1',$value);
        }

    }



    public function doMobileDq2017(){
        include $this->template('fourYears2w/dq2017');
    }
    public function doMobileDq2018(){
        include $this->template('fourYears2w/dq2017cheer');
    }

    //老司机送300 活动begin
    //身份判断
    public function doMobileJudgStatus(){
        global $_W;
        $openid = $_W['openid'];
        $sql="SELECT name,tel,code,platenumber,mdid,create_time,get_time FROM ims_mc_act_car4card_getcard as A left join ims_mc_act_car4card_cardcode as B on A.openid=B.openid  where A.openid='{$openid}'";
        $result=pdo_fetch($sql);
        if(empty($result['create_time']))
        {
            $response=array('status'=>0,'errmsg'=>'ok');
        }elseif (!empty($result['create_time']) && empty($result['get_time'])){
            $response=array('status'=>1,'name'=>$result['name'],
                'tel'=>$result['tel'],
                'idNum'=>$result['idNum'],
                'mdid'=>$result['mdid'],
                'platenumber'=>$result['platenumber']);
        }else{
            $response=array('status'=>2,'name'=>$result['name'],
                'tel'=>$result['tel'],
                'idNum'=>$result['idNum'],
                'mdid'=>$result['mdid'],
                'platenumber'=>$result['platenumber']);
        }
        echo  json_encode($response);
    }
    //送卡券 起始页
    public function doMobilebegin(){
        global $_W;
        if ($_W['openid'] == "") {
            // 非微信浏览器禁止浏览
            echo '<img style="margin-left:30%;height:80px;width:80px;display:inline-block" src="../addons/member/template/mobile/road38/images/cardhead.png"/>';
            echo "<h4 style='display:inline-block'> :-) 嘿嘿嘿 请在微信打开此网页 3Q ^_^ </h4>";
        } else {
            $sys = array('on_off' => 8);
            if($sys['on_off'] == 1) {
                $errMsg = '对不起，活动已经结束。';
                include $this->template("tips/tips");
                exit();
            } else {
                global $_W;
                $typea=$_GET['typea'];
                $openid = $_W['openid'];
                $userip=$_SERVER['REMOTE_ADDR'];
                $info=pdo_fetch("SELECT * FROM ims_mc_act_car4card_getcard WHERE openid ='{$openid}'");
                $card=pdo_fetch("SELECT * FROM ims_mc_act_car4card_cardcode WHERE openid ='{$openid}' and cardid in ('pk_mF1uR_WlWRg68gREM9wx26zNg','pk_mF1lcUshWZRgodu5Brk4DqjM4') order by id desc");

                if(!empty($info) && !empty($card)){
                    $code= $card['code'];
                    $cardid = $card['cardid'];
                    include $this->template('car4card/share');
                }else{
                    include $this->template('car4card/begin');
                }
            }
        }
    }
    //分享页
    public function doMobileshare(){
        global $_W;
        if ($_W['openid'] == "") {
            // 非微信浏览器禁止浏览
            echo '<img style="margin-left:30%;height:80px;width:80px;display:inline-block" src="../addons/member/template/mobile/road38/images/cardhead.png"/>';
            echo "<h4 style='display:inline-block'> :-) 嘿嘿嘿 请在微信打开此网页 3Q ^_^ </h4>";
        } else {
            $sys = array('on_off' => 8);
            if($sys['on_off'] == 1){
                $errMsg = '对不起，活动已经结束。';
                include $this->template("tips/tips");
                exit();
            } else {
                global $_W;
                $typea=$_GET['typea'];
                $openid = $_W['openid'];
                $userip=$_SERVER['REMOTE_ADDR'];
                $info=pdo_fetch("SELECT * FROM ims_mc_act_car4card_getcard WHERE openid ='{$openid}'");
                $card=pdo_fetch("SELECT * FROM ims_mc_act_car4card_cardcode WHERE openid ='{$openid}' and cardid in ('pk_mF1uR_WlWRg68gREM9wx26zNg','pk_mF1lcUshWZRgodu5Brk4DqjM4') order by id desc");

                if(!empty($info)  && !empty($card)){
                    $code= $card['code'];
                    $cardid = $card['cardid'];
                    include $this->template('car4card/share');
                }else{
                    include $this->template('car4card/begin');
                }
            }
        }
    }

    //保存
    public function doMobilesavecar4card(){
        $openid     =$_POST['openid'];
        $ownername  =htmlspecialchars($_POST['userName']);
        $ownertel   =$_POST['userPhone'];
        $platenumber=str_replace(' ','',$_POST['userCar']);
        $mdid      =$_POST['mdid'];
        $isAgree    =$_POST['isAgree'];
        $typea      =$_POST['typea'];

        $mdNameArr = array('1'=>"民生百货解放路店",'63'=>"民生百货骡马市店");

        $plateinfo = pdo_fetch("SELECT * FROM ims_mc_act_car4card_getcard where platenumber='{$platenumber}'");
        $telinfo = pdo_fetch("SELECT * FROM ims_mc_act_car4card_getcard where tel='{$ownertel}'");
        if (!empty($telinfo)) {
            $response = array('status' => 1, 'errmsg' => 'AlreadyTel');
        } elseif (!empty($plateinfo)) {
            $response = array('status' =>2, 'errmsg' => 'AlreadyPlate');
        } else{
            $data = array(
                'openid' => $openid,
                'name' => $ownername,
                'tel' => $ownertel,
                'mdid'=>$mdid,
                'mdname' => $mdNameArr[$mdid],
                'platenumber' => $platenumber,
                'create_time' => time(),
                'gzh'=>$typea
            );
            $insert = pdo_insert('mc_act_car4card_getcard', $data);
            if ($insert) {
                $response = array('status' => 0, 'errmsg' => 'ok');
            }else{
                $response = array('status' => 7, 'errmsg' => '信息插入失败');
            }
        }

        $response=json_encode($response);
        echo $response;

    }

    //拉起卡券 签名算法
    public function doMobileCardSignInfo(){
        global $_W;
        $return = "";
        $timestamp=$_W['timestamp'];
        $cticket=$this->doMobileGetCardS();
        $sql = "select mdid from ims_mc_act_car4card_getcard where openid = '".$_W['openid']."'";
        $rs = pdo_fetch($sql);
        switch($rs['mdid']){
            case 1: $card_id = "pk_mF1uR_WlWRg68gREM9wx26zNg"; break;
            case 63: $card_id = "pk_mF1lcUshWZRgodu5Brk4DqjM4"; break;
        }


        $nonce_str=$this->generateNonceStr();
        $card = array(
            $timestamp,
            $cticket,
            $card_id,
            $nonce_str
        );
        sort($card,SORT_STRING);
        foreach($card as $k=>$v){
            $return .= $v;
        }

        $sign=sha1($return);
        $res=array(
            'timestamp'=>$timestamp,
            'signature'=>$sign,
            'noncestr'=>$nonce_str,
        );

        echo json_encode($res);
    }


    /**
     * @param $uniacid
     * @return array|mixed
     * @author: 橙橙同学
     * @fun:获取accessToken
     */
    protected function getAccessToken($uniacid){
        $weiObj = WeAccount::create($uniacid);
        $token = $weiObj->fetch_token();
        return $token;
    }

    
    /**
     * 卡券 获得 $api_ticket
     */
    public function doMobileGetCardS(){
        global $_W;
        //取缓存
        $dcdyr_ticket=cache_load('api_ticket');

        //如果缓存的时间 那么 重新获取并缓存
        if(@$dcdyr_ticket['exp'] < time()){
            $access_token=$this->getAccessToken($_W['uniacid']);
            $userinfo = ihttp_get("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=wx_card");
            
            $ticketJson=$userinfo['content'];
            $ticketArr=json_decode($ticketJson,true);
            $ticket=$ticketArr['ticket'];
            //缓存时间为当前时间加7000秒  实际为7200秒
            $cacheTime=time()+6000;
            $cacheTicket=array(
                'ticket'=>$ticket,
                'exp'=>$cacheTime,
            );
            cache_write('api_ticket', $cacheTicket);

            $dcdyr_ticket['ticket'] = $cacheTicket['ticket'];
        }

        return $dcdyr_ticket['ticket'];
    }
    /*
     * 随机字符串
     */
    public function generateNonceStr($length=16){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
    //获取用户地理位置
    public function doMobileGetLocation(){
        $ip=$_POST['userip'];
        $gzh=$_POST['typea'];
        $openid=$_POST['openid'];
        $lat=$_POST['latitude'];    //纬度
        $lng=$_POST['longitude'];

        if (!empty($lat) && !empty($lng)) {
            $location = file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location=" . $lat . "," . $lng . "&key=IBMBZ-5DRW3-NV535-YLCTQ-P7AM2-5PFP6");
            $location = json_decode($location, true);
            $province = $location['result']['address_component']['province'];
            $city = $location['result']['address_component']['city'];
            $district = $location['result']['address_component']['district'];
            $street = $location['result']['address_component']['street'];
            $street_num = $location['result']['address_component']['street_number'];
            $response = array("status" => 0, "city" => $city);
        } else {
            $response = array('status' => 1, 'errmsg' => '请给我经纬度');
        }

        echo json_encode($response);
    }
    //油卡 end
    //家电折扣券 begin
    public function doMobileJd(){

        global $_W;
        include $this->template('jd/jd');

    }
    //家电折扣券 end






    public function doMobileNewYearCardSignInfo(){
        global $_W;
        $timestamp=$_W['timestamp'];
        $api_ticket=$this->doMobileGetCardS();
//        $card_id = 'pk_mF1vEIHfCLjML2t8b9hJhkU5Y';
        $card_id = 'pk_mF1mbrcKJ_VMhdTbSY_Az8H_Y';
        $nonce_str=$this->generateNonceStr();
        $card = array(
            $timestamp,
            $api_ticket,
            $card_id,
            $nonce_str
        );
        sort($card,SORT_STRING);
        $return='';
        foreach($card as $k=>$v){
            $return.= $v;
        }
        $sign=sha1($return);

        $shareFun = $this->doMobileNewYearShare();

        if($shareFun){
            $res=array(
                'status'=>1,
                'timestamp'=>$timestamp,
                'signature'=>$sign,
                'nonce_str'=>$nonce_str,
            );
        }else{
            $res=array(
                'status'=>0,
            );
        }
//        return $res;
        echo json_encode($res);
    }


    public function doMobileNewYearEntry()
    {
        //过滤
        $this->ZanFilter();
        global  $_W;
        $openid = $_W['openid'];
        //sql优化：const型
        $userId = pdo_fetch("SELECT id FROM ims_newyear_userinfo Where openid='".$openid."'");
        if(empty($userId)){
            include $this->template('newyear/newyear');
        }else{
            header('location:http://mswechat.mebaby.cn/app/index.php?i=2&c=entry&do=NewYearShowBill&m=dq_2017&zuid='.$userId['id']);
        }
    }


    public function doMobileNewYearShowBill(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        //记录进入点赞程序的用户
        $user = pdo_fetch("SELECT * FROM ims_newyear_act_user Where openid='".$openid."'");
        $ip = $this->get_client_ip();
        $client = strtolower($_SERVER['HTTP_USER_AGENT']);
        //如果该用户没有信息 插入记录
        if(empty($user)){
            $userInfo = array(
                'openid'=>$openid,
                'client'=>$client,
                'createtime'=>time(),
                'ip'=>$ip
            );
            //插入赞用户信息
            $insertRes = pdo_insert('newyear_act_user',$userInfo);
            //如果插入信息失败 则让用户刷新重新操作
            if(!$insertRes){
                //系统繁忙
                exit();
            }
        }
        //获取此链接的ZUID
        $zuid = $_GET['zuid'];
        //查询链接创建者信息
        $zuserInfo = pdo_fetch("SELECT * FROM ims_newyear_userinfo Where id='".$zuid."'");

        $name = $zuserInfo['name'];
        if(empty($zuserInfo) || $zuid == ''){
            //echo "此助力链接非法 未录入信息";
            include $this->template('newyear/error');
        }else{
            if($zuserInfo['openid'] == $openid){
                //如果zuid是当前用户自己的 或者为空 代表是自己的助力页面
                include $this->template('newyear/showbill');
            }else{
                include $this->template('newyear/showbillother');
            }
        }
    }



    public function doMobileNewYearShare()
    {
        $this->commonApiCheck();
        global $_W,$_GPC;
        $openid = $_W['openid'];
        $userInfo = pdo_fetch("select * from ims_newyear_userinfo where openid="."'$openid'");

        $data = array(
            'isshare'=>1,
            'sharecount'=>$userInfo['sharecount']+1
        );

        $shareUpdate = pdo_update("newyear_userinfo",$data,array(
            'openid'=>$openid
        ));
        return $shareUpdate;
    }


    /**
     * 收集信息
     */
    public function doMobileNewYearSaveActUserInfo(){
        $this->commonApiCheck();
        global $_W,$_GPC;
        $openid = $_W['openid'];

        //sql优化：ref
        if(!empty($_GPC['transaction']) && !empty($_GPC['name']) && !empty($_GPC['mobile']) && !empty($_GPC['cashier'])){

            $name = htmlspecialchars($_GPC['name']);
            $transaction = htmlspecialchars($_GPC['transaction']);
            $mobile = htmlspecialchars($_GPC['mobile']);
            $cashier = htmlspecialchars($_GPC['cashier']);
            $userId = pdo_fetch("SELECT * FROM ims_newyear_userinfo Where openid='" . $openid . "'");
            if (!empty($userId)) {
                echo json_encode(array('status' => -1, 'msg' => '已经提交过信息'));
            }else{
                $data = array(
                    'openid' => $openid,
                    'transaction'=>$transaction,
                    'ip' => $this->get_client_ip(),
                    'create_time' => time(),
                    'mobile' => $mobile,
                    'name' => $name,
                    'status'=>0,
                    'cashier'=>$cashier,
                    'isshare'=>0
                );
                pdo_insert('newyear_userinfo', $data);
                $zuid = pdo_insertid();
                if ($zuid) {
                    echo json_encode(array('status' => 1, 'msg' => '成功', 'data' => array('zuid' => $zuid)));
                } else {
                    echo json_encode(array('status' => -1, 'msg' => '失败'));
                }
            }
        }else{
            echo json_encode(array('status' => -1, 'msg' => '信息不能为空'));
        }
    }

}