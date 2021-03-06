<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

<div class="module-content">
    <div class="module-content-left">

        <ul class="my-left-content">

            <?php if (is_array($module)) { $count=count($module);foreach ($module as $dir=>$t) { ?>
            <li class=" <?php if ($dir==$dirname) { ?>active open<?php } ?> left-content-<?php echo $dir; ?>">
                <a href="javascript:;" title="<?php echo $t['title']; ?>" onclick="McLink('<?php echo $dir; ?>', '<?php echo $t['url']; ?>')">
                    <i class="<?php echo $t['icon']; ?>"></i> <?php echo dr_strcut($t['name'], 8, ''); ?>
                    <span class="<?php if ($dir==$dirname) { ?>selected<?php } ?>"></span>
                </a>
            </li>
            <?php } } ?>

        </ul>

    </div>

    <div class="module-content-right page-content page-content2">

        <iframe name="contentright" id="module-content-right" src="<?php echo $url; ?>&cache=<?php echo SYS_TIME; ?>" url="<?php echo $url; ?>&cache=<?php echo SYS_TIME; ?>" frameborder="false" scrolling="auto" style="border:none; margin-bottom:0px;" width="100%" height="auto" allowtransparency="true"></iframe>


    </div>
</div>
<style>
    .my-left-content {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .my-left-content li.active.open {
        background: #EFEFEF;
        border-top-color: transparent;
    }
    .my-left-content li.active.open>a {
        color: #555;
    }
    .my-left-content li:hover {
        background: #F9F9F9;
    }
    .my-left-content li:first-child {

        border-top: 0;
    }
    .my-left-content li {
        padding: 10px 0;
        border-top: 1px solid #F0F5F7;
    }
    .my-left-content li .selected {
        display: block;
        float: right;
        position: relative;
        top: -12px;
        right: -8px;
        border-top: 20px double transparent;
        border-bottom: 20px double transparent;
        border-right: 0;
        border-left: 8px solid #EFEFEF;
    }
    .my-left-content li a {
        padding-left: 10px;
        border: 0px;
        padding: 10px 15px;
        padding-left:8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 300;
        color: #555;
    }
    .my-content-top-tool {
        margin-top: -25px;
        margin-bottom: 10px;
    }
    .my-sysfield .control-label{
        text-align: left !important;
        margin-bottom: 10px;!important;
    }
    .my-sysfield .col-md-2 {
        width:100% !important;
    }
    .finecms-tool-row .form-body {
        padding-bottom: 0;
    }
    .finecms-tool-row .form-actions {
        text-align: center;
        padding-bottom: 0;
    }
    .finecms-top-name {
        margin-top:8px
    }
    .table-footer-button {
        margin-top: 12px;
    }
    .table-search-tool .col-md-12 {
        width:auto;
    }
    .table-search-tool .col-md-12 .onloading {
        margin-top:2px;
    }
    .table-search-tool {
        margin-bottom:10px;
    }
    .list-field-cog {
        margin: 10px 0;
    }
    .list-field-cog .btn {
        width: 48px;
    }
    .list-field-cog .form-control, .list-field-cog .btn {
        height: 30px;
        font-size: 12px;
    }

    .module-content-right {
        border-left: 1px solid #e7ecf1;
    }
    .module-content-left {
        margin-top: 63px;
        float: left;
        width:100px;
    }
    .page-content2 {
        padding: 0px !important;
        margin-left: 100px !important;
    }
</style>

<script type="text/javascript">

    function McLink(dir, url) {

        // 延迟提示
        //var admin_loading = layer.load(2, { time: 10000 });


        $('.my-left-content li span').removeClass('selected');
        $('.my-left-content li').removeClass('active open');

        $('.left-content-'+dir+'  ').addClass('active open');
        $('.left-content-'+dir+'  span').addClass('selected');


        $("#module-content-right").attr('src', url);
        $("#module-content-right").attr("url", url);
    }

    function wSize(){
        var str=getWindowSize();
        var strs= new Array(); //定义一数组
        strs=str.toString().split(","); //字符分割
        var heights = strs[0],Body = $('body');
        $('#module-content-right').height(heights);
    }

    var getWindowSize = function(){
        return ["Height","Width"].map(function(name){
            return window["inner"+name] ||
                    document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
        });
    }
    window.onload = function (){
        if(!+"\v1" && !document.querySelector) { // for IE6 IE7
            document.body.onresize = resize;
        } else {
            window.onresize = resize;
        }
        function resize() {
            wSize();
            return false;
        }
    }
    $(function(){
        wSize();
        document.documentElement.style.overflowY = 'hidden'
    });
</script>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include);  exit; ?>