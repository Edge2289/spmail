$(document).ready(function(){


$('.right_login').click(function(){
	alert('123');
})

$('.right_register').click(function(){
	alert('56');
})


	//定时刷新限时秒杀时间
	setInterval(function(){
	var myDate = new Date();
	var h=myDate.getHours();       //获取当前小时数(0-23)
	var m=myDate.getMinutes();     //获取当前分钟数(0-59)
	var s=myDate.getSeconds();     //获取当前秒数(0-59)
	if(s < 10){
		s = '0'+s;  
	}
	if(h < 10){
		h = '0'+h;  
	}
	if(m < 10){
		m = '0'+m;  
	}
		$('.hh').html(h);
		$('.mm').html(m);
		$('.ss').html(s);
	},1000);


$('.disenk').each(function(){
	$(this).hover(function(){
		$(this).children('.hovshz').show();
	},function(){
		$(this).children('.hovshz').hide();})
})

$('.hovshz').each(function(){
	$(this).hover(function(){
		$(this).prev('.pale-dis').trigger('hover');
		$(this).prev('.pale-dis').addClass('innii');
	},function(){
		$(this).prev('.pale-dis').removeClass('innii');
	})
})

$('.J_Ajax').each(function(){
	$(this).click(function(){
		var link = window.location.href.split('?')[0];
		var url = GetRequest(); //获取url中"?"符后的字串
		// console.log(url['index']);
		if (!url['ppah']) {
			url['ppah'] = $(this).attr("data-value");
		}else{
			url['ppah'] +=  "^"+$(this).attr("data-value");
		}
		var usr_link = '';
		//遍历
$.each(url,function(_key){
    var key = _key;
    var value = url[_key];
    usr_link += "&"+String(key)+"="+String(value);
});
console.log(link+"?"+usr_link.substr(1));

		console.log($(this).attr("data-value"));
	})
})

function GetRequest() {
  var url = location.search; //获取url中"?"符后的字串
  var theRequest = new Object();
  if (url.indexOf("?") != -1) {
    var str = url.substr(1);
    strs = str.split("&");
    for(var i = 0; i < strs.length; i ++) {
      theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
    }
  }
  return theRequest;
}

})