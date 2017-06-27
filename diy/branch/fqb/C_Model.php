<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */

// 文档状态
// 1 ~ 8 审核流程
// 0 被退回
// 9 正常
// 10 回收站


class C_Model extends CI_Model {

    public $mdir; // 当前模块目录
    public $link; // 当前模型的数据库对象
    public $where; // 管理角色组数据筛选条件
    public $prefix; // 主表名称（其他表的前缀部分）
    public $share_prefix; // 共享模块主表名称（其他表的前缀部分）
    public $cache_file; // 数据缓存临时文件名称

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

        $this->mdir = $this->dir ? $this->dir : MOD_DIR;
        $this->prefix = $this->db->dbprefix(SITE_ID.'_'.$this->mdir);
        $this->share_prefix = $this->db->dbprefix(SITE_ID.'_share');
        // 管理角色组数据筛选条件
        if (IS_ADMIN && $this->admin['adminid'] > 1) {
            $catid = array();
            $category = $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'category');
            if ($category) {
                foreach ($category as $c) {
                    // 具有管理权限的栏目id集合
                    !$c['child'] && $c['setting']['admin'][$this->admin['adminid']]['show'] == 1 && $catid[] = $c['id'];
                }
                $this->where = $catid ? '`catid` IN ('.implode(',', $catid).')' : '`catid` = -1';
                unset($category);
            }
        }
    }

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	array	$param	条件参数
     * @return	array	
     */
    private function _where(&$select, $data) {

        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if ($t == '') {
                    unset($data[$i]);
                }
            }
            unset($_GET['page']);
        }

        // 存在search参数时，读取缓存文件
        if ($data) {
            if (isset($data['keyword']) && $data['keyword'] != '' && $data['field']) {
                $field = $this->field ? $this->field : $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'field');
                if ($data['field'] == 'id') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int) $i;
                    }
                    $select->where_in('id', $id);
                } elseif ($field[$data['field']]['fieldtype'] == 'Linkage'
                    && $field[$data['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($data['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$data['field']]['setting']['option']['linkage'], (int)$data['keyword'], 0, 'childids');
                        $link && $select->where($data['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->ci->get_cache('linkid-'.SITE_ID, $field[$data['field']]['setting']['option']['linkage']);
                        $id && $select->where($data['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$data['keyword'].'%")');
                    }
                } else {
                    $select->like($data['field'], urldecode($data['keyword']));
                }
            }
            // 时间搜索
            if (isset($data['start']) && $data['start']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end'] ? $data['end'] : SYS_TIME));
                $data['start'] = strtotime(date('Y-m-d 00:00:00', $data['start']));
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            } elseif (isset($data['end']) && $data['end']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end']));
                $data['start'] = 0;
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            }
        }

        isset($data['flag']) && $select->where('flag', $data['flag']);

        if (isset($data['catid']) && $data['catid']) {
            $cat = $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'category', $data['catid']);
            $cat['child'] ? $select->where_in('catid', explode(',', $cat['childids'])) : $select->where('catid', $data['catid']);
        }

        $this->where && $select->where($this->where);

        return $data;
    }

    /**
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array	
     */
    public function limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $_param = $this->_where($select, $param);
            $_param && $select->order_by('id');
            $data = $select->get(isset($param['flag']) ? $this->prefix.'_flag' : $this->prefix)->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param = $this->_where($select, $param);
        $_order = dr_get_order_string($this->input->get('order'), $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'order'));
        if (isset($_param['flag'])) {
            $in = array();
            $ids = $select->select('id')->get($this->prefix.'_flag')->result_array();
            if ($ids) {
                foreach ($ids as $t) {
                    $in[] = $t['id'];
                }
                $data = $this->db->where_in('id', $in)->order_by($_order)->get($this->prefix)->result_array();
            }
        } else {
            $data = $select->order_by($_order)->get($this->prefix)->result_array();
        }
        $_param['total'] = $total;
        $_param['order'] = $_order;

        return array($data, $_param);
    }

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	array	$param	条件参数
     * @param	intval	$cid	文档id
     * @return	array	
     */
    private function _extend_where(&$select, $data, $cid) {


        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if ($t == '') {
                    unset($data[$i]);
                }
            }
            unset($_GET['page']);
        }

        // 存在search参数时，读取缓存文件
        if ($data) {
            if (isset($data['keyword']) && $data['keyword'] != '' && $data['field']) {
                $field = $this->field ? $this->field : $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'extend');
                if ($data['field'] == 'id') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int) $i;
                    }
                    $select->where_in('id', $id);
                } elseif ($field[$data['field']]['fieldtype'] == 'Linkage'
                    && $field[$data['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($data['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$data['field']]['setting']['option']['linkage'], (int)$data['keyword'], 0, 'childids');
                        $link && $select->where($data['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->ci->get_cache('linkid-'.SITE_ID, $field[$data['field']]['setting']['option']['linkage']);
                        $id && $select->where($data['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$data['keyword'].'%")');
                    }
                } else {
                    $select->like($data['field'], urldecode($data['keyword']));
                }
            }
            // 时间搜索
            if (isset($data['start']) && $data['start']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end'] ? $data['end'] : SYS_TIME));
                $data['start'] = strtotime(date('Y-m-d 00:00:00', $data['start']));
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            } elseif (isset($data['end']) && $data['end']) {
                $data['end'] = strtotime(date('Y-m-d 23:59:59', $data['end']));
                $data['start'] = 0;
                $select->where('updatetime BETWEEN '.$data['start'].' AND '.$data['end']);
            }
        }

        $cid && $select->where('cid', (int)$cid);

        return $data;
    }

    /**
     * 数据分页显示
     *
     * @param	intval	$cid	文档id
     * @param	array	$param	条件参数
     * @return	array	
     */
    public function extend_limit_page($cid, $param) {

        if (IS_POST) {
            $page = 1;
            $total = 0;
        } else {
            $page = max((int) $this->input->get('page'), 1);
            $total = (int) $this->input->get('total');
        }

        if (!$total) {
            $select = $this->db->select('count(*) as total');
            $_param = $this->_extend_where($select, $param, $cid);
            $_param && $select->order_by('id');
            $data = $select->get($this->prefix.'_extend')->row_array();
            unset($select);
            $total = (int) $data['total'];
            if (!$total) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param = $this->_extend_where($select, $param, $cid);
        $_order = dr_get_order_string($this->input->get('order'), $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'order_extend'));
        $data = $select->order_by($_order)->get($this->prefix.'_extend')->result_array();
        $_param['total'] = $total;

        return array($data, $_param);
    }
	

    /**
     * 发布前，先生成一个索引数据
     *
     * @param	array	$data
     * @return	array	
     */
    public function index($data) {

        if (defined('IS_SHARE') && IS_SHARE) {
            // 共享模块
            $this->db->insert($this->share_prefix.'_index', array(
                'mid' => $this->mdir,
            ));
            $id = $this->db->insert_id();
            $this->db->insert($this->prefix.'_index', array(
                'id' => $id,
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status'],
                'inputtime' => $data[1]['inputtime'],
            ));
            return $id;
        } else {
            // 独立模块
            $this->db->insert($this->prefix.'_index', array(
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status'],
                'inputtime' => $data[1]['inputtime'],
            ));
            return $this->db->insert_id();
        }
    }

    /**
     * 发布
     *
     * @param	array	$data
     * @param	string	$syncatid
     * @return	array
     */
    public function add($data, $syncatid = 0) {

        // 发布之前挂钩点
        $this->hooks->call_hook('content_add_before', $data);

        // 生成索引id
        $data[0]['id'] = $data[1]['id'] = $id = $this->index($data);
        $data[0]['uid'] = (int)$data[1]['uid'];
        $data[1]['hits'] = (int)$data[1]['hits'];
        $data[0]['catid'] = (int)$data[1]['catid'];
        $data[1]['comments'] = 0;
        $data[1]['favorites'] = 0;

        if (!$id) {
            return FALSE;
        }
        $field = $this->ci->get_table_field($this->prefix);
        // 兼容性处理 创建同步字
        SYS_UPDATE && !$field['link_id'] && $this->db->query("ALTER TABLE  `{$this->prefix}` ADD  `link_id` INT( 10 ) DEFAULT '0' COMMENT  '同步id'");

        // 副表以5w左右数据量无限分表
        $data[1]['tableid'] = floor($id / 50000);
        // 格式化字段值
        $data = $this->get_content_data($data);
        $data[1]['keywords'] = str_replace(array('，', '、', '；', ';'), ',', $data[1]['keywords']);
        if ($data[1]['status'] >= 9) {
            // 审核通过
            // 判断描述字段的归属
            if (!isset($field['description'])) {
                $data[0]['description'] = $data[1]['description'];
                unset($data[1]['description']);
            }
            $data = $this->replace_category_data($id, $data); // 格式化栏目字段
            $data[1]['url'] = dr_show_url($this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir), $data[1]);
            if (!$this->db->query("SHOW TABLES LIKE '".$this->prefix.'_data_'.$data[1]['tableid']."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($this->prefix.'_data_'.$data[1]['tableid'], 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            $this->db->replace($this->prefix, $data[1]); // 主表
            $this->db->replace($this->prefix.'_data_'.$data[1]['tableid'], $data[0]); // 副表
            isset($data[1]['keywords']) && $data[1]['keywords'] && $this->update_tag($data[1]['keywords']); // 更新tag表
            $this->sns_share($data, 0); // 分享信息
            // 同步其他栏目
            if ($syncatid) {
                $syn = @explode('|', $syncatid);
                if ($syn) {
                    // 更新主表状态主表
                    $this->db->where('id', $id)->update($this->prefix, array('link_id' => -1));
                    foreach ($syn as $cid) {
                        if ($cid && $cid != $data[1]['catid']) {
                            // 插入到同步栏目中
                            $new = $data;
                            $new[1]['catid'] = $cid;
                            $new[1]['link_id'] = $id;
                            $new[1]['tableid'] = 0;
                            $new[1]['id'] = $this->index($new);
                            $this->db->replace($this->prefix, $new[1]); // 主表
                        }
                    }
                }
            }
        } else {
            // 非审核通过状态的文档写入审核表
            $data[0]['is_new'] = 1; // 表示新文章
            $verify = array(
                'id' => (int)$data[1]['id'],
                'uid' => (int)$data[1]['uid'],
                'catid' => (int)$data[1]['catid'],
                'author' => $data[1]['author'],
                'status' => (int)$data[1]['status'],
                'content' => dr_array2string(array_merge($data[0], $data[1])),
                'backuid' => 0,
                'backinfo' => '',
                'inputtime' => $data[1]['inputtime']
            );
            $this->db->replace($this->prefix.'_verify', $verify); // 审核表
            unset($verify);
        }

        // 发布之后挂钩点
        $this->hooks->call_hook('content_add_after', $data);
        $this->hooks->call_hook('syn_content_add', array('syn' => $this->syn_content, 'data' => $data));

        // 发布之后执行的方法
        $this->_add_content($data);

        return $id;
    }

    // 修改
    public function edit($_data, $data, $oid = 0) {

        // 参数判断
        if (!$data || !$_data) {
            return FALSE;
        }

        // 判断是否是同步的数据，主数据栏目保持不变
        if ($_data['link_id'] != 0) {
            // 更新当前同步数据的栏目
            $this->db->where('id', $oid)->update($this->prefix.'_index', array(
                'catid' => $data[1]['catid'],
            ));
            $this->db->where('id', $oid)->update($this->prefix, array(
                'catid' => $data[1]['catid'],
            ));
            $data[1]['catid'] = $data[0]['catid'] = $_data['catid'];
        }

        // 修改之前挂钩点
        $data['edit'] = $_data;
        $data[1]['hits'] = (int)$_data['hits'];
        $data[1]['comments'] = (int)$_data['comments'];
        $data[1]['favorites'] = (int)$_data['favorites'];
        $this->hooks->call_hook('content_edit_before', $data);
        unset($data['edit']);

        // 被退回处理
        if (isset($data[1]['back']) && $this->admin) {
            $backinfo = array(
                'uid' => $this->uid,
                'author' => $this->admin['username'],
                'rolename' => $this->admin['role']['name'],
                'optiontime' => SYS_TIME,
                'backcontent' => $data[1]['back']
            );
            unset($data[1]['back']);
        }
        // 格式化字段值
        $data = $this->get_content_data($data, $_data);
        $data[1]['keywords'] = str_replace(array('，', '、', '；', ';'), ',', $data[1]['keywords']);
        if ($data[1]['status'] >= 9) {
            $field = $this->ci->get_table_field($this->prefix);
            // 判断描述字段的归属
            if (!isset($field['description'])) {
                $data[0]['description'] = $data[1]['description'];
                unset($data[1]['description']);
            }
            // 会员不等时表示在修改会员
            $_uid = intval($_data['uid']);
            $_uid != $data[1]['uid'] && $this->db->where('id', intval($_data['id']))->update($this->prefix.'_index', array(
                'uid' => $_uid,
            ));
            // 分析栏目字段数据
            $data = $this->replace_category_data($_data['id'], $data);
            // 生成url地址
            $data[1]['url'] = dr_show_url($this->ci->get_cache('MODULE-'.SITE_ID.'-'.$this->mdir), array_merge($_data, $data[1]));
            // 更新索引表
            $data[1]['status'] = intval($data[1]['status']);
            $this->db->where('id', $_data['id'])->update($this->prefix.'_index', array(
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status']
            ));
            // 提交为审核通过状态
            $data[1]['id'] = $data[0]['id'] = $_data['id'];
            $data[0]['uid'] = $data[1]['uid'];
            $data[0]['catid'] = $data[1]['catid'];
            // 副表以5w左右数据量无限分表
            $data[1]['tableid'] = $_data['tableid'] ? $_data['tableid'] : floor($_data['id'] / 50000);
            if (!$this->db->query("SHOW TABLES LIKE '".$this->prefix.'_data_'.$data[1]['tableid']."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($this->prefix.'_data_'.$data[1]['tableid'], 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            // 主表更新
            $this->db->where('id', $_data['id'])->count_all_results($this->prefix) ? $this->db->where('id', $_data['id'])->update($this->prefix, $data[1]) : $this->db->replace($this->prefix, $data[1]);
            // 副表
            $this->db->replace($this->prefix.'_data_'.$data[1]['tableid'], $data[0]);
            // 审核表
            $this->db->where('id', $_data['id'])->delete($this->prefix.'_verify');
            // 更新tag表
            isset($data[1]['keywords']) && $data[1]['keywords'] && $this->update_tag($data[1]['keywords']);
            // 同步更新其他同步数据
            if ($_data['link_id'] != 0) {
                $syn = $data[1];
                unset($syn['id'], $syn['link_id'], $syn['catid'], $syn['tableid'], $syn['tableid']);
                $this->db->where('link_id', $_data['id'])->update($this->prefix, $syn);
                // 同步共享表
                //defined('IS_SHARE') && IS_SHARE && $this->db->where('link_id', $_data['id'])->update($this->share_prefix, $syn);
            }
            $this->sns_share($data); // 分享信息
        } else {
            // 检查合并审核数据
            $content = $data[0] ? array_merge($data[0], $data[1]) : $data[1];
            if (!$content) {
                return FALSE;
            }
            // 更新主表
            $this->db->where('id', (int)$_data['id'])->update($this->prefix, array(
                'status' => $data[1]['status']
            ));
            // 更新索引表
            $this->db->where('id', intval($_data['id']))->update($this->prefix.'_index', array(
                'status' => $data[1]['status']
            ));
            // 更新审核表
            $this->db->replace($this->prefix.'_verify', array(
                'id' => (int)$_data['id'],
                'uid' => (int)$data[1]['uid'],
                'catid' => (int)$data[1]['catid'],
                'author' => $data[1]['author'],
                'status' => (int)$data[1]['status'],
                'content' => dr_array2string($content),
                'backuid' => (int) $this->uid,
                'backinfo' => $this->admin ? dr_array2string($backinfo) : '',
                'inputtime' => SYS_TIME
            ));
        }

        $this->ci->clear_cache('hits'. $this->mdir.SITE_ID.$_data['id']);
        $this->ci->clear_cache('show'.$this->mdir.SITE_ID.$_data['id']);
        $this->ci->clear_cache('mshow'.$this->mdir.SITE_ID.$_data['id']);

        // 修改之后挂钩点
        $data['edit'] = $_data;
        $this->hooks->call_hook('content_edit_after', $data);

        // 修改之后执行的方法
        $this->_edit_content($data);

        return $_data['id'];
    }


    /**
     * 发布前，先生成一个索引数据
     *
     * @param	array	$data
     * @return	array
     */
    private function extend_index($data) {

        if (defined('IS_SHARE') && IS_SHARE) {
            // 共享模块
            $this->db->insert($this->share_prefix.'_extend_index', array(
                'mid' => $this->mdir,
            ));
            $id = $this->db->insert_id();
            $this->db->insert($this->prefix.'_extend_index', array(
                'id' => $id,
                'uid' => $data[1]['uid'],
                'cid' => $data[1]['cid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status'],
            ));
            return $id;
        } else {
            // 独立模块
            $this->db->insert($this->prefix . '_extend_index', array(
                'cid' => $data[1]['cid'],
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status'],
            ));

            return $this->db->insert_id();
        }
    }

    /**
     * 发布
     *
     * @param	array	$data
     * @return	array
     */
    public function add_extend($data) {

        // 发布之前挂钩点
        $this->hooks->call_hook('extend_add_before', $data);

        // 生成索引id
        $data[0]['id'] = $data[1]['id'] = $id = $this->extend_index($data);
        $data[0]['uid'] = $data[1]['uid'];
        $data[0]['cid'] = $data[1]['cid'];
        $data[0]['catid'] = $data[1]['catid'];

        if (!$id) {
            return FALSE;
        }

        // 副表以5w左右数据量无限分表
        $data[1]['tableid'] = floor($id / 50000);
        // 格式化字段值
        $data = $this->get_content_extend_data($data);
        if ($data[1]['status'] >= 9) {
            // 审核通过]
            $data[1]['url'] = dr_extend_url($this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir), $data[1]);
            if (!$this->db->query("SHOW TABLES LIKE '".$this->prefix.'_extend_data_'.$data[1]['tableid']."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_extend_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($this->prefix.'_extend_data_'.$data[1]['tableid'], 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            $this->db->replace($this->prefix.'_extend', $data[1]); // 主表
            $this->db->replace($this->prefix.'_extend_data_'.$data[1]['tableid'], $data[0]); // 副表
            // 分享信息
            $data[1]['title'] = trim($this->content['title']).' - '.$data[1]['name'];
            $this->sns_share($data, 1); // 分享信息
            // 更新内容表时间
            $this->db->where('id', (int)$data[1]['cid'])->update($this->prefix, array('updatetime' => SYS_TIME));
            // 审核表
            $this->db->where('id', (int)$data[1]['id'])->delete($this->prefix.'_extend_verify');
        } else {
            $data[0]['is_new'] = 1; // 表示新文章
            $content = array_merge($data[0], $data[1]);
            $content['title'] = $this->content['title'];
            // 非审核通过状态的文档写入审核表
            $this->db->replace($this->prefix.'_extend_verify', array(
                'id' => (int)$data[1]['id'],
                'uid' => (int)$data[1]['uid'],
                'cid' => (int)$data[1]['cid'],
                'catid' => (int)$data[1]['catid'],
                'author' => $data[1]['author'],
                'status' => (int)$data[1]['status'],
                'content' => dr_array2string($content),
                'backuid' => 0,
                'backinfo' => '',
                'inputtime' => $data[1]['inputtime']
            )); // 审核表
        }

        $this->ci->clear_cache('extend'.$this->mdir.SITE_ID.$id);
        $this->ci->clear_cache('mextend'.$this->mdir.SITE_ID.$id);

        // 发布之后挂钩点
        $this->hooks->call_hook('extend_add_after', $data);

        // 发布之后执行的方法
        $this->_add_extend($data);

        return $id;
    }

    // 修改
    public function edit_extend($_data, $data) {

        // 参数判断
        if (!$data || !$_data) {
            return FALSE;
        }

        // 修改之前挂钩点
        $data['edit'] = $_data;
        $this->hooks->call_hook('extend_edit_before', $data);
        unset($data['edit']);

        // 被退回处理
        if (isset($data[1]['back']) && $this->admin) {
            $backinfo = array(
                'uid' => $this->uid,
                'author' => $this->admin['username'],
                'rolename' => $this->admin['role']['name'],
                'optiontime' => SYS_TIME,
                'backcontent' => $data[1]['back']
            );
            unset($data[1]['back']);
        }
        // 格式化字段值
        $data = $this->get_content_extend_data($data, $_data);
        $data[1]['status'] = intval($data[1]['status']);
        if ($data[1]['status'] >= 9) {
            // 生成url地址
            $data[1]['url'] = dr_extend_url($this->ci->get_cache('MODULE-'.SITE_ID.'-'.$this->mdir), array_merge($_data, $data[1]));
            // 更新索引表
            $this->db->where('id', $_data['id'])->update($this->prefix.'_extend_index', array(
                'uid' => $data[1]['uid'],
                'catid' => $data[1]['catid'],
                'status' => $data[1]['status']
            ));
            // 提交为审核通过状态
            $data[1]['id'] = $data[0]['id'] = $_data['id'];
            $data[0]['uid'] = $data[1]['uid'];
            $data[0]['cid'] = $data[1]['cid'];
            $data[0]['catid'] = $data[1]['catid'];
            // 副表以5w左右数据量无限分表
            $data[1]['tableid'] = $_data['tableid'] ? $_data['tableid'] : floor($_data['id'] / 50000);
            if (!$this->db->query("SHOW TABLES LIKE '".$this->prefix.'_extend_data_'.$data[1]['tableid']."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_extend_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($this->prefix.'_extend_data_'.$data[1]['tableid'], 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            // 主表更新
            $this->db->where('id', $_data['id'])->count_all_results($this->prefix.'_extend') ? $this->db->where('id', $_data['id'])->update($this->prefix.'_extend', $data[1]) : $this->db->replace($this->prefix.'_extend', $data[1]);
            // 副表
            $this->db->replace($this->prefix.'_extend_data_'.$data[1]['tableid'], $data[0]);
            // 审核表
            $_data['status'] < 9 && $this->db->where('id', $_data['id'])->delete($this->prefix.'_extend_verify');

            // 更新内容表时间
            $this->db->where('id', $data[1]['cid'])->update($this->prefix, array('updatetime' => SYS_TIME));
            // 分享信息
            $data[1]['title'] = trim($this->content['title']).' - '.$data[1]['name'];
            $this->sns_share($data, 1); // 分享信息
        } else {
            // 检查合并审核数据
            $content = $data[0] ? array_merge($data[0], $data[1]) : $data[1];
            if (!$content) {
                return FALSE;
            }
            $content['title'] = $this->content['title'];
            // 更新主表
            $this->db->where('id', (int)$_data['id'])->update($this->prefix.'_extend', array(
                'status' => $data[1]['status']
            ));
            // 更新索引表
            $this->db->where('id', intval($_data['id']))->update($this->prefix.'_extend_index', array(
                'status' => $data[1]['status']
            ));
            // 更新审核表
            $this->db->replace($this->prefix.'_extend_verify', array(
                'id' => (int)$_data['id'],
                'uid' => (int)$data[1]['uid'],
                'cid' => (int)$data[1]['cid'],
                'catid' => (int)$data[1]['catid'],
                'author' => $data[1]['author'],
                'status' => (int)$data[1]['status'],
                'content' => dr_array2string($content),
                'backuid' => (int) $this->uid,
                'backinfo' => $this->admin ? dr_array2string($backinfo) : '',
                'inputtime' => SYS_TIME
            ));
        }

        $this->ci->clear_cache('extend'.$this->mdir.SITE_ID.$_data['id']);
        $this->ci->clear_cache('mextend'.$this->mdir.SITE_ID.$_data['id']);

        // 修改之后挂钩点
        $data['edit'] = $_data;
        $this->hooks->call_hook('extend_edit_after', $data);

        // 修改之后执行的方法
        $this->_edit_extend($data);

        return $_data['id'];
    }

    // 筛选出栏目表字段
    private function replace_category_data($id, $data) {

        $catfield = $this->ci->get_cache('MODULE-'.SITE_ID.'-'.$this->mdir, 'category', $data[1]['catid'], 'field');

        if ($catfield) {

            $cdata = array();
            $cdata[0]['id'] = $cdata[1]['id'] = $id;
            $cdata[0]['uid'] = $cdata[1]['uid'] = $data[1]['uid'];
            $cdata[0]['catid'] = $cdata[1]['catid'] = $data[1]['catid'];

            // 主表内容
            foreach ($data[1] as $i => $t) {
                if (strpos($i, '_lng') || strpos($i, '_lat')) {
                    $i = str_replace(array('_lng', '_lat'), '', $i);
                    if (isset($catfield[$i]) && $catfield[$i]['ismain'] == 1
                        && !isset($cdata[1][$i.'_lng'])) {
                        $cdata[1][$i.'_lng'] = $data[1][$i.'_lng'];
                        $cdata[1][$i.'_lat'] = $data[1][$i.'_lat'];
                        unset($data[1][$i.'_lng'], $data[1][$i.'_lat']);
                    }
                } else {
                    if (isset($catfield[$i]) && $catfield[$i]['ismain'] == 1) {
                        $cdata[1][$i] = $t;
                        unset($data[1][$i]);
                    }
                }
            }
            $this->db->replace($this->prefix.'_category_data', $cdata[1]); // 栏目主表
            // 附表内容
            if ($data[0]) {
                foreach ($data[0] as $i => $t) {
                    if (strpos($i, '_lng') || strpos($i, '_lat')) {
                        $i = str_replace(array('_lng', '_lat'), '', $i);
                        if (isset($catfield[$i]) && $catfield[$i]['ismain'] == 0
                            && !isset($cdata[0][$i.'_lng'])) {
                            $cdata[0][$i.'_lng'] = $data[0][$i.'_lng'];
                            $cdata[0][$i.'_lat'] = $data[0][$i.'_lat'];
                            unset($data[0][$i.'_lng'], $data[0][$i.'_lat']);
                        }
                    } else {
                        if (isset($catfield[$i]) && $catfield[$i]['ismain'] == 0) {
                            $cdata[0][$i] = $t;
                            unset($data[0][$i]);
                        }
                    }
                }

                // 副表以5w左右数据量无限分表
                $data[1]['tableid'] = $data[1]['tableid'] ? $data[1]['tableid'] : floor($id / 50000);
                if (!$this->db->query("SHOW TABLES LIKE '".$this->prefix.'_category_data_'.$data[1]['tableid']."'")->row_array()) {
                    // 附表不存在时创建附表
                    $sql = $this->db->query("SHOW CREATE TABLE `{$this->prefix}_category_data_0`")->row_array();
                    $this->db->query(str_replace(
                        array($sql['Table'], 'CREATE TABLE '),
                        array($this->prefix.'_category_data_'.$data[1]['tableid'], 'CREATE TABLE IF NOT EXISTS '),
                        $sql['Create Table']
                    ));
                }
                $this->db->replace($this->prefix.'_category_data_'.$data[1]['tableid'], $cdata[0]); // 副表
            }
        }

        return $data;
    }

    // 获取扩展内容
    public function get_extend($id) {

        $id = (int) $id;
        if (!$id) {
            return NULL;
        }

        // 主表
        $data1 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_extend')->row_array();
        // 副表
        $data2 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_extend_data_'.(int)$data1['tableid'])->row_array();

        return $data1 + $data2;
    }

    // 获取内容
    public function get($id) {

        if (!$id) {
            return NULL;
        }

        // 主表
        $data1 = $this->db->where('id', $id)->limit(1)->get($this->prefix)->row_array();
        if (!$data1) {
            return NULL;
        }

        // 副表
        $data2 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_data_'.$data1['tableid'])->row_array();
        // 栏目附加数据
        $data3 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_category_data')->row_array();
        // 栏目附加数据副表
        $data3 && $data4 = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_category_data_'.$data1['tableid'])->row_array();

        // 数据组合
        $data = array();
        $data = $data2 ? $data1 + $data2 : $data1;
        $data = $data3 ? $data + $data3 : $data;
        $data = $data4 ? $data + $data4 : $data;

        return $data;
    }

    // 获取审核信息
    public function get_verify($id) {

        if (!$id) {
            return NULL;
        }

        // 主表
        $data = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_verify')->row_array();
        if (!$data) {
            return NULL;
        }

        $data['content'] = dr_string2array($data['content']);
        $data['backinfo'] = dr_string2array($data['backinfo']);
        $data['content']['id'] = $data['id'];
        $data['content']['uid'] = $data['uid'];
        $data['content']['catid'] = $data['catid'];
        $data['content']['author'] = $data['author'];

        return $data;
    }

    // 获取审核信息
    public function get_extend_verify($id) {

        if (!$id) {
            return NULL;
        }

        // 主表
        $data = $this->db->where('id', $id)->limit(1)->get($this->prefix.'_extend_verify')->row_array();
        if (!$data) {
            return NULL;
        }

        $data['content'] = dr_string2array($data['content']);
        $data['backinfo'] = dr_string2array($data['backinfo']);
        $data['content']['id'] = $data['id'];
        $data['content']['cid'] = $data['cid'];
        $data['content']['uid'] = $data['uid'];
        $data['content']['catid'] = $data['catid'];
        $data['content']['author'] = $data['author'];

        return $data;
    }

    /**
     * 社区分享
     *
     * @param	array	$data	文档数据内容
     * @return  NULL
     */
    public function sns_share($body, $is_extend = 0) {

        // 判断是否开启了同步分享
        if (!$this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'syn2')) {
            return;
        }

        $data = $body[1] + $body[0];
        if (!$data) {
            return;
        }

        $url = $data['url']; // 地址
        $uid = $data['uid'] ? $data['uid'] : $this->uid;
        $thumb = $data['thumb'] ? dr_thumb($data['thumb']) : '';

        $title = $data['title'].($data['description'] ? ' '.$data['description'] : '');

        // 自定义同步内容
        $syn = $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'syn2field');
        if ($syn) {
            isset($data[$syn]) && $data[$syn] ? $title = $data[$syn] : log_message('error', '无法执行自定义同步内容：同步字段（'.$syn.'）内容为空');
        }

        $title = dr_clearhtml($title);

        // tag作为话题
        if ($this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'syn2tag')
            && isset($data['keywords']) && $data['keywords']
            && strpos($title, '#') === false) {
            $str = '';
            $tag = explode(',', trim($data['keywords'], ','));
            foreach ($tag as $t) {
                $t && $str.= '#'.$t.'# ';
            }
            $title = $str.$title;
        }

        // 添加到QQ分享任务队列
        $this->input->post('qq_share') && $this->member['oauth']['qq'] && $this->cron_model->add(2, array(
            'uid' => $uid,
            'url' => $url,
            'thumb' => $thumb,
            'title' => $title,
        ));

        // 添加到新浪分享任务队列
        $this->input->post('sina_share') && $this->member['oauth']['sina'] && $this->cron_model->add(4, array(
            'uid' => $uid,
            'url' => $url,
            'thumb' => $thumb,
            'title' => $title,
        ));

        // 百度ping
        $this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'setting', 'bdping') && $this->cron_model->add(5, array(
            'url' => $url,
            'site' => SITE_URL,
            'title' => $data['title'] ? $data['title'] : $data['name'],
        ));

    }

    // 审核后执行的操作
    public function verify_notice($id, $data) {
        $this->member_model->add_notice($data[1]['uid'], 3, fc_lang('【%s】审核通过', $data[1]['title']));
    }

    /**
     * 删除静态页面
     *
     * @param	string	$data	文件序列化字符串
     * @return  NULL
     */
    public function delete_html_file($data) {

        if (!$data) {
            return NULL;
        }

        foreach ($data as $t) {
            $filepath = dr_string2array($t['filepath']);
            $this->db->where('id', (int)$t['id'])->delete($this->prefix.'_html');
            if ($filepath) {
                foreach ($filepath as $file) {
                    unlink($file);
                    dr_rmdir(dirname($file));
                }
            }
        }
    }

    /**
     * 删除内容
     *
     * @param	intval	$id			模块内容的id
     * @param	intval	$tableid	模块内容附表id
     * @return  NULL
     */
    public function delete_for_id($id, $tableid) {

        if (!$id) {
            return NULL;
        }

        // 删除执行的方法
        $this->_del_content($id);

        // 删除内容执行的钩子
        $this->hooks->call_hook('content_delete', array('id' => $id, 'tableid' => $tableid));

        // 删除缓存
        $this->ci->clear_cache('hits'. $this->mdir.SITE_ID.$id);
        $this->ci->clear_cache((SITE_MOBILE === TRUE ? 'm' : '').'show'.$this->mdir.SITE_ID.$id);

        // 删除表对应的附件
        if ($this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel')) {
            $this->load->model('attachment_model');
            $this->attachment_model->delete_for_table($this->prefix.'-'.$id);
            $this->attachment_model->delete_for_table($this->prefix.'_verify-'.$id);
        }

        // 删除审核表
        $this->db->where('id', $id)->delete($this->prefix.'_verify');
        // 删除索引表
        $this->db->where('id', $id)->delete($this->prefix.'_index');
        // 删除附表表
        $this->db->where('id', $id)->delete($this->prefix.'_data_'.(int)$tableid);
        // 删除标记表
        $this->db->where('id', $id)->delete($this->prefix.'_flag');
        // 删除统计
        $this->db->where('id', $id)->delete($this->prefix.'_hits');
        // 删除主表
        $this->db->where('id', $id)->delete($this->prefix);
        // 当存在栏目附加表时
        if ($this->db->query("SHOW TABLES LIKE '".$this->prefix.'_category_data_'.(int)$tableid."'")->row_array()) {
            // 删除栏目附加表
            $this->db->where('id', $id)->delete($this->prefix.'_category_data');
            // 删除栏目附加表
            $this->db->where('id', $id)->delete($this->prefix.'_category_data_'.(int)$tableid);
        }
        // 删除收藏表
        $this->db->where('id', $id)->delete($this->prefix.'_favorite');
        $this->db->where('id', $id)->delete($this->prefix.'_buy');
        // 删除应用的相关表
        $app = $this->ci->get_cache('app');
        if ($app) {
            foreach ($app as $dir) {
                $a = $this->ci->get_cache('app-'.$dir);
                if (isset($a['related']) && $a['related']
                    && is_file(FCPATH.'app/'.$dir.'/models/'.$dir.'_model.php')) {
                    $this->load->add_package_path(FCPATH.'app/'.$dir.'/');
                    $this->load->model($dir.'_model', 'app_model');
                    $this->app_model->delete_for_cid($id, $this->mdir);
                }
            }
        }
        // 删除文件
        if ($html = $this->db->select('filepath,id')->where('rid', $id)->where('type', 1)->get($this->prefix.'_html')->result_array()) {
            $this->delete_html_file($html);
            $this->db->where('rid', $id)->where('type', 1)->delete($this->prefix.'_html');
        }
        // 删除扩展内容
        if ($this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'extend')) {
            $data = $this->db->select('tableid,id')->where('cid', $id)->get($this->prefix.'_extend')->result_array();
            if ($data) {
                // 逐一删除内容
                foreach ($data as $t) {
                    $this->delete_extend_for_id($t['id'], $id, $t['tableid']);
                }
            }
        }
        $this->db->db_debug = FALSE;

        // 删除草稿
        if ($data = $this->db->where('cid', $id)->get($this->prefix.'_draft')->result_array()) {
            foreach ($data as $t) {
                $this->db->where('id', $t['id'])->delete($this->prefix.'_draft');
                $this->attachment_model->delete_for_table($this->prefix.'_draft-'.$t['id']);
            }
        }

        // 删除表单数据
        $form = $this->db->where('module', $this->mdir)->order_by('id ASC')->get('module_form')->result_array();
        if ($form) {
            foreach ($form as $f) {
                // 删除表对应的附件
                $table = SITE_ID.'_'.$this->mdir.'_form_'.$f['table'];
                $data = $this->db->where('cid', $id)->get($table)->result_array();
                if ($data) {
                    foreach ($data as $t) {
                        $this->db->where('id', $t['id'])->delete($table);
                        $this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel') && $this->attachment_model->delete_for_table($table.'-'.$t['id']);
                    }
                }
            }
        }

        // 删除评论
        $index = $this->db->where('cid', $id)->get($this->prefix.'_comment_index')->row_array();
        if ($index) {
            $this->db->where('cid', $id)->delete($this->prefix.'_comment_my');
            $this->db->where('cid', $id)->delete($this->prefix.'_comment_index');
            $table = $this->prefix.'_comment_data_'.intval($index['tableid']);
            $data = $this->db->where('cid', $id)->get($table)->result_array();
            if ($data) {
                foreach ($data as $t) {
                    $this->db->where('id', $t['id'])->delete($table);
                    $this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel') && $this->attachment_model->delete_for_table($table.'-'.$t['id']);
                }
            }
        }

    }

    /**
     * 删除扩展内容
     *
     * @param	array	$ids	id数组
     * @return  NULL
     */
    public function delete_extend_for_ids($ids) {

        if (!$ids) {
            return NULL;
        }

        $data = $this->db->select('tableid,id,cid')->where_in('id', $ids)->get($this->prefix.'_extend')->result_array();
        if (!$data) {
            return NULL;
        }

        // 逐一删除内容
        foreach ($data as $t) {
            $this->delete_extend_for_id($t['id'], $t['cid'], $t['tableid']);
        }
    }

    /**
     * 删除扩展内容
     *
     * @param	array	$ids	id数组
     * @return  NULL
     */
    public function delete_extend_for_id($id, $cid, $tableid) {

        if (!$id || !$cid) {
            return NULL;
        }

        // 删除执行的方法
        $this->_del_extend($id);

        // 删除执行的钩子
        $this->hooks->call_hook('extend_delete', array('id' => $id, 'cid' => $cid, 'tableid' => $tableid));

        // 删除缓存
        $this->ci->clear_cache((SITE_MOBILE === TRUE ? 'm' : '').'extend'.$this->mdir.SITE_ID.$id);

        // 删除表对应的附件
        if ($this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel')) {
            $this->load->model('attachment_model');
            $this->attachment_model->delete_for_table($this->prefix.'-'.$cid.'-'.$id);
            $this->attachment_model->delete_for_table($this->prefix.'_verify-'.$cid.'-'.$id);
        }

        // 删除索引表
        $this->db->where('id', $id)->delete($this->prefix.'_extend_index');
        $this->db->where('id', $id)->delete($this->prefix.'_extend');
        $this->db->where('id', $id)->delete($this->prefix.'_extend_verify');

        // 删除附表
        $this->db->where('id', $id)->delete($this->prefix.'_extend_data_'.(int)$tableid);

        // 删除文件
        if ($html = $this->db
                        ->select('filepath,id')
                        ->where('rid', $id)
                        ->where('type', 2)
                        ->get($this->prefix.'_html')
                        ->result_array()) {
            $this->delete_html_file($html);
            $this->db->where('rid', $id)->where('type', 2)->delete($this->prefix.'_html');
        }

        // 删除草稿
        if ($data = $this->db->where('cid', $cid)->where('eid', $id)->get($this->prefix.'_draft')->result_array()) {
            foreach ($data as $t) {
                $this->db->where('id', $t['id'])->delete($this->prefix.'_draft');
                $this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel') && $this->attachment_model->delete_for_table($this->prefix.'_draft-'.$t['id']);
            }
        }

        // 删除评论
        $index = $this->db->where('cid', $id)->get($this->prefix.'_extend_comment_index')->row_array();
        if ($index) {
            $this->db->where('cid', $id)->delete($this->prefix.'_extend_comment_my');
            $this->db->where('cid', $id)->delete($this->prefix.'_extend_comment_index');
            $table = $this->prefix.'_extend_comment_data_'.intval($index['tableid']);
            $data = $this->db->where('cid', $id)->get($table)->result_array();
            if ($data) {
                foreach ($data as $t) {
                    $this->db->where('id', $t['id'])->delete($table);
                    $this->ci->get_cache('module-'.SITE_ID.'_'.$this->mdir, 'setting', 'attdel') && $this->attachment_model->delete_for_table($table.'-'.$t['id']);
                }
            }
        }

    }

    // 删除审核
    public function del_extend_verify($id) {

        if (!$id) {
            return NULL;
        }

        // 删除审核表
        $this->db->where('id', $id)->delete($this->prefix.'_extend_verify');

        // 当主表无数据时才删除索引表
        if (!$this->db->where('id', $id)->count_all_results($this->prefix.'_extend')) {
            $this->db->where('id', $id)->delete($this->prefix.'_extend_index');
            return TRUE;
        } else {
            // 主表有数据时 恢复为通过状态
            $this->db->where('id', $id)->update($this->prefix.'_extend_index', array('status' => 9));
            $this->db->where('id', $id)->update($this->prefix.'_extend', array('status' => 9));
            return FALSE;
        }
    }

    // 删除审核
    public function del_verify($id) {

        if (!$id) {
            return NULL;
        }

        // 删除审核表
        $this->db->where('id', $id)->delete($this->prefix.'_verify');

        // 当主表无数据时才删除索引表
        if (!$this->db->where('id', $id)->count_all_results($this->prefix)) {
            $this->db->where('id', $id)->delete($this->prefix.'_index');
            return TRUE;
        } else {
            // 主表有数据时 恢复为通过状态
            $this->db->where('id', $id)->update($this->prefix.'_index', array('status' => 9));
            $this->db->where('id', $id)->update($this->prefix, array('status' => 9));
            return FALSE;
        }
    }

    // 文档标记
    public function flag($ids, $flag) {

        if (!$ids || !$flag) {
            return NULL;
        }

        $data = $this->db->where_in('id', $ids)->select('catid,id')->get($this->prefix)->result_array();
        if (!$data) {
            return NULL;
        }

        $i = 0;

        foreach ($data as $t) {
            if ($flag > 0) {
                // 增加推荐位
                if (!$this->db->where('id', $t['id'])->where('flag', $flag)->count_all_results($this->prefix.'_flag')) {
                    $this->db->replace($this->prefix.'_flag', array(
                        'id' => $t['id'],
                        'uid' => $this->uid,
                        'flag' => $flag,
                        'catid' => $t['catid']
                    ));
                    $i ++;
                }
            } elseif ($flag < 0) {
                // 取消推荐位
                $this->db->where('id', $t['id'])->where('flag', abs($flag))->delete($this->prefix.'_flag');
                $i ++;
            }
        }

        return $i;
    }

    // 推荐位统计
    public function flag_total($id, $catid = NULL, $uid = NULL) {

        $this->where && $this->db->where($this->where);
        $uid && $this->db->where('uid', $uid);
        $catid && $this->db->where('catid', $catid);
        $this->db->where('flag', $id);

        return $this->db->count_all_results($this->prefix.'_flag');
    }

    // 更新文档时间
    public function updatetime($id) {
        $this->db->where('uid', $this->uid)->where_in('id', $id)->update($this->prefix, array('updatetime' => SYS_TIME));
    }

    // 移动栏目
    public function move($id, $catid) {

        if (!$id || !$catid) {
            return FALSE;
        }

        $this->db->where_in('id', $id)->update($this->prefix, array('catid' => $catid));
        $this->db->where_in('id', $id)->update($this->prefix.'_index', array('catid' => $catid));

        if ($this->ci->get_cache('module-'.SITE_ID.'-'.$this->mdir, 'extend')) {
            $this->db->where_in('cid', $id)->update($this->prefix.'_extend', array('catid' => $catid));
            $this->db->where_in('cid', $id)->update($this->prefix.'_extend_index', array('catid' => $catid));
        }

        return TRUE;
    }

    // 移动扩展
    public function extend_move($ids, $type) {

        if (!$ids) {
            return FALSE;
        }

        $this->db->where_in('id', $ids)->update($this->prefix.'_extend', array('mytype' => $type));

        return TRUE;
    }

    // 更新至tag表
    public function update_tag($keyword) {
        $array = explode(',', $keyword);
        foreach ($array as $name) {
            $name = trim($name);
            if (strlen($name) > 2
                && strlen($name) < 30
                && !$this->db->where('name', $name)->count_all_results($this->prefix.'_tag')) {
                $this->db->replace($this->prefix.'_tag', array(
                    'name' => $name,
                    'code' => dr_word2pinyin($name),
                    'hits' => 0
                ));
            }
        }
    }

    // 获取内容（用于商品订单），模块可重写
    public function get_item_data($id) {
        return NULL;
    }

    // 格式化字段值，模块可重写
    protected function get_content_data($data, $_data = NULL) {
        
        !$data[1]['description'] && $data[1]['description'] = trim(dr_strcut(dr_clearhtml($data[0]['content']), 200));
        
        return $data;
    }

    // 格式化字段值，模块可重写
    protected function get_content_extend_data($data, $_data = NULL) {
        return $data;
    }

    // 保存html文件记录
    public function set_html($type, $uid, $cid, $rid, $catid, $filepath) {

        $table = $this->prefix.'_html';
        if ($type != 3
            && $this->db->where('rid', $rid)->where('type', $type)->count_all_results($table)) {
            $this->db->where('rid', $rid)->where('type', $type)->update($table, array(
                'cid' => $cid,
                'uid' => $uid,
                'type' => $type,
                'catid' => $catid,
                'filepath' => dr_array2string($filepath)
            ));
        } else {
            $this->db->insert($table, array(
                'rid' => $rid,
                'cid' => $cid,
                'uid' => $uid,
                'type' => $type,
                'catid' => $catid,
                'filepath' => dr_array2string($filepath),
            ));
        }
    }

    /**
     * 保存内容的草稿
     *
     * @param	intval	$id 	草稿id
     * @param	array	$data	数据数组
     * @param	intval	$is_et 	是否是扩展表
     * @return  intval  $id     草稿id
     */
    public function save_draft($id, $data, $is_et = 0) {

        $data = array(
            'uid' => $this->uid,
            'cid' => $is_et ? intval($data[1]['cid']) : intval($data[1]['id']),
            'eid' => $is_et ? (intval($data[1]['id']) ? intval($data[1]['id']) : -1) : 0,
            'catid' => $data[1]['catid'],
            'content' => dr_array2string($data[0] ? array_merge($data[0], $data[1]) : $data[1]),
            'inputtime' => SYS_TIME
        );

        // 判断草稿是否存在，不存在就插入
        if ($id && $this->db->where('id', $id)->count_all_results($this->prefix.'_draft')) {
            $this->db->where('id', $id)->update($this->prefix.'_draft', $data);
        } else {
            $this->db->insert($this->prefix.'_draft', $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }

    // 删除草稿
    public function delete_draft($id, $where) {

        if ($this->db->where('id', $id)->where('uid', $this->uid)->where($where)->get($this->prefix.'_draft')->row_array()) {
            $this->db->where('id', $id)->delete($this->prefix.'_draft');
            return TRUE;
        }

        return FLASE;
    }

    // 获取草稿内容
    public function get_draft($id) {

        $data = $this->db->where('id', $id)->where('uid', $this->uid)->get($this->prefix.'_draft')->row_array();
        if (!$data) {
            return NULL;
        }

        $body = dr_string2array($data['content']);
        $body['draft']['cid'] = $data['cid'];
        $body['draft']['eid'] = $data['eid'];
        $body['draft']['catid'] = $body['catid'] = $data['catid'];

        return $body;

    }

    // 获取草稿列表
    public function get_draft_list($where) {

        $rt = array();
        $data = $this->db->where('uid', $this->uid)->where($where)->order_by('inputtime desc')->get($this->prefix.'_draft')->result_array();
        if ($data) {
            foreach ($data as $t) {
                $rt[$t['id']] = dr_string2array($t['content']);
                $rt[$t['id']]['id'] = $t['id'];
                $rt[$t['id']]['inputtime'] = $t['inputtime'];
            }
        }

        return $rt;
    }

    // 以下方法用于二次开发或扩展
    public function _add_content($data) { }
    public function _edit_content($data) { }
    public function _del_content($data) { }
    public function _add_extend($data) { }
    public function _edit_extend($data) { }
    public function _del_extend($data) { }
    public function _update_status($data) { }
    public function _update_status_extend($data) { }
    public function update_share($id, $eid) { }

}
