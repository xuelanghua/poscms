<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class D_Admin_Content extends M_Controller {

    public $row;

    /**
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
    }


    ////////////////////内容维护部分/////////////////////////

    // 内容维护功能菜单
    private function _get_content_menu() {

        return array(
            fc_lang('提取缩略图') => APP_DIR.'/admin/content/index',
            fc_lang('更新URL') => APP_DIR.'/admin/content/url',
            fc_lang('替换内容') => APP_DIR.'/admin/content/replace',
            fc_lang('提取关键字') => APP_DIR.'/admin/content/keyword',
        );
    }

    // 提取关键字
    public function keyword() {

        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().'_content_keyword';

        if (IS_POST) {
            $query = $this->db;
            $catid = $this->input->post('catid');
            $keyword = $this->input->post('keyword');
            if (count($catid) > 1 || $catid[0]) {
                $query->where_in('catid', $catid);
                count($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category')) == count($catid) && $catid = 0;
            } else {
                $catid = 0;
            }
            // 统计数量
            $total = $keyword ? $query->where('keywords=""')->count_all_results($this->content_model->prefix) : $query->count_all_results($this->content_model->prefix.'_index');
            $this->cache->file->save($cfile, array('keyword' => $keyword, 'catid' => $catid, 'total' => $total), 10000);
            if ($total) {
                $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】提取关键词#'.$total); // 记录日志
                $this->mini_msg(fc_lang('可更新内容%s条，正在准备执行...', $total), dr_url(APP_DIR.'/content/keyword', array('todo' => 1)), 2);
            } else {
                $this->mini_msg(fc_lang('抱歉，没有找到可更新的内容'));
            }
        }

        // 处理url
        if ($this->input->get('todo')) {
            $page = max(1, (int)$this->input->get('page'));
            $psize = 100; // 每页处理的数量
            $cache = $this->cache->file->get($cfile);
            $table = $this->content_model->prefix;
            if ($cache) {
                $total = $cache['total'];
                $catid = $cache['catid'];
                $keyword = $cache['keyword'];
            } else {
                $catid = 0;
                $keyword = 1;
                $total = $this->db->where('keywords=""')->count_all_results($table);
            }
            $tpage = ceil($total / $psize); // 总页数
            if ($page > $tpage) {
                // 更新完成删除缓存
                $this->cache->file->delete($cfile);
                $this->mini_msg(fc_lang('更新成功'), NULL, 1);
            }
            $module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
            $catid && $this->db->where_in('catid', $catid);
            $keyword && $this->db->where('keywords=""');
            $data = $this->db->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
            foreach ($data as $t) {
				$kw = $t['title'].' '.$t['description'];
				$info = @file_get_contents('http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title='.rawurlencode($kw).'&content='.rawurlencode($kw));
				if ($info) {
					$kws = array();
					$xml = xml_parser_create();
					xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
					xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
					xml_parse_into_struct($xml, $info, $values, $index);
					xml_parser_free($xml);
					foreach ($values as $v) {
						$kw = trim($v['value']);
                        (strlen($kw) > 5 && ($v['tag'] == 'kw' || $v['tag'] == 'ekw')) && $kws[] = $kw;
					}
					$update = @implode(',', $kws);
					if ($update) {
						$this->db->where('id='.$t['id'])->update($table, array(
							'keywords' => $update
						));
						$this->content_model->update_tag($update); // 更新tag表
					}
				}
            }
            $this->mini_msg(fc_lang('正在执行中(%s) ... ', "$tpage/$page"), dr_url(APP_DIR.'/content/keyword', array('todo' => 1, 'page' => $page + 1)), 2, 0);
        } else {
            $this->template->assign(array(
                'menu' => $this->get_menu($this->_get_content_menu()),
                'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid" name=\'catid[]\' multiple style="width:200px;height:250px;"', ''),
            ));
            $this->template->display('content_keyword.html');
        }

    }

    // 提取缩略图
    public function index() {

        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().'_content_thumb';

        if (IS_POST) {
            $catid = $this->input->post('catid');
            $thumb = $this->input->post('thumb');
            $query = $this->db;
            if (count($catid) > 1 || $catid[0]) {
                $query->where_in('catid', $catid);
                count($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category')) == count($catid) && $catid = 0;
            } else {
                $catid = 0;
            }
            // 统计数量
            $total = $thumb ? $query->where('thumb=""')->count_all_results($this->content_model->prefix) : $query->count_all_results($this->content_model->prefix.'_index');
            $this->cache->file->save($cfile, array('thumb' => $thumb, 'catid' => $catid, 'total' => $total), 10000);
            if ($total) {
                $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】提取缩略图#'.$total); // 记录日志
                $this->mini_msg(fc_lang('可更新内容%s条，正在准备执行...', $total), dr_url(APP_DIR.'/content/index', array('todo' => 1)), 2);
            } else {
                $this->mini_msg(fc_lang('抱歉，没有找到可更新的内容'));
            }
        }

        // 处理url
        if ($this->input->get('todo')) {
            $page = max(1, (int)$this->input->get('page'));
            $psize = 100; // 每页处理的数量
            $cache = $this->cache->file->get($cfile);
            $table = $this->content_model->prefix;
            if ($cache) {
                $total = $cache['total'];
                $catid = $cache['catid'];
                $thumb = $cache['thumb'];
            } else {
                $catid = 0;
                $thumb = 1;
                $total = $this->db->where('thumb=""')->count_all_results($table);
            }
            $tpage = ceil($total / $psize); // 总页数
            if ($page > $tpage) {
                // 更新完成删除缓存
                $this->cache->file->delete($cfile);
                $this->mini_msg(fc_lang('更新成功'), NULL, 1);
            }
            $module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
            $catid && $this->db->where_in('catid', $catid);
            $thumb && $this->db->where('thumb=""');
            $data = $this->db->select('id,tableid')->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
            foreach ($data as $t) {
                $row = $this->db->select('content')->where('id', $t['id'])->get($table.'_data_'.$t['tableid'])->row_array();
                if ($row) {
                    $thumb = 0;
                    if (preg_match("/index\.php\?c=api&m=thumb&id=([0-9]+)&/U", $row['content'], $m)) {
                        $thumb = intval($m[1]);
                    } elseif (preg_match('/id=\"'.UEDITOR_IMG_ID.'_img_([0-9]+)\"/iU', $row['content'], $m)) {
                        $thumb = intval($m[1]);
                    } elseif (preg_match("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png))\\2/i", $row['content'], $m)) {
                        $thumb = $m[3];
                    }
                    if ($thumb) {
                        $this->db->where('id', $t['id'])->update($table, array('thumb' => $thumb));
                        IS_SHARE && $this->db->where('id', $t['id'])->update($this->content_model->share_prefix, array('thumb' => $thumb));
                    }
                }
            }
            $this->mini_msg(fc_lang('正在执行中(%s) ... ', "$tpage/$page"), dr_url(APP_DIR.'/content/index', array('todo' => 1, 'page' => $page + 1)), 2, 0);
        } else {
            $this->template->assign(array(
                'menu' => $this->get_menu($this->_get_content_menu()),
                'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid" name=\'catid[]\' multiple style="width:200px;height:250px;"', ''),
            ));
            $this->template->display('content_thumb.html');
        }

    }

    /**
     * 更新URL
     */
    public function url() {

        if (IS_POST) {
            $catid = $this->input->post('catid');
            $extend = $this->input->post('extend');
            $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().$extend.'_content_url';
            $query = $this->db;
            if (count($catid) > 1 || $catid[0]) {
                $query->where_in('catid', $catid);
                count($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category')) == count($catid) && $catid = 0;
            } else {
                $catid = 0;
            }
            // 统计数量
            $total = $query->count_all_results($this->content_model->prefix.($extend ? '_extend' : '').'_index');
            $this->cache->file->save($cfile, array('catid' => $catid, 'total' => $total), 10000);
            if ($total) {
                $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】更新URL地址#'.$total); // 记录日志
                $this->mini_msg(fc_lang('可更新内容%s条，正在准备执行...', $total), dr_url(APP_DIR.'/content/url', array('todo' => 1, 'extend' => $extend)), 2);
            } else {
                $this->mini_msg(fc_lang('抱歉，没有找到可更新的内容'));
            }
        }

        $extend = (int)$this->input->get('extend');
        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().$extend.'_content_url';

        // 处理url
        if ($this->input->get('todo')) {
            $page = max(1, (int)$this->input->get('page'));
            $psize = 100; // 每页处理的数量
            $cache = $this->cache->file->get($cfile);
            if ($cache) {
                $total = $cache['total'];
                $catid = $cache['catid'];
            } else {
                $catid = 0;
                $total = $this->db->count_all_results($this->content_model->prefix.($extend ? '_extend' : '').'_index');
            }
            $tpage = ceil($total / $psize); // 总页数
            if ($page > $tpage) {
                // 更新完成删除缓存
                $this->cache->file->delete($cfile);
                $this->mini_msg(fc_lang('更新成功'), NULL, 1);
            }
            $module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
            if ($extend) {
                $table = $this->content_model->prefix.'_extend';
                $catid && $this->db->where_in('catid', $catid);
                $data = $this->db->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
                foreach ($data as $e) {
                    $url = dr_extend_url($module, $e);
                    $this->db->where('id',(int)$e['id'])->update($table, array(
                        'url' => $url
                    ));
                }
            } else {
                $table = $this->content_model->prefix;
                $catid && $this->db->where_in('catid', $catid);
                $data = $this->db->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
                foreach ($data as $t) {
                    if ($t['link_id'] && $t['link_id'] >= 0) {
                        // 同步栏目的数据
                        $i = $t['id'];
                        $t = $this->db->where('id', (int)$t['link_id'])->get($table)->row_array();
                        if (!$t) {
                            continue;
                        }
                        $url = dr_show_url($module, $t);
                        $t['id'] = $i; // 替换成当前id
                    } else {
                        $url = dr_show_url($module, $t);
                    }
                    $this->db->update($table, array('url' => $url), 'id='.$t['id']);
                    IS_SHARE && $this->db->where('id', $t['id'])->update($this->content_model->share_prefix, array('url' => $url));
                }
            }

            $this->mini_msg(fc_lang('正在执行中(%s) ... ', "$tpage/$page"), dr_url(APP_DIR.'/content/url', array('todo' => 1, 'extend' => $extend, 'page' => $page + 1)), 2, 0);
        } else {
            $this->template->assign(array(
                'menu' => $this->get_menu($this->_get_content_menu()),
                'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid" name=\'catid[]\' multiple style="width:200px;height:220px;"', ''),
                'select2' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid2" name=\'catid[]\' multiple style="width:200px;height:220px;"', ''),
                'is_extend' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend'),
            ));
            $this->template->display('content_url.html');
        }
    }

    // 替换内容
    public function replace() {

        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().'_content_replace';

        if (IS_POST) {
            $bm = $this->input->post('bm');
            $t1 = $this->input->post('t1');
            $t2 = $this->input->post('t2');
            $fd = $this->input->post('fd');
            (!$fd || !$t1) && $this->mini_msg('“待替换字段”和“被替换内容”必须填写！');
            $fd == 'id' && $this->mini_msg('主键不支持替换！');
            // 表名判断
            $field = $this->get_table_field(str_replace('{id}', '0', $bm));
            !$field && $this->mini_msg('此表【'.str_replace('{id}', '0', $bm).'】无可用字段');
            // 可用字段判断
            !isset($field[$fd]) && $this->mini_msg('在表【'.str_replace('{id}', '0', $bm).'】中没有找到字段【'.$fd.'】');
            $this->cache->file->save($cfile, array(
                'bm' => $bm,
                't1' => $t1,
                't2' => $t2,
                'fd' => $fd,
            ), 10000);
            $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】替换内容操作'); // 记录日志
            $this->mini_msg('正在搜索替换...', dr_url(APP_DIR.'/content/replace', array('todo' => 1)), 2);
        }

        // 处理url
        if ($this->input->get('todo')) {
            $cache = $this->cache->file->get($cfile);
            if (!$cache['fd'] || !$cache['t1']) {
                $this->cache->file->delete($cfile);
                $this->mini_msg('缓存失败：“待替换字段”和“被替换内容”必须填写！');
            }
            $count = 0;
            $replace = '`'.$cache['fd'].'`=REPLACE(`'.$cache['fd'].'`, \''.addslashes($cache['t1']).'\', \''.addslashes($cache['t2']).'\')';
            if (strpos($cache['bm'], '{id}')) {
                for ($i = 0; $i < 100; $i ++) {
                    $table = str_replace('{id}', $i, $cache['bm']);
                    if (!$this->db->query("SHOW TABLES LIKE '".$table."'")->row_array()) {
                        break;
                    }
                    $this->db->query('UPDATE `'.$table.'` SET '.$replace);
                    $count+= $this->db->affected_rows();
                }
            } else {
                $this->db->query('UPDATE `'.$cache['bm'].'` SET '.$replace);
                $count = $this->db->affected_rows();
            }
            $this->cache->file->delete($cfile);
            $this->mini_msg('替换完毕，共替换'.$count.'条数据', NULL, 1);
        } else {
            $bm = array(
                array('name' => '主表', 'table' => $this->content_model->prefix),
                array('name' => '附表', 'table' => $this->content_model->prefix.'_data_{id}'),
                array('name' => '栏目主表', 'table' => $this->content_model->prefix.'_category_data'),
                array('name' => '栏目附表', 'table' => $this->content_model->prefix.'_category_data_{id}'),
            );
            if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend')) {
               $bm[] = array('name' => '扩展主表', 'table' => $this->content_model->prefix.'_extend');
               $bm[] = array('name' => '扩展附表', 'table' => $this->content_model->prefix.'_extend_data_{id}');
            }
            $form = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form');
            if ($form) {
                foreach ($form as $t) {
                    $bm[] = array('name' => $t['name'].'主表', 'table' => $this->content_model->prefix.'_form_'.$t['table']);
                    $bm[] = array('name' => $t['name'].'附表', 'table' => $this->content_model->prefix.'_form_'.$t['table'].'_data_{id}');
                }
            }
            $this->template->assign(array(
                'bm' => $bm,
                'menu' => $this->get_menu($this->_get_content_menu()),
            ));
            $this->template->display('content_replace.html');
        }

    }

}
