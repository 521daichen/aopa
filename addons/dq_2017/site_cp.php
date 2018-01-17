<?php
/**
 * 店庆模块微站定义
 *
 * @author 橙橙同学
 * @url
 */

defined('IN_IA') or exit('Access Denied');
//载入队列
load()->classs('Queue');
load()->func('logging');
load()->func('place');
load()->classs('Log');
load()->classs('excel');

class Dq_2017ModuleSite extends WeModuleSite {

    public function doMobilecheat(){
        include $this->template('fourYears2w/cheat');
    }
    public function doMobileAvgGet(){
        include $this->template('tongji/avg');
    }
    public function doMobileAvgGetJiasu(){
        include $this->template('tongji/avgjiasu');
    }
    public function ZanFilter(){
        global $_W;
        $openid =$_W['openid'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            include $this->template('fourYears2w/wechat');
            exit();
        }
        if(!empty($openid)){
            $myinfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_1 Where openid='" . $openid . "'");
            if(!empty($myinfo) && $myinfo['status'] == 0){
                include $this->template('fourYears2w/cheat');
                exit();
            }
        }
    }
    public function doMobileActRule(){
        include $this->template('fourYears2w/rule');
    }
    public function doMobileerrPage(){
        include $this->template('fourYears2w/error');
    }
    public function quePage(){
        $loadurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        include $this->template('fourYears2w/error');
        exit();
    }
    public function doMobileCustomer(){
        $queuer = new Queue();
        $info = $queuer->customer->custom('daichenlists');
        logging_run('出队列成功 信息为'.$info."\n", 'trace', 'queuecustomer');

    }
    public function doMobileProducter(){
        $queuer = new Queue();
        for($i=0;$i<100000;$i++){
            $queuer->producter->product('daichenlists','i am daichen'.$i);
            logging_run('入队列成功 daichen'.$i."\n", 'trace', 'queueproducter');
            sleep(1);
        }
    }
    public function doMobileDaogou(){
        global $_W;
        $openid = $_W['openid'];
        $url = "http://wx.cnsaga.com/app/index.php?i=4&c=entry&do=guideRegister&m=member&pkid=".$openid;
        header('Location: '.$url);
    }

    public function doMobilefindfrequency(){
        $frequencyinfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='frequency'");
        return $frequencyinfo['value'];
    }
    public function doMobileResultone(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        $frequency = $this->doMobilefindfrequency();
        if($frequency > 1){
            $userInfo = pdo_fetchall("SELECT openid,name,mobile,idNum FROM ims_dq_zan_lucker_1 ORDER BY convert(name using gbk) ASC");
            $myinfo = pdo_fetch("SELECT openid,name,mobile,idNum,zcount FROM ims_dq_2wuserinfo_1 Where openid='" . $openid . "'");
            if(!empty($myinfo)) {
                $userzcount = $myinfo['zcount'];
                unset($myinfo['zcount']);
                $luck = in_array($myinfo, $userInfo);
                if ($luck) {
                    $isluck = 1;
                } else {
                    $isluck = 0;
                }
            }else{
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
    public function doMobileResulttwo(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        $frequency = $this->doMobilefindfrequency();
        if($frequency > 2){
            $userInfo = pdo_fetchall("SELECT openid,name,mobile,idNum FROM ims_dq_zan_lucker_2 ORDER BY convert(name using gbk) ASC");
            $myinfo = pdo_fetch("SELECT openid,name,mobile,idNum,zcount FROM ims_dq_2wuserinfo_2 Where openid='" . $openid . "'");
            if(!empty($myinfo)) {
                $userzcount = $myinfo['zcount'];
                unset($myinfo['zcount']);
                $luck = in_array($myinfo, $userInfo);
                if ($luck) {
                    $isluck = 1;
                } else {
                    $isluck = 0;
                }
            }else{
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
            include $this->template('fourYears2w/resulttwo');
        }else{
            include $this->template('fourYears2w/resultall');
        }
    }
    public function doMobileResultthree(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        $frequency = $this->doMobilefindfrequency();
        if($frequency > 3){
            $userInfo = pdo_fetchall("SELECT openid,name,mobile,idNum FROM ims_dq_zan_lucker_3 ORDER BY convert(name using gbk) ASC");
            $myinfo = pdo_fetch("SELECT openid,name,mobile,idNum,zcount FROM ims_dq_2wuserinfo_3 Where openid='" . $openid . "'");
            if(!empty($myinfo)) {
                $userzcount = $myinfo['zcount'];
                unset($myinfo['zcount']);
                $luck = in_array($myinfo, $userInfo);
                if ($luck) {
                    $isluck = 1;
                } else {
                    $isluck = 0;
                }
            }else{
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
            include $this->template('fourYears2w/resultthree');
        }else{
            include $this->template('fourYears2w/resultall');
        }
    }
    public function doMobileResultfour(){
        $this->ZanFilter();
        global $_W;
        $openid = $_W['openid'];
        $frequency = $this->doMobilefindfrequency();
        if($frequency >4){
            $userInfo = pdo_fetchall("SELECT openid,name,mobile,idNum FROM ims_dq_zan_lucker_4 ORDER BY convert(name using gbk) ASC");
            //获得我的信息
            $myinfo = pdo_fetch("SELECT openid,name,mobile,idNum,zcount FROM ims_dq_2wuserinfo_4 Where openid='" . $openid . "'");
            if(!empty($myinfo)) {

                $userzcount = $myinfo['zcount'];
                unset($myinfo['zcount']);

                $luck = in_array($myinfo, $userInfo);
                if ($luck) {
                    $isluck = 1;
                } else {
                    $isluck = 0;
                }
            }else{
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
            include $this->template('fourYears2w/resultfour');
        }else{
            include $this->template('fourYears2w/resultall');
        }
    }
    /**
     * 查询用户选择的加速器
     */
    public function doMobileGetUserSelects(){
        $frequency = $this->doMobilefindfrequency();
        $openid = $_POST['openid'];
        if(empty($openid)){
            $return = array(
                'status'=>0,
                'message'=>'请传入参数'
            );
            echo json_encode($return);
            exit();
        }
        $userSelected = pdo_fetch("SELECT istrip as trip,isbank as bank,iscar as car,isownship 
                                        as ownship,isownplane as ownplane,istransport as transport,
                                        isnongyao as nongyao,ismall as mall,isgolf as golf
                                        FROM ims_dq_2wuserinfo_".$frequency." Where openid='".$openid."'");
        $data = $userSelected;
        if($userSelected!=''){
            $return = array(
                'status'=>1,
                'data'=>$data,
                'message'=>'查询成功'
            );
            echo json_encode($return);
        }else{
            $return = array(
                'status'=>0,
                'data'=>'',
                'message'=>'查询失败'
            );
            echo json_encode($return);
        }
    }
    /**
     * 更新用户选择加速器
     */
    public function doMobileUpdateSelects(){
        $this->ZanFilter();
        $frequency = $this->doMobilefindfrequency();
        $trip = isset($_POST['trip'])?$_POST['trip']:0;
        $bank = isset($_POST['bank'])?$_POST['bank']:0;
        $car = isset($_POST['car'])?$_POST['car']:0;
        $golf = isset($_POST['golf'])?$_POST['golf']:0;
        $mall = isset($_POST['mall'])?$_POST['mall']:0;
        $nongyao = isset($_POST['nongyao'])?$_POST['nongyao']:0;
        $transport = isset($_POST['transport'])?$_POST['transport']:0;
        $ownplane = isset($_POST['ownplane'])?$_POST['ownplane']:0;
        $ownship = isset($_POST['ownship'])?$_POST['ownship']:0;
        $zancount = 0;
        if($trip!=0){
            $istrip = 1;
            $zancount += $trip;
        }else{
            $istrip = 0;
        }
        if($bank!=0){
            $isbank = 1;
            $zancount += $bank;
        }else{
            $isbank =0;
        }

        if($car!=0){
            $iscar = 1;
            $zancount += $car;
        }else{
            $iscar =0;
        }

        if($golf!=0){
            $isgolf = 1;
            $zancount += $golf;
        }else{
            $isgolf =0;
        }

        if($mall!=0){
            $ismall = 1;
            $zancount += $mall;
        }else{
            $ismall =0;
        }

        if($nongyao!=0){
            $isnongyao = 1;
            $zancount += $nongyao;
        }else{
            $isnongyao =0;
        }

        if($transport!=0){
            $istransport = 1;
            $zancount += $transport;
        }else{
            $istransport =0;
        }

        if($ownplane!=0){
            $isownplane = 1;
            $zancount += $ownplane;
        }else{
            $isownplane =0;
        }

        if($ownship!=0){
            $isownship = 1;
            $zancount += $ownship;
        }else{
            $isownship =0;
        }
        $jiasucount = $zancount;
        $openid = $_POST['openid'];
        if(empty($openid)){
            $return = array(
                'status'=>0,
                'message'=>'请传入参数'
            );
            echo json_encode($return);
            exit();
        }
        $userinfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where openid='" . $openid . "'");
        $frontjiasuqi =$userinfo['jiasucount'];
        $zancount = $userinfo['zcount']-$userinfo['jiasucount']+$jiasucount;
        $data = array(
            'update_time' => time(),
            'istrip'=>$istrip,
            'isbank'=>$isbank,
            'iscar'=>$iscar,
            'isgolf'=>$isgolf,
            'ismall'=>$ismall,
            'isnongyao'=>$isnongyao,
            'istransport'=>$istransport,
            'isownplane'=>$isownplane,
            'isownplane'=>$isownplane,
            'isownship'=>$isownship,
            'zcount'=>$zancount,
            'jiasucount'=>$jiasucount
        );
        $result = pdo_update('dq_2wuserinfo_'.$frequency, $data, array('openid' => $openid));
        if($result){
            $return = array(
                'status'=>1,
                'message'=>'更新成功'
            );
            echo json_encode($return);
        }else{
            $return = array(
                'status'=>0,
                'message'=>'更新失败请稍后再试'
            );
            echo json_encode($return);
        }
    }
    /**
     * 无队列版本
     */
    public function doMobilefourYears2w()
    {

        $this->ZanFilter();
        $shareid = $_GET['shareid']?$_GET['shareid']:NULL;
        $boci = htmlspecialchars($_GET['boci']);
        $frequency = $this->doMobilefindfrequency();
        $active = array(
            1,2
        );

        if(!in_array($boci,$active)){
            include $this->template('fourYears2w/resultall');
            exit();
        }

        if($boci != $frequency){
            include $this->template('fourYears2w/resultall');
            exit();
        }
        //debug开关
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        global  $_W;
        $openid = $_W['openid'];
        $way = $_GET['ADTAG'];
        //开启状态
        if($debugsys == 1){
            $logger->debug_fun(__FUNCTION__);
        }
        $userId = pdo_fetch("SELECT id FROM ims_dq_2wuserinfo_".$frequency." Where openid='".$openid."'");
        if(empty($userId)){
            include $this->template('fourYears2w/dq2017');
        }else{
            header('location:http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=zanentry&m=dq_2017&zuid='.$userId['id'].'&ADTAG='.$way.'&boci='.$boci);
        }
    }

    /**
     * 2w入口函数 队列版本
     */
    public function doMobilefourYears2wsss()
    {
        //过滤 获取当前活动波次
        $this->ZanFilter();
        $shareid = $_GET['shareid']?$_GET['shareid']:NULL;
        $boci = htmlspecialchars($_GET['boci']);
        $frequency = $this->doMobilefindfrequency();
        if($boci != $frequency){
            include $this->template('fourYears2w/resultall');
            exit();
        }
        //debug开关
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        global  $_W;
        $openid = $_W['openid'];
        $way = $_GET['ADTAG'];
        $data = json_encode($openid);
        //队列
        $quelist = 'dqentry';
        $queuer = new Queue();
        //进入队列
        $rediscount = array(
            'count'=>$queuer->tool->getListSize($quelist),
        );
        $data = json_encode($rediscount);
        $queuer->producter->product($quelist,$data);
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            //记录当前执行的方法
            $logger->debug_fun(__FUNCTION__);
            $logger->debug_que_push($quelist,$data);
        }
        $logger->info_que_push($quelist,$data);
        //只允许1000个人出队 其他人排队
        if($queuer->tool->getListSize($quelist)>3){
            $logger->info_que_ing($quelist,$data,'ll');

            //排队页面
            $this->quePage();
        }else{
            //开启状态
            if($debugsys == 1){
                //debug级别日志
                $logger->dehug_que_pop($quelist,$data);
            }
            //info 记录出队
            $logger->info_que_pop($quelist,$data);
            $userId = pdo_fetch("SELECT id FROM ims_dq_2wuserinfo_".$frequency." Where openid='".$openid."'");
            if(empty($userId)){
                include $this->template('fourYears2w/dq2017');
            }else{
                header('location:http://2nd.sagabuy.com/app/index.php?i=2&c=entry&do=zanentry&m=dq_2017&zuid='.$userId['id'].'&ADTAG='.$way.'&boci='.$boci);
            }
        }
    }
    /**
     * 收集信息
     */
    public function doMobileSaveActUserInfo(){
        $frequency = $this->doMobilefindfrequency();
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_fun(__FUNCTION__);
        }
        if(isset($_REQUEST['openid']) && isset($_REQUEST['name']) && isset($_REQUEST['mobile']) && isset($_REQUEST['idcard'])) {
            $openid = htmlspecialchars($_REQUEST['openid']);
            $name = htmlspecialchars($_REQUEST['name']);
            $mobile = htmlspecialchars($_REQUEST['mobile']);
            $idNum = htmlspecialchars($_REQUEST['idcard']);
            $way = htmlspecialchars($_REQUEST['ADTAG']);
            $sid = htmlspecialchars($_REQUEST['shareid']);
            //加速器部分
            if(isset($_POST['trip'])){
                $tag1 = htmlspecialchars($_POST['trip']);
            }
            if(isset($_POST['bank'])){
                $tag1 = htmlspecialchars($_POST['bank']);
            }
            if(isset($_POST['car'])){
                $tag1 = htmlspecialchars($_POST['car']);
            }
            if(isset($_POST['shareid'])){
                if(!empty($_POST['shareid'])){
                    $sid = htmlspecialchars($_POST['shareid']);
                }
            }
            $trip = isset($_POST['trip'])?$_POST['trip']:0;
            $bank = isset($_POST['bank'])?$_POST['bank']:0;
            $car = isset($_POST['car'])?$_POST['car']:0;
            $golf = isset($_POST['golf'])?$_POST['golf']:0;
            $mall = isset($_POST['mall'])?$_POST['mall']:0;
            $nongyao = isset($_POST['nongyao'])?$_POST['nongyao']:0;
            $transport = isset($_POST['transport'])?$_POST['transport']:0;
            $ownplane = isset($_POST['ownplane'])?$_POST['ownplane']:0;
            $ownship = isset($_POST['ownship'])?$_POST['ownship']:0;
            $zancount = 0;
            if($trip!=0){
                $istrip = 1;
                $zancount += $trip;
            }else{
                $istrip = 0;
            }
            if($bank!=0){
                $isbank = 1;
                $zancount += $bank;
            }else{
                $isbank =0;
            }
            if($car!=0){
                $iscar = 1;
                $zancount += $car;
            }else{
                $iscar =0;
            }
            if($golf!=0){
                $isgolf = 1;
                $zancount += $golf;
            }else{
                $isgolf =0;
            }
            if($mall!=0){
                $ismall = 1;
                $zancount += $mall;
            }else{
                $ismall =0;
            }
            if($nongyao!=0){
                $isnongyao = 1;
                $zancount += $nongyao;
            }else{
                $isnongyao =0;
            }
            if($transport!=0){
                $istransport = 1;
                $zancount += $transport;
            }else{
                $istransport =0;
            }
            if($ownplane!=0){
                $isownplane = 1;
                $zancount += $ownplane;
            }else{
                $isownplane =0;
            }
            if($ownship!=0){
                $isownship = 1;
                $zancount += $ownship;
            }else{
                $isownship =0;
            }
            $jiasucount = $zancount;
            global $_W;
            $openId = $_W['openid'];
            $userId = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where openid='" . $openid . "'");
            if (!empty($userId)) {
                echo json_encode(array('status' => 0,'msg' => '您的信息已经录入 请勿重复录入'));
            }else{
                //查询已经获奖的会员名单
                $luckformobile = pdo_fetch("SELECT mobile,idNum,openid FROM ims_dq_lucklist Where mobile='" . $mobile . "'");
                $luckforidnum = pdo_fetch("SELECT mobile,idNum,openid FROM ims_dq_lucklist Where idNum='" . $idNum . "'");
                $luckforopenid = pdo_fetch("SELECT mobile,idNum,openid FROM ims_dq_lucklist Where openid='" . $openid . "'");
                //前面中奖过的用户判断
                if(!empty($luckformobile)){
                    echo json_encode(array('status' => 0, 'msg' => '该手机号已经中过奖了！'));
                    exit();
                }
                if(!empty($luckforidnum)){
                    echo json_encode(array('status' => 0, 'msg' => '该身份证已经中过奖了！'));
                    exit();
                }
                if(!empty($luckforopenid)){
                    echo json_encode(array('status' => 0, 'msg' => '该微信已经中过奖了！'));
                    exit();
                }
                $searchwithmobile = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where mobile='" . $mobile . "'");
                $searchwithidnum = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where idNum='" . $idNum . "'");
                if(!empty($searchwithidnum)){
                    echo json_encode(array('status' => 0, 'msg' => '该身份证信息已经存在！'));
                    exit();
                }
                if(!empty($searchwithmobile)){
                    echo json_encode(array('status' => 0, 'msg' => '该手机号信息已存在！'));
                    exit();
                }
                if($idNum == $userId['idNum']){
                    $msg = array(
                        'name'=>'身份证',
                        'msg'=>'您的身份证已经被注册'
                    );
                    echo json_encode(array('status' => 0, 'msg' => '身份证已经存在'));
                }else{
                    $data = array(
                        'openid' => $openid,
                        'name' => $name,
                        'mobile' => $mobile,
                        'way' => $way,
                        'idNum' => $idNum,
                        'ip' => $this->get_client_ip(),
                        'create_time' => time(),
                        'update_time' => time(),
                        'istrip'=>$istrip,
                        'isbank'=>$isbank,
                        'iscar'=>$iscar,
                        'isgolf'=>$isgolf,
                        'ismall'=>$ismall,
                        'isnongyao'=>$isnongyao,
                        'istransport'=>$istransport,
                        'isownplane'=>$isownplane,
                        'isownplane'=>$isownplane,
                        'isownship'=>$isownship,
                        'zcount'=>$zancount,
                        'jiasucount'=>$jiasucount,
                        'sid'=>$sid,
                    );
                    $zuidinfo = pdo_insert('dq_2wuserinfo_'.$frequency, $data);
                    $zuid = pdo_insertid();
                    $jsonData = json_encode($data);
                    if ($zuid) {
                        //db 成功
                        $logger->db_que_db('dq_2wuserinfo_'.$frequency,'insert',$jsonData,'success');
                        //记录进入点赞程序的用户
                        $user = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='" . $openid . "'");
                        $ip = $this->get_client_ip();
                        $client = strtolower($_SERVER['HTTP_USER_AGENT']);
                        //如果该用户没有信息 插入记录
                        if (empty($user)) {
                            $userInfo = array(
                                'openid' => $openid,
                                'client' => $client,
                                'updatetime' => time(),
                                'ip' => $ip
                            );
                            $data = $userInfo;

                            $uid = pdo_insert('dq_zan_user_'.$frequency, $userInfo);

                            $jsonlog = json_encode($userInfo);

                            if($uid){
                                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'success');
                            }else{
                                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'fail');
                            }
                        }
                        $shareId = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='" . $openid . "'");
                        echo json_encode(array('status' => 1, 'msg' => '成功', 'data' => array('zuid' => $zuid, 'shareid' => $shareId['id'],'way' => $way)));
                    } else {
                        $logger->db_que_db('dq_2wuserinfo','insert',$jsonData,'fail');
                        echo json_encode(array('status' => 0, 'msg' => '失败'));
                    }
                }
            }
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

    //赞入口 收录用户信息的接口 队列触发
    public function doMobileApiZanEntry(){
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET');
        $frequency = $this->doMobilefindfrequency();
        $userInfo = $_POST;
        $jsondate = json_encode($userInfo);

        global $_W;
        $openId = $_W['openid'];
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();

        //出队日志
        $logger->info_que_pop('zanentry',$jsondate);
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $jsonlog = json_encode($userInfo);
            $logger->debug_fun(__FUNCTION__);
            $logger->debug_api(__FUNCTION__,$jsonlog);
        }

        $user = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='".$openId."'");
        if(empty($user)){

            $uid = pdo_insert('dq_zan_user_'.$frequency, $userInfo);
            $jsonlog = json_encode($userInfo);
            if($uid){
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'success');
            }else{
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'fail');
            }
        }
    }

    /**
     * 赞入口
     * 这里需要做队列 繁忙处理
     */
    public function doMobileZanEntry(){
        $this->ZanFilter();
        $frequency = $this->doMobilefindfrequency();
        $boci = htmlspecialchars($_GET['boci']);
        $active = array(
            1,2
        );
        //活动次数判断
        if(!in_array($boci,$active)){
            include $this->template('fourYears2w/resultall');
            exit();
        }
        if($boci != $frequency){
            include $this->template('fourYears2w/resultall');
            exit();
        }
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_fun(__FUNCTION__);
        }
        global $_W;
        $openid = $_W['openid'];
        $way = htmlspecialchars($_GET['ADTAG']);
        $shareid = isset($_GET['shareid'])?$_GET['shareid']:'';
        if(empty($openid)){
            $this->quePage();
            exit();
        }
        //记录进入点赞程序的用户
        $user = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='".$openid."'");
        $ip = $this->get_client_ip();
        $client = strtolower($_SERVER['HTTP_USER_AGENT']);
        /*******队列开始 用于处理插入用户信息 将一堆人挡在外面，但是保存他们的信息，在后台进行异步处理*********/
        $quelist = 'zanentry';
        $queuer = new Queue();
        //如果队列大于1000直接不入队
        if(empty($user)){
            if($queuer->tool->getListSize($quelist)>1500){
                $this->quePage();
                exit();
            }
        }
        //如果该用户没有信息 插入记录
        if(empty($user)){
            $userInfo = array(
                'openid'=>$openid,
                'client'=>$client,
                'updatetime'=>time(),
                'ip'=>$ip
            );
            $data = $userInfo;
            $queuer->producter->product($quelist,$data);
            //开启状态
            if($debugsys == 1){
                //debug级别日志
                $logger->debug_fun(__FUNCTION__);
                $logger->debug_que_push($quelist,json_encode($data));
            }
            $logger->info_que_push($quelist,json_encode($data));
            //下面的行为 以接口的形式编写 让守护进程的PHP程序监听调用
        }
        //只允许1000个人在整个队列里 其他人排队
        if($queuer->tool->getListSize($quelist)>1800){
            //显示排队过程
            $logger->info_que_ing($quelist,json_encode($data),'ll');
            $this->quePage();
        }else{
            $user = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='".$openid."'");
            //如果后台进程还未能处理此队列 那么告诉用户还处于排队中
            if(empty($user)){

                $logger->info_que_ing($quelist,json_encode($data),'bf');
                //排队页面
                $this->quePage();
            }else{
                //获取此链接的ZUID
                $zuid = $_GET['zuid'];
                //查询链接创建者信息
                $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
                $name = $zuserInfo['name'];
                if(empty($zuserInfo) || $zuid == ''){
                    $msg = array(
                        'name'=>'非法连接',
                        'msg'=>'此助力链接非法 未录入信息'
                    );
                    $jsonMsg = json_encode($msg);
                    //echo "此助力链接非法 未录入信息";
                    include $this->template('fourYears2w/error');
                    exit();
                }else{
                    //如果此助力链接所属者的openid等于当前用户openid 自己的助力页面
                    if($zuserInfo['openid'] == $openid){
                        //如果zuid是当前用户自己的 或者为空 代表是自己的助力页面
                        include $this->template('fourYears2w/dq2017show');
                    }else{
                        include $this->template('fourYears2w/dq2017cheer');
                    }
                }
            }
        }
    }

    /**
     * 微信订阅消息
     */
    public function doMobileSubscribeNow(){
        $url = 'wx.cnsaga.com';
        $unurl =urlencode($url);
        $realUrl = 'https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=wxc5c898f251c5fa05&scene=1000&template_id=mZXHWKWbnKkDNNQEcfMsZrr_s7bhJCEcugEs9q5c6T8&redirect_url=http%3A%2F%2Fwx.cnsaga.com&reserved=test#wechat_redirect';
        echo "<script>window.location.href='".$realUrl."'</script>";
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
                'message'=>'成功',
            );
            echo json_encode($return);
        }else{
            $return  = array(
                'status'=>0,
                'message'=>'此人未参与活动！',
            );
            echo json_encode($return);
        }
    }


    public function rankingControl(){
        $luckcount = pdo_fetch("SELECT * FROM ims_dq_sys Where dq_option='luckcount'");
        $zhongjiang = $luckcount['value'];
        $control = array(
            'zhongjiang'=>$zhongjiang,
        );
        return $control;
    }

    public function doMobileZanRankingList(){
        $frequency = $this->doMobilefindfrequency();
        $control =  $this->rankingControl();
        $zhongjiang = $control['zhongjiang'];
        $zuid = $_POST['zuid'];
        $zhongjiajiuequ =$zhongjiang;
        $myZan = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
        $frontMy = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount>".$myZan['zcount']);
        $equalMy = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount=".$myZan['zcount']." and update_time<".$myZan['update_time']);
        if($equalMy['counts']!=0){
            $frontMyCounts = $frontMy['counts'] + $equalMy['counts'];
        }else{
            $frontMyCounts = $frontMy['counts'];
        }
        if($frontMyCounts >=$zhongjiajiuequ ){
            $myRank = $frontMyCounts+1;
            $myRankThink = $myRank;
            $quanburenshureal = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency);
            $quanburenshu = $quanburenshureal['counts'];
            $equalMyChao = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount=".$myZan['zcount']." and update_time>".$myZan['update_time']);
            $behindMy = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount<".$myZan['zcount']);
            $chaoguo = $behindMy['counts']+$equalMyChao['counts'];
            $suanchulai = $chaoguo/$quanburenshu;
            $weizhongjiazhanbi = round($suanchulai,2);
            if($weizhongjiazhanbi == 0){
                $weizhongjiazhanbi = 0.1;
            }
            $return = array(
                'status'=>0,
                'myzan'=>$myZan['zcount'],
                'weizhongjiangzhanbi'=>$weizhongjiazhanbi,
                'chaoguo'=>$chaoguo,
                'myrank'=>$myRankThink,
            );
            return json_encode($return);
        }else{
            $myRank = $frontMyCounts+1;
            $myRankThink = $myRank ;
            $quanburenshureal = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency);
            $quanburenshu = $quanburenshureal['counts'];
            $equalMyChao = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount=".$myZan['zcount']." and update_time>".$myZan['update_time']);
            $behindMy = pdo_fetch("SELECT count(*) as counts FROM ims_dq_2wuserinfo_".$frequency." where zcount<".$myZan['zcount']);
            $chaoguo = $behindMy['counts']+$equalMyChao['counts'];
            $suanchulai = $chaoguo/$quanburenshu;
            $zhangjiangzhanbi = round($suanchulai,2);
            if($zhangjiangzhanbi==0){
                $zhangjiangzhanbi = 0.1;
            }
            $return = array(
                'status'=>1,
                'myzan'=>$myZan['zcount'],
                'chaoguo'=>$chaoguo,
                'zhongjiangzhanbi'=>$zhangjiangzhanbi,
                'myrank'=>$myRankThink,
            );
            return json_encode($return);
        }
    }

    public function doMobileZanShareUrl()
    {
        $frequency = $this->doMobilefindfrequency();
        //debug开关
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];

        $logger = new ActLog();
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_fun(__FUNCTION__);
        }
        global $_W;
        $openId = $_W['openid'];
        //转发ID
        $zuid = htmlspecialchars($_POST['zuid']);
        $url= $_POST['url'];
        $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='" . $zuid . "'");
        if(empty($zuserInfo)){
            $return = array(
                'status' => 0,
                'message' => '此助力链接非法，未录入信息'
            );
            $msg = array(
                'name'=>'非法连接',
                'msg'=>'此助力链接非法 未录入信息'
            );
            $jsonMsg = json_encode($msg);
            echo json_encode($return);
            exit();
        }
        //当前用户ID
        $userId = pdo_fetch("SELECT id FROM ims_dq_zan_user_".$frequency." Where openid='" . $openId . "'");
        $shared = pdo_fetch("SELECT * FROM ims_dq_zan_share_".$frequency." Where zuid='" . $zuid . "' and shareid='" . $userId['id'] . "'");
        if(!empty($shared)){
            $shareData = array(
                'count' => $shared['count'] + 1
            );
            $result = pdo_update('dq_zan_share_'.$frequency, $shareData, array('zuid' => $zuid,'shareid'=>$userId['id']     ));
        }else{

            if($zuserInfo['openid'] == $openId){
                $isself = 1;
            }else{
                $isself = 0;
            }
            $shareInfo = array(
                'zuid' =>$zuid,
                'shareid' => $userId['id'],
                'count' =>'1',
                'isself'=>$isself,
                'create_time'=>time(),
                'update_time'=>time(),
                'zopenid'=>$zuserInfo['openid'],
                'sopenid'=>$openId
            );
            $sres = pdo_insert('dq_zan_share_'.$frequency, $shareInfo);
            $jsonlog = json_encode($shareInfo);
            if($sres){
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'success');
            }else{
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'fail');
            }
        }
        $urlInfo = pdo_fetch("SELECT * FROM ims_dq_zan_url_".$frequency." Where url='" . $url . "'");
        if (empty($urlInfo)) {
            if($zuserInfo['openid'] == $openId){
                $isself = 1;
            }else{
                $isself = 0;
            }
            $urlData = array(
                'zuid' =>$zuid,
                'url' => $url,
                'update_time'=>time(),
                'create_time'=>time(),
                'isself'=>$isself,
                'zopenid'=>$zuserInfo['openid']
            );
            $uid = pdo_insert('dq_zan_url_'.$frequency, $urlData);
            $jsonlog = json_encode($urlData);
            if($uid){
                $return = array(
                    'status' => 1,
                    'shareid'=>$userId['id'],
                    'message' => '转发成功'
                );
                echo json_encode($return);
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'success');
            }else{
                $logger->db_que_db('dq_zan_user_'.$frequency,'insert',$jsonlog,'fail');
            }
        } else {
            $urlShared = array(
                'sharecount' => $urlInfo['sharecount'] + 1,
                'create_time'=>time(),
            );

            $result = pdo_update('dq_zan_url_'.$frequency, $urlShared, array('id' => $urlInfo['id']));
            $jsonlog = json_encode($urlShared);
            if($result) {
                $logger->db_que_db('dq_zan_url_'.$frequency,'update',$jsonlog,'success');
                $return = array(
                    'status' => 1,
                    'shareid'=>$userId['id'],
                    'message' => '转发成功'
                );
                echo json_encode($return);
            }else{
                $logger->db_que_db('dq_zan_url_'.$frequency,'update',$jsonlog,'fail');
            }
        }
    }

    /**
     * 赞行为接口
     */
    public function doMobileZanFun(){
        $frequency = $this->doMobilefindfrequency();
        $clientip = $this->get_client_ip();
        //debug开关
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Wherse dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        /**
         * 黑名单IP
         */
        $issbip = pdo_fetch("SELECT sbip FROM ims_dq_sbip Where sbip='" . $clientip . "'");

        if(!empty($issbip)){
            $logger->info_user_warning('300黑名单IP 不让入队列 ip:'.$clientip);

            $return = array(
                'status'=>1,
                'message'=>'助力成功'
            );
            echo json_encode($return);
            exit();
        }
        $ipcount = pdo_fetch("SELECT count(clientip) as ipcount FROM ims_dq_zan_log_".$frequency." Where clientip='" . $clientip . "'");
        if($ipcount['ipcount']>=300){
            $return = array(
                'status'=>1,
                'message'=>'助力成功'
            );
            $logData = array(
                'sbip'=>$clientip,
            );
            $logId = pdo_insert('ims_dq_sbip', $logData);
            if($logId){
                $logger->info_user_warning('此IP大于等于300此 进黑名单IP ip:'.$clientip);
            }
            //入黑名单
            //$logger->info_user_warning('>300ip不让入队列 入黑名单 ip:'.$clientip);
            echo json_encode($return);
            exit();
        }
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_fun(__FUNCTION__);
        }
        global $_W;
        $openId = $_W['openid'];
        $zuid = htmlspecialchars($_POST['zuid']);
        $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
        //如果当前用户的openid 等于 该zuid所对应的openid
        if($zuserInfo['openid'] == $openId){
            $return = array(
                'status'=>0,
                'message'=>'自己不能给自己助力！'
            );
            echo json_encode($return);
        }else{
            $isZan = pdo_fetch("SELECT * FROM ims_dq_zan_log_".$frequency." Where zopenid='".$zuserInfo['openid']."' and hopenid='".$openId."' limit 1");

            if(!empty($isZan)){
                $return = array(
                    'status'=>0,
                    'message'=>'您已经为此人助力过了！'
                );
                $msg = array(
                    'name'=>'已经助力',
                    'msg'=>'您已经为此人助力过了'
                );
                echo json_encode($return);

                $dataInfo = array(
                    'openid'=>$openId,
                    'zuid'=>$zuid,
                    'url'=>$_POST['url'],
                    'clientip'=>$clientip

                );
                $data = $dataInfo;
                $quelist = 'dqzan';
                $queuer = new Queue();
                $queuer->producter->product($quelist,$data);
                if($debugsys == 1){
                    //debug级别日志
                    $logger->debug_que_push($quelist,json_encode($data));
                }
                $logger->info_que_push($quelist,json_encode($data));
                $return = array(
                    'status'=>1,
                    'message'=>'助力成功'
                );
                echo json_encode($return);
            }
        }
    }

    //队列接口 ApiZanFun
    public function doMobileApiZanFun(){
        $debugsysInfo = pdo_fetch("SELECT value FROM ims_dq_sys Where dq_option='debug'");
        $debugsys = $debugsysInfo['value'];
        $logger = new ActLog();
        //过滤 获取当前活动波次
        $frequency = $this->doMobilefindfrequency();
        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_fun(__FUNCTION__);

        }
        $info = $_POST;

        $infoData = json_encode($info);
        //出队日志
        $logger->info_que_pop('dqzan',$infoData);

        //开启状态
        if($debugsys == 1){
            //debug级别日志
            $logger->debug_api(__FUNCTION__,json_encode($info));
        }
        //拿到队列信息
        $openId = $info['openid'];
        $zuid = $info['zuid'];
        $url = $info['url'];
        $clientip = $info['clientip'];

        $huidInfos = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='".$openId."'");
        //赞日志记录
        if($clientip == ''){
            $clientip = $huidInfos['ip'];
        }

        $zuserInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='" . $zuid . "'");
        $isZan = pdo_fetch("SELECT * FROM ims_dq_zan_log_".$frequency." Where zopenid='".$zuserInfo['openid']."' and hopenid='".$openId."'");

        $ipcount = pdo_fetch("SELECT count(clientip) as ipcount FROM ims_dq_zan_log_".$frequency." Where clientip='" . $clientip . "'");

        $logdata = array(
            'zopenid'=>$zuserInfo['openid'],
            'hopenid'=>$openId,
            'clientip'=>$clientip,
            'ipcount'=>$ipcount['ipcount']

        );

        if($ipcount['ipcount']>=300){
            $logger->db_que_db('dq_zan_log_'.$frequency,'del','>300'.json_encode($logdata),'success');
            exit();
        }
        if(!empty($isZan)){
            $logger->db_que_db('dq_zan_log_'.$frequency,'del','repeat data'.json_encode($logdata),'success');
            exit();
        }

        $urlInfo = pdo_fetch("SELECT * FROM ims_dq_zan_url_".$frequency." Where url='".$url."' and status=1");
        $urlData = array(
            'zcount'=>$urlInfo['zcount']+1
        );
        //更新该URL赞量 由于微信总是抽风 所以不写在事务里 单独更新
        $urlRes = pdo_update('dq_zan_url_'.$frequency, $urlData, array('id' => $urlInfo['id']));
        try{
            pdo_begin();
            //查询此人的被赞信息
            $zanInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
            //更新助力记录

            $zanC = $zanInfo['zcount']+1;
            $zanData = array(
                'zcount'=> $zanC
            );
            $userRes = pdo_update('dq_2wuserinfo_'.$frequency, $zanData, array('id' => $zuid));

            if ($userRes == false) {
                throw new Exception('更新用户记录失败');
                $jsonlog = json_encode($zanData);
                $logger->db_que_db('dq_zan_log_'.$frequency,'insert',$jsonlog,'fail');
            }

            $sql = "SELECT * FROM ims_dq_zan_url_".$frequency." Where url='".$url."' and status=1";

            $huidInfo = pdo_fetch("SELECT * FROM ims_dq_zan_user_".$frequency." Where openid='".$openId."'");

            if($clientip == ''){
                $clientip = $huidInfo['ip'];
            }
            $logData = array(
                'zuid'=>$zuid,
                'hopenid'=>$openId,
                'zopenid'=>$zanInfo['openid'],
                'huid'=>$huidInfo['id'],
                'create_time'=>time(),
                'update_time'=>time(),
                'clientip'=>$clientip,
            );
            $logId = pdo_insert('dq_zan_log_'.$frequency, $logData);
            if ($logId == false) {
                throw new Exception('记录赞日志失败');
                $jsonlog = json_encode($logData);
                $logger->db_que_db('dq_zan_log_'.$frequency,'insert',$jsonlog,'fail');
            }
            pdo_commit();

            $return = array(
                'status'=>1,
                'message'=>'助力成功'
            );
            echo json_encode($return);

        }catch (Exception $e) {
            $return = array(
                'status'=>0,
                'message'=>$e->getMessage(),
            );
            echo json_encode($return);
            pdo_rollback();
            exit();
        }
    }
    public function istuhao($zuid){
        $frequency = $this->doMobilefindfrequency();
        $userInfo = pdo_fetch("SELECT * FROM ims_dq_2wuserinfo_".$frequency." Where id='".$zuid."'");
        $tag = '';
        //如果trip不等于0 先减trip
        if($userInfo['trip']!=0){
            //如果 trip 等于0 而bank不等于0 减bank
            $tag = 'trip';
            return $tag;
        }elseif ($userInfo['bank']!=0){
            //如果trip 等于0 而且 bank 也等于0就减car
            $tag = 'bank';
            return $tag;
        }elseif ($userInfo['car']!=0){
            $tag = 'car';
            return $tag;
        }else{
            return false;
        }

    }
    public function doMobilePhoneJudge()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET');
        $yzm=$_POST['yzm'];
        $tel = $_POST['userPhone'];

        if (!empty($tel) && !empty($yzm)) {
            $sql="SELECT *  FROM `ims_core_cache` WHERE `key` LIKE '".$tel."%' limit 0,1 ";
            $result = pdo_fetch($sql);
            //$data['tel'] = $result[0]['key'];
            $data= unserialize($result['value']);
            if($data['code'] == $yzm)
            {
                $response=array('status'=>0,'errmsg'=>'ok');
            }else{
                $response=array('status'=>2,'errmsg'=>'验证码错误');
            }
        }else{
            $response=array('status'=>1,'errmsg'=>'手机号与验证码都不能为空');
        }
        echo json_encode($response);
    }
    //短信接口
    public function sendmsg($tel, $msg)
    {
        load()->func('communication');
        $msg    = iconv("UTF-8", "gb2312//IGNORE", $msg);
        $status = ihttp_post('http://58.83.147.92:8080/qxt/smssenderv2', array('user'     => 'zs_saige',
            'password' => strtolower(md5('521748')),
            'tele'     => $tel,
            'msg'      => $msg
        ));
        if ($status['code'] == "200" && $status['status'] == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    public  function doMobileSendPhone(){
        global $_GPC;
        load()->func('cache.mysql');
        $tel  = $_GPC['phone'];
        $code = random(6,true);
        $msg  = "【西安赛格】短信验证码为：" . $code . ",请您在30分钟内完成,如非本人操作,请忽略。退订回TD";
        $preg = preg_match('/^\d{11}$/',$tel);
        if(!$preg  || empty($tel)){
            echo json_encode(array('status'=>0,'text'=>'手机号必填或格式不正确'));
            exit();
        }
        $cache = cache_load($_GPC['phone']);
        if(empty($cache)){
            cache_write($_GPC['phone'],array('code'=>$code,'create_time'=>(TIMESTAMP+60)));
            $this->sendmsg($tel,$msg);
            echo json_encode(array('status'=>1,'text'=>'短信已发送，请注意查收'));
            exit();
        }else{
            //已过期
            if($cache['create_time'] < TIMESTAMP){
                cache_write($_GPC['phone'],array('code'=>$code,'create_time'=>(TIMESTAMP+60)));
                $this->sendmsg($tel,$msg);
                echo json_encode(array('status'=>1,'text'=>'短信已发送，请注意查收'));exit();
            }else{
                //未过期
                echo json_encode(array('status'=>2,'text'=>'您好，验证码时效为10分钟，请直接填写现有验证码。'));exit();
            }
        }
    }
    public function doMobileDq2017(){
        include $this->template('fourYears2w/dq2017');
    }
    public function doMobileDq2018(){
        include $this->template('fourYears2w/dq2017cheer');
    }
}