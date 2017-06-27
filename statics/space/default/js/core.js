
// 关注
function dr_guanzhu(_this) {
	var url = memberpath+"index.php?s=space&c=api&m=guanzhu&uid="+$(_this).attr('uid')+"&"+Math.random();
	$.ajax({type: "GET", url:url, dataType:'jsonp',jsonp:"callback",async: false,
	    success: function (data) {
			if (data.status == 0) {
				dr_tips(data.msg);
			} else if (data.msg == 0) {
				dr_tips('关注失败');
			} else if (data.msg == 1) {
				dr_tips('关注成功', 2, 1);
				$(_this).addClass('unfollow');
				$(_this).html('取消关注');
			} else if (data.msg == 2) {
				dr_tips('相互关注', 2, 1);
				$(_this).addClass('unfollow');
				$(_this).html('取消关注');
			} else if (data.msg == -1) {
				dr_tips('取消关注', 2, 1);
				$(_this).removeClass('unfollow');
				$(_this).html('<em></em>加关注');
			}
	    }
	});
}