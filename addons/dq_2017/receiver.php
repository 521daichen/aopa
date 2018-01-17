<?php
/**
 * 活动模块订阅器
 *
 * @author dv
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Dq_2017ModuleReceiver extends WeModuleReceiver {
	public function receive() {
        load()->func('logging');

	    global $_W;
		$type = $this->message['type'];
        $str = $this->message['event']."  testcontent:".$this->message['fromusername']."   ".$this->message['usecardcode'];
        logging_run($type,"test");
		//用户领卡事件
        if($this->message['event'] == 'user_get_card')
        {
            $fromuser = $this->message['fromusername'];
            $get_time = $this->message['createtime'];
            $code = $this->message['usercardcode'];
            $cardid = $this->message['cardid'];

            $data = array(
                'openid' => $fromuser,
                'get_time' => $get_time,
                'code'=>$code,
                'cardid' =>$cardid
            );
            $insert = pdo_insert('mc_act_car4card_cardcode', $data);
        }
	}
}