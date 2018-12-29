 $(document).ready(function(){

//  正则 验证手机号
function isPhoneNo(phone) { 
 var pattern = /^1[34578]\d{9}$/; 
 return pattern.test(phone); 
}
//  正则 验证邮箱
function isemailNo(email) { 
 var pattern = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
 return pattern.test(email); 
}
      //  用户名  $('input[name="login_user"]')
      //  密码   $('input[name="login_password"]')
      //  验证码  $('input[name="login_yzm"]')
       
       //验证登录

 	$('input[name="login_submit"]').click(function(){
 		var setter = 0;
 		if($('input[name="login_user"]').val() == "" || $('input[name="login_user"]').val() == null){
 			alert("请输入账号！");
 		}else if($('input[name="login_password"]').val() == "" || $('input[name="login_password"]').val() == null){
 			alert("亲，密码不见了！");
 		}else if($('input[name="login_yzm"]').val() == "" || $('input[name="login_yzm"]').val() == null){
 			alert("验证码给我写上去！");
 		}else{
 		if (isPhoneNo($('input[name="login_user"]').val())) {
 			setter = 1 ;
}else if (isemailNo($('input[name="login_user"]').val())) {
		setter = 2 ;
}

var obj={login_user:$("input[name='login_user']").val(),login_password:$("input[name='login_password']").val(),login_yzm:$("input[name='login_yzm']").val(),setter:setter};
 			$.ajax({
 				type:'POST',
 				url: "index/login_register",
 				data: {obj:obj},
 				dataType:'json',
 				success:function(data){
 					var data = jQuery.parseJSON(data);
 					if(data.static == 0){
 						alert(data.message);
 						$('#captcha').trigger("click");
 					}else{
 						window.location.href = "index";
 					}
 				}
 			})
 		 }
 	})

 	//切换短信界面
 	$('#qhdlfs').click(function(){
 		$('#qhdlfs').hide();
 		$('#zhmmdl').show();
 		$('#login_zh').hide();
 		$('.mfzc_yjx').show();
 		$('#login_sms').show();
 	})
 	//切换账号界面
 	$('#zhmmdl').click(function(){
 		$('#zhmmdl').hide();
 		$('#qhdlfs').show();
 		$('#login_sms').hide();
 		$('#login_zh').show();
 		$('.mfzc_yjx').hide();
 	})
 	 //发送短信验证码
 $('.sendsmccode').click(function(){
	 		if($("input[name='login_user_mobile']").val() == "")
		 	{
		 			alert("请输入手机号码");
		 			return false;
		 	}else if(!isPhoneNo($("input[name='login_user_mobile']").val()))
	 		{
	 			alert("手机号码格式不正确");
	 			return false;
	 		}else{
	 			var setdatatime = 59;
	 						$('.sendsmccode').css("pointer-events",'none');
	 						$('.sendsmccode').html(60 + "秒后重新获取");
	 						var setdata = setInterval(function(){
	 							if (setdatatime != 0) {
	 								$('.sendsmccode').html(setdatatime + "秒后重新获取");
	 								setdatatime = setdatatime - 1;
	 							}else{
	 								clearInterval(setdata);
	 								$('.sendsmccode').html("获取短信验证码");
	 								$('.sendsmccode').css("pointer-events",'');
	 							}
	 						},1000)
	 			$.ajax({
	 				type:'POST',
	 				data: {mobile:$("input[name='login_user_mobile']").val(),yzm:$("input[name='mobile_phone_mobile']").val()},
	 				url:'index/SendSMS',
	 				dataType:'json',
	 				success:function(data){
	 					var data = jQuery.parseJSON(data);
	 					if(data.staic == 0){
	 						alert("123");
	 					}else{
	 						alert(data.message);
	 					}
	 				}
	 			})
	 		}
       })
})