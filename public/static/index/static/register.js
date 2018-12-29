 $(document).ready(function(){
 

// //密码提示
// $('input[name="email_pwd"]').keyup(function(e) {
//  var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
//  var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
//  var enoughRegex = new RegExp("(?=.{6,}).*", "g");
//  if (false == enoughRegex.test($(this).val())) {
//  $('#passstrength').html('密码设置太简单，安全性弱');
//  }else if (strongRegex.test($(this).val())){
//  $('#passstrength').className = 'ok';
//  $('#passstrength').html('强壮的!');
//  }else if (mediumRegex.test($(this).val())) {
//  $('#passstrength').className = 'alert';
//  $('#passstrength').html('中等的！！!');
//  }else {
//  $('#passstrength').className = 'error';
//  $('#passstrength').html('微弱的!');
//  }
//  return true;
// });

// 隐藏 邮箱

$("input[name='email']").focus(function(){
$('.email_re').hide();
});

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


 		// 邮箱验证
 	$("input[name='email_register']").click(function(){
 		if($("input[name='email']").val() == ""){
 			$('.email_re').show();
 			return false;
 		}else if(!isemailNo($("input[name='email']").val())){
 			$('.email_re').show();
 			return false;
 		}else if(!$("input[name='emaile_reader']").is(':checked')){
 			clearInterval(setdata);
		 	$('#captcha').trigger("click");
		 	$('.mine_coupon_words').html("请勾选商城协议");
		 	$('.mine_coupon_words').fadeIn("slow");
		 	var setdata = setInterval(function(){
			$('.mine_coupon_words').fadeOut("slow");
		 	},3000);
 			return false;
 		}else{
var obj={__token__:$("input[name='__token__']").val(),email:$("input[name='email']").val(),email_yzm:$("input[name='email_yzm']").val(),pwd:$("input[name='email_pwd']").val(),code:$("input[name='emailcode']").val()};
		 		$.ajax({
		 			type : 'POST',
		 			url: "/index/Redister/email_register",
		 			data : {obj:obj},
		 			async : false,
		 			dataType: 'json',
		 			success:function(data){
		 				var data = jQuery.parseJSON(data);
		 					clearInterval(setdata);
		 					$('#captcha').trigger("click");
		 					$('.mine_coupon_words').html(data.message);
		 					$('.mine_coupon_words').fadeIn("slow");
		 					setInterval(function(){
				 				if(data.static == 1){
		 								window.location.href = '/index/login/index';
				 				}
							$('.mine_coupon_words').fadeOut("slow");
		 					},3000);
		 			},
		 		})
 			}
 	})
$('.sendemailcode').click(function(){
		if($("input[name='email']").val() == ""){
		 			$('.email_re').show();
		 			return false;
		 		}else if(!isemailNo($("input[name='email']").val())){
		 			$('.email_re').show();
		 			return false;
		 		}else{
	 			var setdatatime = 59;
	 						$('.sendemailcode').css("pointer-events",'none');
	 						$('.sendemailcode').html(60 + "秒后重新获取");
	 						var setdata = setInterval(function(){
	 							if (setdatatime != 0) {
	 								$('.sendemailcode').html(setdatatime + "秒后重新获取");
	 								setdatatime = setdatatime - 1;
	 							}else{
	 								clearInterval(setdata);
	 								$('.sendemailcode').html("获取邮箱验证码");
	 								$('.sendemailcode').css("pointer-events",'');
	 							}
	 						},1000)
	 			$.ajax({
	 				type:'POST',
	 				data: {email:$("input[name='email']").val()},
	 				url: "/index/Redister/email",
	 				dataType:'json',
	 				success:function(data){
	 					var data = jQuery.parseJSON(data);
	 					if(data.staic == 0){
	 						alert(data.message);
	 					}
	 				}
	 			})
	 		}
})
 //发送短信验证码
 $('.sendsmccode').click(function(){
	 		if($("input[name='mobile']").val() == "")
		 	{
		 			alert("请输入手机号码");
		 			return false;
		 	}else if(!isPhoneNo($("input[name='mobile']").val()))
	 		{
	 			alert("手机号码格式不正确");
	 			return false;
	 		}else if($("input[name='hidde']").attr('index') == 0){
	 			$('.hidden').show();
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
	 				data: {mobile:$("input[name='mobile']").val()},
	 				url: "/index/Redister/sendSMSList",
	 				dataType:'json',
	 				success:function(data){
	 					var data = jQuery.parseJSON(data);
	 					if(data.staic == 0){
	 						alert(data.message);
	 					}
	 				}
	 			})
	 		}
       })

 	//手机号验证
 	$("input[name='mobile_register']").click(function(){
 		//alert('email_register');email   email_yzm    emaile_reader email_pwd  email_con_pwd
 		var obj={__token__:$("input[name='__token__']").val(),mobile:$("input[name='mobile']").val(),code:$("input[name='mobile_code']").val(),pwd:$("input[name='mobile_pwd']").val()};
 		if(!$("input[name='mobile_reader']").is(':checked')){
 			clearInterval(setdata);
		 	$('#captcha').trigger("click");
		 	$('.mine_coupon_words').html("请勾选商城协议");
		 	$('.mine_coupon_words').fadeIn("slow");
		 	var setdata = setInterval(function(){
			$('.mine_coupon_words').fadeOut("slow");
		 	},3000);
 			return false;
 		}else if(!isPhoneNo(obj['mobile']))
 		{
 			alert("手机号码格式不正确");
 			return false;
 		}else if($("input[name='hidde']").attr('index') == 0)
 		{
 			$('.hidden').show();
 			return false;
 		}else if(obj['code'] == '' || obj['pwd'] == ''){
 			alert("请填写完整信息");
 			return false;
 		}else if (obj['code'].length != 6){
 			alert("验证码位数为6");
 			return false;
 		}else if (obj['pwd'].length <= 5){
 			alert("密码必须超过6位");
 			return false;
 		}
 		$.ajax({
 			type : 'POST',
 			url: "index/Redister/mobile_register",
 			data : {obj:obj},
 			async : false,
 			dataType: 'json',
 			success:function(data){
 				var data = jQuery.parseJSON(data);
 				if(data.static == 0){
 					$('#captcha').trigger("click");
 					alert(data.message);
 				}else{
 					alert(data.message);
 					window.location.href = '/index/login/index';
 				}
 			},
 			error:function(){

 			}
 		})
 	 })

 })