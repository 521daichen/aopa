<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('article');

$dos = array('category_post', 'category', 'category_del', 'list', 'post', 'batch_post', 'del', 'displaysetting');
$do = in_array($do, $dos) ? $do : 'list';
permission_check_account_user('system_article_notice');

if ($do == 'category_post') {
	$_W['page']['title'] = '公告分类-公告管理-文章-系统管理';
	if (checksubmit('submit')) {
		$i = 0;
		if (!empty($_GPC['title'])) {
			foreach ($_GPC['title'] as $k => $v) {
				$title = trim($v);
				if  (empty($title)) {
					continue;
				}
				$data = array(
					'title' => $title,
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'type' => 'notice',
				);
				pdo_insert('article_category', $data);
				$i++;
			}
		}
		itoast('添加公告分类成功', url('article/notice/category'), 'success');
	}
	template('article/notice-category-post');
}

if ($do == 'category') {
	$_W['page']['title'] = '分类列表-公告分类-公告管理-文章-系统管理';
	if (checksubmit('submit')) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('article_category', $data, array('id' => intval($v)));
			}
			itoast('修改公告分类成功', referer(), 'success');
		}
	}
	$data = pdo_fetchall('SELECT * FROM ' . tablename('article_category') . ' WHERE type = :type ORDER BY displayorder DESC', array(':type' => 'notice'));
	template('article/notice-category');
}

if ($do == 'category_del') {
	$id = intval($_GPC['id']);
	pdo_delete('article_category', array('id' => $id,'type' => 'notice'));
	pdo_delete('article_notice', array('cateid' => $id));
	itoast('删除公告分类成功', referer(), 'success');
}

if ($do == 'post') {
	$_W['page']['title'] = '编辑公告-公告管理-文章-系统管理';
	$id = intval($_GPC['id']);
	$notice = pdo_fetch('SELECT * FROM ' . tablename('article_notice') . ' WHERE id = :id', array(':id' => $id));
	if (empty($notice)) {
		$notice = array(
			'is_display' => 1,
			'is_show_home' => 1,
			'group' => array('vice_founder' => array(), 'normal' => array())
		);
	} else {
		$notice['style'] = iunserializer($notice['style']);
		$notice['group'] = empty($notice['group']) ? array('vice_founder' => array(), 'normal' => array()) : iunserializer($notice['group']);
	}
	$user_groups = table('group')->groupList();
	$user_vice_founder_groups = table('group')->groupList(true);
	if (checksubmit()) {
		$title = trim($_GPC['title']) ? safe_gpc_string($_GPC['title']) : itoast('公告标题不能为空', '', 'error');
		$cateid = intval($_GPC['cateid']) ? intval($_GPC['cateid']) : itoast('公告分类不能为空', '', 'error');
		$content = trim($_GPC['content']) ? safe_gpc_string($_GPC['content']) : itoast('公告内容不能为空', '', 'error');
		$style = array('color' => safe_gpc_string($_GPC['style']['color']), 'bold' => intval($_GPC['style']['bold']));
		$group = $vice_group = array();
		if (!empty($_GPC['group']) && is_array($_GPC['group'])) {
			foreach ($_GPC['group'] as $value) {
				if (!is_numeric($value)) {
					itoast('参数错误！');
				}
				$group[] = intval($value);
			}
		}
		if (!empty($_GPC['vice_founder_group']) && is_array($_GPC['vice_founder_group'])) {
			foreach ($_GPC['vice_founder_group'] as $vice_founder_value) {
				if (!is_numeric($vice_founder_value)) {
					itoast('参数错误！');
				}
				$vice_group[] = intval($vice_founder_value);
			}
		}
		if (empty($group) && empty($vice_group)) {
			$group = '';
		} else {
			$group = iserializer(array('normal' => $group, 'vice_founder' => $vice_group));
		}
		$data = array(
			'title' => $title,
			'cateid' => $cateid,
			'content' => htmlspecialchars_decode($content),
			'displayorder' => intval($_GPC['displayorder']),
			'click' => intval($_GPC['click']),
			'is_display' => intval($_GPC['is_display']),
			'is_show_home' => intval($_GPC['is_show_home']),
			'createtime' => TIMESTAMP,
			'style' => iserializer($style),
			'group' => $group,
		);

		if (!empty($notice['id'])) {
			pdo_update('article_notice', $data, array('id' => $id));
		} else {
			pdo_insert('article_notice', $data);
		}
		itoast('编辑公告成功', url('article/notice/list'), 'success');
	}
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('article_category') . ' WHERE type = :type ORDER BY displayorder DESC', array(':type' => 'notice'));
	template('article/notice-post');
}

if ($do == 'list') {
	$_W['page']['title'] = '公告列表-公告管理-文章-系统管理';
	$condition = ' WHERE 1';
	$cateid = intval($_GPC['cateid']);
	$createtime = intval($_GPC['createtime']);
	$search_title = trim($_GPC['title']);
	$params = array();
	if ($cateid > 0) {
		$condition .= ' AND cateid = :cateid';
		$params[':cateid'] = $cateid;
	}
	if ($createtime > 0) {
		$condition .= ' AND createtime >= :createtime';
		$params[':createtime'] = strtotime("-{$createtime} days");
	}
	if (!empty($search_title)) {
		$condition .= " AND title LIKE :title";
		$params[':title'] = "%{$search_title}%";
	}
	$order = !empty($_W['setting']['notice_display']) ? $_W['setting']['notice_display'] : 'displayorder';

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$sql = 'SELECT * FROM ' . tablename('article_notice') . $condition . " ORDER BY " . $order . " DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize;
	$notices = pdo_fetchall($sql, $params);
	foreach ($notices as &$notice_value) {
		if (!empty($notice_value)) {
			$notice_value['style'] = iunserializer($notice_value['style']);
		}
	}
	unset($notice_value);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('article_notice') . $condition, $params);
	$pager = pagination($total, $pindex, $psize);

	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('article_category') . ' WHERE type = :type ORDER BY displayorder DESC', array(':type' => 'notice'), 'id');
	template('article/notice');
}

if ($do == 'batch_post') {
	if (checksubmit()) {
		if (!empty($_GPC['ids'])) {
			foreach ($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'click' => intval($_GPC['click'][$k]),
				);
				pdo_update('article_notice', $data, array('id' => intval($v)));
			}
			itoast('编辑公告列表成功', referer(), 'success');
		}
	}
}

if ($do == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('article_notice', array('id' => $id));
	pdo_delete('article_unread_notice', array('notice_id' => $id));
	itoast('删除公告成功', referer(), 'success');
}

if ($do == 'displaysetting') {
	$setting = trim($_GPC['setting']);
	$data = $setting == 'createtime' ? 'createtime' : 'displayorder';
	setting_save($data, 'notice_display');
	itoast('更改成功！', referer(), 'success');
}
