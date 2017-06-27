<?php



require FCPATH.'branch/fqb/D_Common.php';

class D_App extends D_Common {

    public $app; // 当前应用的配置情况

    /**
     * 应用继承类
     */
    public function __construct() {
        parent::__construct();
        $this->app = $this->get_cache('app-'.APP_DIR);
        // 应用不存在或者被禁用
        !IS_ADMIN && !$this->app && $this->msg(fc_lang('应用尚未安装或者被禁用了'));
        // 定义应用模板路径
        define('APP_TPL_PATH', SITE_URL.'app/'.APP_DIR.'/templates/'.SITE_THEME.'/');
    }

    /**
     * url方法
     *
     * @param	string	$uri	URL规则(相对于当前应用)，如home/index
     * @param	array	$query	相关参数
     * @return	string	项目入口文件.php?参数
     */
    public function url($uri, $query = '') {
        $url = dr_url(APP_DIR.'/'.$uri, $query);
        return IS_MEMBER ? MEMBER_URL.$url : (IS_ADMIN ? $url : SITE_URL.$url);
    }

    /**
     * 生成此应用的钩子配置文件
     *
     * @param	string	$dir	应用目录名称
     * @param	array	$data	钩子数据数组
     * @return  void
     */
    protected function update_hooks($dir, $data = NULL) {

        $app = require WEBPATH.'config/app_hooks.php';

        if ($data) {
            // 安装钩子
            $app[$dir] = $data;
        } else {
            // 卸载钩子
            unset($app[$dir]);
        }

        // 更新文件
        $php = '<?php'.PHP_EOL.PHP_EOL
            .'/**'.PHP_EOL
            .' * 应用的钩子定义配置'.PHP_EOL
            .' */'.PHP_EOL.PHP_EOL
            .'return '.str_replace(
                array('\'{app}', '"{app}'),
                array('FCPATH.\'app/'.$dir.'/', 'FCPATH."app/'.$dir.'/'),
                var_export($app, TRUE)
            ).';';

        // 生成文件
        file_put_contents(WEBPATH.'config/app_hooks.php', $php);

    }

    // 应用配置继承类
    public function _admin_config() {

        // 判断是否具有配置权限
        !$this->is_auth('admin/application/config') && $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'application/config'));

        // 当前应用配置
        $data = $this->application_model->get(APP_DIR);
        $config = require APPPATH.'config/app.php';

        if (IS_POST) {

            $setting = $this->input->post('data');

            if ($this->application_model->edit($data['id'], array(
                'module' => dr_array2string($this->input->post('module')),
                'setting' => dr_array2string($setting)
            ))) {
                // 查询增加的模块
                $del = $this->input->post('del');
                if ($del) {
                    foreach ($del as $dir => $value) {
                        $value && $this->_delete_for_module($dir); // 删除该模块时
                    }
                }
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), $this->url('home/cache', array('todo' => 1)), 1);
            }
        }

        $mod = array();
        $local = @array_diff(dr_dir_map(FCPATH.'module/', 1), array('member', 'space')); // 搜索本地模块
        if ($local) {
            foreach ($local as $dir) {
                is_file(FCPATH.'module/'.$dir.'/config/module.php') && $mod[$dir] = require FCPATH.'module/'.$dir.'/config/module.php';
            }
        }

        $this->template->assign(array(
            'mod' => $data['module'],
            'data' => $data['setting'],
            'menu' => $this->get_menu(array(
                fc_lang('应用管理') => 'admin/application/index',
                fc_lang('应用配置') => APP_DIR.'/admin/home/index',
                fc_lang('更新缓存') => APP_DIR.'/admin/home/cache',
            )),
            'menu2' => $this->get_menu_v3(array(
                fc_lang('应用管理') => array('admin/application/index', 'cloud'),
                fc_lang('应用配置') => array(APP_DIR.'/admin/home/index', 'cog'),
                fc_lang('更新缓存') => array(APP_DIR.'/admin/home/cache', 'refresh')
            )),
            'module' => $mod,
            'module_app' => isset($config['related']) ? 1 : 0,
        ));

        return $data;
    }

    // 应用安装继承类
    public function _admin_install() {

        !$this->is_auth('admin/application/install') && $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'application/install'));

        $this->db->where('dirname', APP_DIR)->count_all_results('application') && $this->admin_msg(fc_lang('应用【%s】已经存在，安装失败', APP_DIR));
        
        // 插入应用数据库
        $id = $this->application_model->add(APP_DIR);

        // 插入初始化数据
        if (is_file(FCPATH.'app/'.APP_DIR.'/config/install.sql')
            && $install = file_get_contents(FCPATH.'app/'.APP_DIR.'/config/install.sql')) {
            $_sql = str_replace(
                array('{dbprefix}', '{appid}', '{appdir}', '{siteid}'),
                array($this->db->dbprefix, $id, APP_DIR, SITE_ID),
                $install
            );
            $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
            foreach($sql_data as $query) {
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

        // 安装菜单
        if (is_file(FCPATH.'app/'.APP_DIR.'/config/menu.php')) {
            $menu = require FCPATH.'app/'.APP_DIR.'/config/menu.php';
            $this->system_model->add_app_menu($menu, APP_DIR, $id);
        }

        return $id;
    }

    // 应用卸载继承类
    public function _admin_uninstall() {

        !$this->is_auth('admin/application/uninstall') && $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'application/uninstall'));
       
        $data = $this->application_model->get(APP_DIR);

        // 删除菜单
        $this->db->where('mark', 'app-'.APP_DIR)->delete('admin_menu');
        $this->db->where('mark', 'app-'.APP_DIR)->delete('member_menu');

        // 删除缓存
        $this->dcache->delete('app-'.APP_DIR);

        // 删除应用表数据
        $this->application_model->del($data['id']);

        // 插入初始化数据
        if (is_file(FCPATH.'app/'.APP_DIR.'/config/uninstall.sql')
            && $install = file_get_contents(FCPATH.'app/'.APP_DIR.'/config/uninstall.sql')) {
            $_sql = str_replace(
                array('{dbprefix}', '{appid}', '{appdir}', '{siteid}'),
                array($this->db->dbprefix, $data['id'], APP_DIR, SITE_ID),
                $install
            );
            $sql_data = explode(';SQL_FINECMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_FINECMS_EOL', $_sql)));
            foreach($sql_data as $query) {
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

        return $data;
    }

    // 应用缓存继承类
    public function _admin_cache() {

        $data = $this->application_model->get(APP_DIR);
        $config = require APPPATH.'config/app.php';

        $data['name'] = $config['name'];
        $data['related'] = $config['related'];

        $this->application_model->cache();
        $this->dcache->set('app-'.APP_DIR, $data);
        
        return $data;
    }

    // 取消应用在当前模块中时的执行方法
    public function _delete_for_module($dir) {

    }
}