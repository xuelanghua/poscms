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


// 搜索好友
function dr_find_user(gid) {
    $("#group-2").html('加载中...');
    $("#groups li").attr('class', '');
    $("#dr_group_"+gid).attr('class', 'current');
    $.ajax({type: "GET", url: memberpath+"index.php?s=member&mod=space&c=sns&m=select_user&gid="+gid, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (text) {
            var data = text.html;
            if (data) {
                $("#group-2").html(data);
            } else {
                $("#group-2").html('没有了');
            }
        }
    });
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
    $.ajax({type: "GET", url:url, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (text) {
            var win = $.dialog.top;
            dr_Dialog.content(text.html);
            // 添加按钮
            dr_Dialog.button({name: '转发', callback:function() {
                var content = win.$("#dr_content").val();
                $.ajax({type: "GET", url:url+'&submit=1', data: {content: content}, dataType:'jsonp',jsonp:"callback",async: false,
                    success: function (data) {
                        if (data.status == 1) {
                            dr_tips(data.code, 3, 1);
                            setTimeout('window.location.reload(true)', 3000); // 刷新页
                        } else {
                            dr_tips(data.code);
                            win.$('#dr_content').focus();
                        }
                    }
                });
            },focus: true});
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
    $.ajax({type: "GET", url:memberpath+"index.php?s=member&mod=space&c=sns&m=comment&id="+id+"&content="+content, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (data) {
            if (data.status == 1) {
                dr_tips('提交成功', 2, 1);
                dr_sns_list_comment(id, 1);
                $("#dr_comment_"+id).toggle();
            } else {
                dr_tips(data.code);
            }
        }
    });
}

// 列表评论
function dr_sns_list_comment(id, page) {
    $("#dr_comment_"+id).toggle();
    $('#comment_content_'+id).val('');
    $.ajax({type: "GET", url: memberpath+'index.php?s=member&mod=space&c=sns&m=comment_list&more=1&id='+id+'&page='+page, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (text) {
            $('#commentlist_'+id).html(text.html);
        }
    });
}


// 回复评论
function dr_recomment(id, username) {
    $('#comment_content_'+id).focus();
    $('#comment_content_'+id).val('@'+username+' ');
}

// 赞
function dr_sns_digg(id) {
    $.ajax({type: "GET", url: memberpath+'index.php?s=member&mod=space&c=sns&m=digg&id='+id, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (text) {
            $('#dr_digg_'+id).html(text.html);
        }
    });
}

// 收藏
function dr_sns_favorite(id) {
    $.ajax({type: "GET", url: memberpath+'index.php?s=member&mod=space&c=sns&m=favorite&id='+id, dataType:'jsonp',jsonp:"callback",async: false,
        success: function (text) {
            $("#dr_favorite_"+id).html(text.html);
        }
    });
}



// 删除评论
function dr_sns_delete_comment(id) {
    art.dialog.confirm("<font color=red><b>你确认要删除吗？</b></font>", function(){
        $.ajax({type: "GET", url: memberpath+'index.php?s=member&mod=space&c=sns&m=delete_comment&id='+id, dataType:'jsonp',jsonp:"callback",async: false,
            success: function (data) {
                if (data.status == 1) {
                    dr_tips(data.code, 3, 1);
                    $("#dr_row_comment_"+id).remove();
                } else {
                    dr_tips(data.code);
                }
                art.dialog.close();
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

