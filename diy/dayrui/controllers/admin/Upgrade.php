<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Upgrade extends M_Controller {

    private $rid;
    private $version;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->version = DR_VERSION;
    }

    /**
     * 程序管理
     */
    public function index() {

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('程序管理') => array('admin/upgrade/index', 'plug'),
            )),
            'note' => strpos(FCPATH, '/diy/') === false ? '<font color="red">由于你更改过默认主程序目录，无法进行在线升级</font>' : '如果改过diy目录或者变更过php文件内容，请下载补丁包进行手动升级！',
        ));
        $this->template->display('upgrande.html');
    }

    // 版本列表
    public function vlist() {

        $data = dr_catcher_data('http://www.poscms.net/index.php?c=fc&m=vlist&my='.DR_VERSION_ID.'&domain='.trim(str_replace(array('http://', 'https://'), '', $this->site_info[1]['SITE_URL']), '/'));

        if (!$data) {
            exit('<tr><td align="left" colspan="5" style="color:red; padding: 30px 10px"> <a href="http://help.dayrui.com/index.php?c=doc&m=bdb" target="_blank">单击前往下载补丁包！</a> </td></tr>');
        } elseif (strpos($data, 'error:') === 0) {
            echo('<tr><td align="left" colspan="5" style="color:red; padding: 10px 10px"> '.$data.' </td></tr>');
            exit('<tr><td align="left" colspan="5" style="color:red; padding: 10px 10px"> <a href="http://help.dayrui.com/index.php?c=doc&m=bdb" target="_blank">单击前往下载补丁包！</a> </td></tr>');
        }

        $vlist = dr_object2array(json_decode($data));
        if (!$vlist) {
            exit('<tr><td align="left" colspan="5" style="color:red; padding: 30px 10px"> 返回数据不规范，请联系售后工程师！ </td></tr>');
        }


        $this->template->assign('nid', DR_VERSION_ID + 1);
        $this->template->assign('vlist', $vlist);
        $this->template->display('upgrande_vlist.html');
    }

    /**
     * 检查下载程序
     */
    public function update() {

        if (strpos(FCPATH, '/diy/') === false) {
         $this->admin_msg('由于你更改过默认主程序目录，无法进行在线升级');
        }

        $vid = (int)$this->input->get('id');
        $sid = (int)$this->input->get('file');

        define('UPVID', $vid);
        $data = dr_catcher_data(urldecode('http://www.poscms.net/index.php?c=fc&m=down&id='.$vid.'&sn='.$sid));

        if (!$data) {
            $this->admin_msg('您的服务器不支持远程下载');
        } elseif (strlen($data) < 200) {
            $this->admin_msg('服务端错误：'.$data);
        }

        $save = WEBPATH.'cache/down/update.zip';
        $check = WEBPATH.'cache/down/update/';
        if (!@file_put_contents($save, $data)) {
            $this->admin_msg('目录/cache/down/没有写入权限');
        }

        // 解压缩文件
        $this->load->helper('file');
        $this->load->library('Pclzip');
        $this->pclzip->PclFile($save);
        if ($this->pclzip->extract(PCLZIP_OPT_PATH, $check, PCLZIP_OPT_REPLACE_NEWER) == 0) {
            @unlink($save);
            delete_files(WEBPATH.'cache/down/', TRUE);
            $this->admin_msg("Error : " . $this->pclzip->errorInfo(true));
        }

        if (!is_file($check.'config/version.php') || !filesize($check.'config/version.php')) {
            delete_files(WEBPATH.'cache/down/', TRUE);
            $this->admin_msg('升级文件不完整，没有找到版本文件');
        }

        // 备份更新文件
        $this->_backup_file(WEBPATH.'cache/down/update/');

        // 覆盖至网站根目录
        $this->pclzip->extract(PCLZIP_OPT_PATH, WEBPATH, PCLZIP_OPT_REPLACE_NEWER);

        // 逐一验证文件是否被覆盖
        $this->_check_file(WEBPATH.'cache/down/update/');

        // 删除更新包
        $this->dcache->set('install', TRUE);
        delete_files(WEBPATH.'cache/down/', TRUE);

        // 运行SQL语句
        if (is_file(WEBPATH.'update.sql')) {
            $sql = file_get_contents(WEBPATH.'update.sql');
            $sql = str_replace('{dbprefix}', $this->db->dbprefix, $sql);
            $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $sql)));
            foreach($sql_data as $query){
                if (!$query) continue;
                $queries = explode('SQL_FINECMS_EOL', trim($query));
                $ret = '';
                foreach($queries as $query) {
                    $ret .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
                }
                if (!$ret) continue;
                $this->db->query($ret);
            }
            @unlink(WEBPATH.'update.sql');
        }

        $this->system_log('升级版本'); // 记录日志
        //检查update控制器
        if (is_file(FCPATH.'dayrui/controllers/admin/Update.php')) {
            $this->admin_msg('正在升级数据，请稍候...', dr_url('update/index'), 2);
        }

        $this->admin_msg('升级完成，请按F5刷新整个页面', dr_url('home/main'), 1);
    }

    // 遍历检测文件
    private function _check_file($source_dir, $directory_depth = 0) {

        if ($fp = @opendir($source_dir)) {
            $filedata	= array();
            $new_depth	= $directory_depth - 1;
            $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            while (FALSE !== ($file = readdir($fp))) {
                if ($file === '.'
                    || $file === '..'
                    || $file[0] === '.') {
                    continue;
                }
                is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 || $new_depth > 0)
                    && is_dir($source_dir.$file)) {
                    $filedata[$file] = $this->_check_file($source_dir.$file, $new_depth);
                } else {
                    $file1 = $source_dir.$file; // 更新包文件
                    if (!is_file($file1)) {
                        $this->admin_msg($file1.'不存在');
                    }
                    $file2 = str_replace('cache/down/update/', '', $source_dir).$file; // 当前系统文件
                    if (is_file($file2)) {
                        // 备份当前系统的
                        $md1 = md5(file_get_contents($file1));
                        $md2 = md5(file_get_contents($file2));
                        if ($md1 != $md2) {
                            delete_files(WEBPATH.'cache/down/', TRUE);
                            delete_files(WEBPATH.'cache/backup/'.$this->version.'/', TRUE);
                            $this->admin_msg('文件'.str_replace(FCPATH, '', $file2).'覆盖失败，<br>在线升级前请给全站可写权限，以便更新文件的写入<br>然后再点“重新升级”的链接升级程序');
                            exit;
                        }
                    }
                }
            }
            closedir($fp);
            return $filedata;
        }
    }

    // 备份文件
    private function _backup_file($source_dir, $directory_depth = 0) {

        if ($fp = @opendir($source_dir)) {
            $filedata	= array();
            $new_depth	= $directory_depth - 1;
            $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            while (FALSE !== ($file = readdir($fp))) {
                if ($file === '.'
                    || $file === '..'
                    || $file[0] === '.') {
                    continue;
                }
                is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

                if (($directory_depth < 1 || $new_depth > 0)
                    && is_dir($source_dir.$file)) {
                    $filedata[$file] = $this->_backup_file($source_dir.$file, $new_depth);
                } else {
                    $file1 = $source_dir.$file; // 更新包文件
                    if (!is_file($file1)) {
                        $this->admin_msg($file1.'不存在');
                    }
                    $file2 = str_replace('cache/down/update/', '', $source_dir).$file; // 当前系统文件
                    if (is_file($file2)) {
                        $backfile = str_replace('cache/down/update/', 'cache/backup/'.intval(UPVID - 1).'/', $source_dir).$file; // 备份文件
                        dr_mkdirs(dirname($backfile)); // 创建文件
                        $rt = copy($file2, $backfile);
                        if ($rt === false) {
                            $this->admin_msg('文件'.str_replace(FCPATH, '', $file2).'备份失败，<br>在线升级前请给全站可写权限，备份当前程序文件');
                            exit;
                        }
                    }
                }
            }
            closedir($fp);
            return $filedata;
        }
    }

    // 执行sql
    private function _query($sql) {

        if (!$sql) {
            return NULL;
        }

        $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $sql)));

        foreach($sql_data as $query){
            if (!$query) {
                continue;
            }
            $ret = '';
            $queries = explode('SQL_FINECMS_EOL', trim($query));
            foreach($queries as $query) {
                $ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
            }
            if (!$ret) {
                continue;
            }
            $this->db->query($ret);
        }
    }

}