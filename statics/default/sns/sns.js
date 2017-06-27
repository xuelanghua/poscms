/**
 * Created by dayrui on 14-8-7.
 */
(function($) {
    $.fn
        .extend({
            insertContent : function(myValue, t) {
                var $t = $(this)[0];
                if (document.selection) { // ie
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                    sel.moveStart('character', -l);
                    var wee = sel.text.length;
                    if (arguments.length == 2) {
                        var l = $t.value.length;
                        sel.moveEnd("character", wee + t);
                        t <= 0 ? sel.moveStart("character", wee - 2 * t
                            - myValue.length) : sel.moveStart(
                            "character", wee - t - myValue.length);
                        sel.select();
                    }
                } else if ($t.selectionStart
                    || $t.selectionStart == '0') {
                    var startPos = $t.selectionStart;
                    var endPos = $t.selectionEnd;
                    var scrollTop = $t.scrollTop;
                    $t.value = $t.value.substring(0, startPos)
                        + myValue
                        + $t.value.substring(endPos,
                        $t.value.length);
                    this.focus();
                    $t.selectionStart = startPos + myValue.length;
                    $t.selectionEnd = startPos + myValue.length;
                    $t.scrollTop = scrollTop;
                    if (arguments.length == 2) {
                        $t.setSelectionRange(startPos - t,
                            $t.selectionEnd + t);
                        this.focus();
                    }
                } else {
                    this.value += myValue;
                    this.focus();
                }
            }
        })
})(jQuery);
// 插入表情
function dr_emotion(value) {
    $("#dr_content").insertContent(' ['+value+'] ');
}
// 显示表情
function dr_get_face() {
    $("#emotions").show(200);
}
// 插入@
function dr_insert_user(value) {
    $("#dr_content").insertContent(' @'+value+' ');
}
//过滤html标签
function strip_tags (input, allowed) {
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}
// 添加话题
function dr_huati_add(){
    var name = strip_tags($('#huati_name').val());
    if (!name || name=='') {
        dr_tips('话题不能为空！');
        $('#huati_name').focus();
        return;
    }
    $("#dr_content").insertContent(' #'+name+'# ');
    $('#huati_name').val('');
}

/**
 * 微博多图插入Js核心插件
 */
dr_multimage = {
    /**
     * 工厂模式调用初始化
     * @param object attrs 初始化参数对象
     * @return void
     */
    _init: function (attrs) {
        if (attrs.length === 4) {
            core.multimage.init(attrs[1], attrs[2], attrs[3]);
        } else if (attrs.length === 3) {
            core.multimage.init(attrs[1], attrs[2]);
        } else if (attrs.length === 2) {
            core.multimage.init(attrs[1]);
        } else {
            return false;
        }
    },
    /**
     * 初始化操作执行
     * @param object obj 点击的DOM节点对象
     * @param object textarea 输入框DOM对象
     * @param object postbtn 发布按钮DOM对象
     * @return {[type]}          [description]
     */
    init: function (obj, textarea, postbtn) {
        this.obj = obj;
        this.textarea = textarea;
        this.postbtn = postbtn;
        // 创建显示弹窗DIV
        core.multimage.createDiv();
    },
    /**
     * 创建图片显示DIV弹窗
     * @return void
     */
    createDiv: function () {
        var _this = this;
        // 判断弹窗是否存在
        if ($('#multi_image').length > 0) {
            return false;
        }
        $('.attach-file').remove();
        // body点击事件绑定
        $('body').bind('click',function(event){
            var obj = ('undefined' !== typeof event.srcElement) ? event.srcElement : event.target;
            if ($(obj).attr('event-node') === 'insert_file') {
                core.multimage.removeDiv();
            }
        });
    },
    /**
     * 移除多图窗口
     * @return void
     */
    removeDiv: function () {
        var multiImageNode = $('#multi_image')[0];
        if (multiImageNode != null) {
            multiImageNode.parentNode.removeChild(multiImageNode);
        }
        $('#attach_ids').remove();
    },
    /**
     * 移除图片接口
     * @param string unid ID的字符串
     * @param integer index 索引数
     * @param integer attachId 附件ID
     * @return void
     */
    removeImage: function (unid, index, attachId) {
        // 移除附件ID数据
        dr_multimage.upAttachVal('del', attachId);
        // 移除图像
        $('#li_'+unid+'_'+index).remove();
        // 移除附件ID项
        ($('#ul_'+unid).find('li').length - 1 === 0) && $('#attach_ids').remove();
        // 动态设置数目
        dr_multimage.upNumVal(unid, 'dec');
    },
    /**
     * 更新附件表单值
     * @return void
     */
    upAttachVal: function (type, attachId) {
        var attachVal = $('#attach_ids').val();
        var attachArr = attachVal.split('|');
        var newArr = [];
        type === 'add' && attachArr.push(attachId);
        for (var i in attachArr) {
            if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
                newArr.push(attachArr[i]);
            }
        }
        $('#attach_ids').val('|' + newArr.join('|') + '|');
    },
    /**
     * 更新上传显示数目
     * @param string unid 唯一ID
     * @param string type 更新类型，inc增加；dec减少
     * @return void
     */
    upNumVal: function (unid, type) {
        var $uploadNum = $('#upload_num_'+unid),
            $totalNum = $('#total_num_'+unid);
        switch (type) {
            case 'inc':
                // 动态设置数目 - 增加
                $uploadNum.html(parseInt($uploadNum.html()) + 1);
                $totalNum.html(parseInt($totalNum.html()) - 1);
                break;
            case 'dec':
                // 动态设置数目 - 减少
                $uploadNum.html(parseInt($uploadNum.html()) - 1);
                $totalNum.html(parseInt($totalNum.html()) + 1);
                break;
        }
    },
    /**
     * 添加loading效果
     * @param string unid 唯一ID
     * @return void
     */
    addLoading: function (unid) {
        var loadingHtml = '<li id="loading_'+unid+'" class="load"><span><img src="'+THEME_URL+'sns/loading.gif" /></span></li>';
        $('#btn_'+unid).before(loadingHtml);
    },
    /**
     * 移除loading效果
     * @param string unid 唯一ID
     * @return void
     */
    removeLoading: function (unid) {
        $('#loading_'+unid).remove();
    }
};

// 搜索好友
function dr_find_user(gid) {
    $("#group-2").html('加载中...');
    $("#groups li").attr('class', '');
    $("#dr_group_"+gid).attr('class', 'current');
    $.get(memberpath+"index.php?s=member&mod=space&c=sns&m=select_user&gid="+gid, function(data){
        if (data) {
            $("#group-2").html(data);
        } else {
            $("#group-2").html('没有了');
        }
    });
}

// 发布微博
function dr_post() {
    var content = $('#dr_content').val();
    if (!content || content == '') {
        dr_tips('请填写内容');
        $('#dr_content').focus();
        return false;
    }
    $.post(memberpath+"index.php?s=member&mod=space&c=sns&m=post", {content:content, attach: $('#attach_ids').val()}, function(data){
        if (data.status == 1) {
            dr_tips('发布成功', 2, 1);
            $('#dr_content').val('');
            $('#upload_num_weibo').html('0');
            $('#total_num_weibo').html('9');
            $('#attach_ids').val('');
            $('.dr_row_li').remove();
            $.get(moreurl+'&more=1', function(data){
                if (data == 'null') return;

                $("#feed-lists").html(data);
                $("#dr_page").val(2);
            });
        } else {
            dr_tips(data.code);
            $('#dr_content').focus();
        }
    }, 'json');
}

// 转发
function dr_sns_repost(id) {
    // 创建窗口
    var throughBox = $.dialog.through;
    var dr_Dialog = throughBox({
        title: '转发',
        opacity: 0.1
    });
    var url = memberpath+'index.php?s=member&mod=space&c=sns&m=repost&id='+id;
    // ajax调用窗口内容
    $.ajax({type: "GET", url:url, dataType:'text', success: function (text) {
        var win = $.dialog.top;
        dr_Dialog.content(text);
        // 添加按钮
        dr_Dialog.button({name: '转发', callback:function() {
            var content = win.$("#dr_content").val();
            $.ajax({type: "POST",dataType:"json", url: url, data: {content: content}, success: function(data) {
                    if (data.status == 1) {
                        dr_tips(data.code, 3, 1);
                        $.get(moreurl+'&more=1', function(data){
                            $("#feed-lists").html(data);
                            $("#dr_page").val(2);
                        });
                    } else {
                        dr_tips(data.code);
                        win.$('#dr_content').focus();
                    }
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {

                }
            });
            },
            focus: true
        });
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {

        }
    });
    return;
}

// 提交评论
function dr_sns_comment_post(id) {
    var content = strip_tags($('#comment_content_'+id).val());
    if (!content || content == '') {
        dr_tips('请填写评论内容');
        return false;
    }
    $.post(memberpath+"index.php?s=member&mod=space&c=sns&m=comment&id="+id, {content:content}, function(data){
        if (data.status == 1) {
            dr_tips('评论成功', 2, 1);
            dr_sns_list_comment(id, 1);
            $("#dr_comment_"+id).toggle();
        } else {
            dr_tips(data.code);
        }
    }, 'json');
}

// 列表评论
function dr_sns_list_comment(id, page) {
    $("#dr_comment_"+id).toggle();
    $('#comment_content_'+id).val('');
    $.get(memberpath+'index.php?s=member&mod=space&c=sns&m=comment_list&more=1&id='+id+'&page='+page, function(data){
        $('#commentlist_'+id).html(data);
    });
}


// 回复评论
function dr_recomment(id, username) {
    $('#comment_content_'+id).focus();
    $('#comment_content_'+id).val('@'+username+' ');
}

// 赞
function dr_sns_digg(id) {
    $.get(memberpath+'index.php?s=member&mod=space&c=sns&m=digg&id='+id, function(data){
        $('#dr_digg_'+id).html(data);
    });
}

// 收藏
function dr_sns_favorite(id) {
    $.get(memberpath+'index.php?s=member&mod=space&c=sns&m=favorite&id='+id, function(data){
        $('#dr_favorite_'+id).html(data);
    });
}

// 删除动态
function dr_sns_delete(id) {
    art.dialog.confirm("<font color=red><b>你确认要删除吗？</b></font>", function(){
        $.ajax({type: "POST",dataType:"json", url: memberpath+'index.php?s=member&mod=space&c=sns&m=delete&id='+id, success: function(data) {
            if (data.status == 1) {
                dr_tips(data.code, 3, 1);
                $("#dr_row_"+id).remove();
            } else {
                dr_tips(data.code);
            }
            art.dialog.close();
            return false;
        },
            error: function(HttpRequest, ajaxOptions, thrownError) {

            }
        });
        return true;
    });
    return false;
}

// 删除动态
function dr_sns_delete(id) {
    art.dialog.confirm("<font color=red><b>你确认要删除吗？</b></font>", function(){
        $.ajax({type: "POST",dataType:"json", url: memberpath+'index.php?s=member&mod=space&c=sns&m=delete&id='+id, success: function(data) {
            if (data.status == 1) {
                dr_tips(data.code, 3, 1);
                $("#dr_row_"+id).remove();
            } else {
                dr_tips(data.code);
            }
            art.dialog.close();
            return false;
        },
            error: function(HttpRequest, ajaxOptions, thrownError) {

            }
        });
        return true;
    });
    return false;
}

// 删除动态2
function dr_sns_delete2(id) {
    art.dialog.confirm("<font color=red><b>你确认要删除吗？</b></font>", function(){
        $.ajax({type: "POST",dataType:"json", url: memberpath+'index.php?s=member&mod=space&c=sns&m=delete&id='+id, success: function(data) {
            if (data.status == 1) {
                dr_tips(data.code, 3, 1);
                setTimeout('window.location.href="'+memberpath+'index.php?s=member&mod=space&c=sns&m=index"', 2000);
            } else {
                dr_tips(data.code);
            }
            art.dialog.close();
            return false;
        },
            error: function(HttpRequest, ajaxOptions, thrownError) {

            }
        });
        return true;
    });
    return false;
}

// 删除评论
function dr_sns_delete_comment(id) {
    art.dialog.confirm("<font color=red><b>你确认要删除吗？</b></font>", function(){
        $.ajax({type: "POST",dataType:"json", url: memberpath+'index.php?s=member&mod=space&c=sns&m=delete_comment&id='+id, success: function(data) {
            if (data.status == 1) {
                dr_tips(data.code, 3, 1);
                $("#dr_row_comment_"+id).remove();
            } else {
                dr_tips(data.code);
            }
            art.dialog.close();
            return false;
        },
            error: function(HttpRequest, ajaxOptions, thrownError) {

            }
        });
        return true;
    });
    return false;
}

// 监听会员资料
$(function(){
    $('a[event-node="face_card"]').mouseenter(function(){
        $('.face_card').hide();
        var uid = $(this).attr('uid');
        var obj = $(this);
        dr_facecard.init();
        dr_facecard.show(obj, uid);
    });
    $('a[event-node="face_card"]').mouseleave(function(){
        dr_facecard.hide();
    });
    $('a[event-node="face_card"]').blur(function(){
        dr_facecard.hide();
    });
    //
});

/**
 * 小名片JS模型
 */
dr_facecard ={
    //给工厂调用的接口
    _init:function(attrs){
        this.init();
    },
    init:function(){

    },
    show:function(obj,uid){

    },
    deleteUser:function(uid){

    },
    setCss:function(obj){	//计算位置

    },
    hide:function(){

    },
    dohide:function(){//强制隐藏

    }
};


