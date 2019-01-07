 
$(document).ready(function(){
	  //speclist 操作函数
	 $('#message_img').next('span').hide();
	$('#file_upload').click(function(){
		$('#file_upload_img').trigger('click');
	})
	//文字
	$('.button_add').each(function(){
		$(this).click(function(){
			$(this).toggleClass('button_add_color');
			$(this).next().removeAttr("disabled");
				var data = new Array();
					var index = 0;
				$('.button_add_color').each(function(){
					data[index] = $(this).attr('data-spec_id')+'_M_n_'+$(this).attr('value');
					index++;
				})
				
			//异步传输显示规格价钱显示
			$.ajax({
					type : 'post',
					url : "/admin/goods/getattr_index_list",  //测试模块
					// url : "getattr_index_list",   //框架模块
					data : {'data':JSON.stringify(data),'id':$('.goods_hidden_id').attr('value')},
					dataType : 'html',
					success:function(html){
						var html = jQuery.parseJSON(html);
						$('.speclist_mou_index').html('');
						$('.speclist_mou_index').html(html.info);
						hbdyg();
					},
					error:function(){
						$('.speclist_mou_index').html('');
						layer.msg("出错");
					}
			})
		})
	})


 function hbdyg() {
            var tab = document.getElementById("spec_input_tab"); //要合并的tableID
            var maxCol = tab.rows.item(0).cells.length-4, val, count, start;  //maxCol：合并单元格作用到多少列  
            if (tab != null) {
                for (var col = maxCol - 1; col >= 0; col--) {
                    count = 1;
                    val = "";
                    for (var i = 0; i < tab.rows.length; i++) {
                        if (val == tab.rows[i].cells[col].innerHTML) {
                            count++;
                        } else {
                            if (count > 1) { //合并
                                start = i - count;
                                tab.rows[start].cells[col].rowSpan = count;
                                for (var j = start + 1; j < i; j++) {
                                    tab.rows[j].cells[col].style.display = "none";
                                }
                                count = 1;
                            }
                            val = tab.rows[i].cells[col].innerHTML;
                        }
                    }
                    if (count > 1) { //合并，最后几行相同的情况下
                        start = i - count;
                        tab.rows[start].cells[col].rowSpan = count;
                        for (var j = start + 1; j < i; j++) {
                            tab.rows[j].cells[col].style.display = "none";
                        }
                    }
                }
            }
        }
        $('.Get_add_cate_img').each(function(){
        	$(this).click(function(){
        		var $this = $(this);
				var geshi = "jpg|png|jpeg|bmp";
        		$('#upload_speclist_img_odd').trigger("click");
        		var ClickNum = 0;
        		$('#upload_speclist_img_odd').on('change',function(){
        			if (ClickNum++ == 0) {
        				var formData = new FormData();
        			var file = this.files[0] ? this.files[0] : null;
        			var suffix = file.name.substr(file.name.lastIndexOf("."));
					var suffix = suffix.substring(1).toLowerCase();
					if(geshi.indexOf(suffix) == -1){
						alert("请选择 jpg|png|jpeg|bmp 格式的图片");
						return false;
					}
        			formData.append('file',file);
	        			$.ajax({
								type : 'post',
								url :  '/admin/goods/ajaxSpeclistUpload',
								data  : formData,
						        processData: false,//此参数必加
						        contentType: false,//此参数必加
								dataType : 'json',
								success:function(data){
									var data = jQuery.parseJSON(data);
									if (data["code"] == 1) {
										$this.attr("src",data["data"]);
										$this.next().attr("value",data["data"]);
										$this.next().removeAttr("disabled");
									}else{
										alert(data["msg"]);
									}
								},
								error:function(){
									alert("请重新上传，接口出错！！！");
								}
						})
        			}
        		})
        	})
        })

	//图片
	$('#speclist_wuxiao').each(function(){
		$(this).click(function(){
			alert($(this).attr('src'));
		})
	})

	// 显示小图片  goods_message
	$('.Get_add_cate_img').mouseover(function(){
		var AbUrl = $(this).attr('src');
		if(AbUrl != "/static/image/images/add-button.jpg")
		{
			layer.tips('<img class="img_width_height" src="'+AbUrl+'">',this,{tips: [1, '#fff']});
		}
		
	})
	$('.Get_add_cate_img').mouseout(function(){
		layer.closeAll();
	})

	// 显示小图片  goods_message
	$('#upload_class_img').mouseover(function(){
		layer.tips('<img class="img_width_height" src="'+$(this).attr('index')+'">',this,{tips: [1, '#fff']});
	})
	$('#upload_class_img').mouseout(function(){
		layer.closeAll();
	})
	$('#upload_class_video').mouseover(function(){
		layer.tips('<img class="img_width_height" src="'+$(this).attr('index')+'">',this,{tips: [1, '#fff']});
	})
	$('#upload_class_video').mouseout(function(){
		layer.closeAll();
	})
	/**********************************************************************************/

  $('.submit_congfig').click(function(){
    var num = $('input[name="ses_c_num"]').val();//|| isNaN(parseInt(num))
    if (num>6 || num <= 0 || isNaN(parseInt(num))) {
      layer.msg("验证码数量属于错误");
      return false;
    }
    $.ajax({
        type : 'post',
        url : '/admin/Configtemplate/ajaxemeimporsave',
        data : {'data':$('#SceneAjaxFromSW').serialize()},
        dataType : 'json',
        success:function($data){
          if ($data['info'] == 0) {
            layer.msg($data['msg']);
          }else{
            layer.msg($data['msg']);
          }
        },
        error:function(){
          layer.msg("出错 :(");
        }
    })
  })

// 验证手机号
function isPhoneNo(phone) {
    var pattern = /^1[34578]\d{9}$/;
    return pattern.test(phone);
}

$('.onSceneOne').click(function(){
   if (isPhoneNo($('input[name="phone"]').val())) {
    var keyid = $('input[name="ses_c_keyid"]').val();
    var ks = $('input[name="ses_c_ks"]').val();
    var phone = $('input[name="phone"]').val();
    var num = $('input[name="ses_c_num"]').val();
    if (num>6 || num <= 0 || isNaN(parseInt(num))) {
      layer.msg("验证码数量属于错误");
      return false;
    }
    $('.onSceneOne').html('60秒');
    $('.onSceneOne').attr('disabled','disabled');
    $('.onSceneOne').css('cursor','not-allowed');
    $('.onSceneOne').css('background','#e0e0e0');
    $('.onSceneOne').css('color','#009688');
    var min = 60;
    var srt = setInterval(function(){
       min = min - 1;
        if (min==0) {
          $('.onSceneOne').html('发送');
          $('.onSceneOne').removeAttr("disabled"); 
          $('.onSceneOne').css('cursor','pointer');
          $('.onSceneOne').css('background','#009688');
          $('.onSceneOne').css('color','white');
          clearTimeout(srt);
        }else{
          $('.onSceneOne').html(min+'秒');
        }
    },1000);

    /*** ajax提交 ****/
    $.ajax({
        type : 'post',
        url  : '/admin/Configtemplate/smsSendcofig',
        data  : {'key':keyid,'ks':ks,'phone':phone,'num':num},
        dataType : 'json',
        success:function(info){
          layer.msg(info);
        },
        error:function(){
          layer.msg("出错 :(");
        }
    })
  }else{
    layer.msg('请输入一个正确的手机号  :(');
  }
})

  $('.UserDeRedis').click(function(){
    $.get('/admin/user/deleteRedis',function(data){
      layer.msg(data);
    });
  })



/**********   站内信  ***************/
  $('#UserUserZNX').click(function(){
    var strContent = $('#UserZNXTextArea').val(); // 文本
    strContent = strContent.replace(/\r\n/g, '<br/>'); //IE9、FF、chrome
    strContent = strContent.replace(/\n/g, '<br/>'); //IE7-8
    strContent = strContent.replace(/\s/g, ' '); //空格处理
    var i = $('input[name="UserSetId"]').val();
    var r = $('input[name="Userredio"]').val();
    var inf = r=="whole"?"whole":i;
    var data = new Array();
    console.log(strContent);
    data[0] = strContent;   // 文本内容
    data[1] = inf;          // 列表 - 个别 - 全部
    data[2] = r;            // 类型

    $.ajax({
        type : 'post',
        url  : '/admin/User/ajaxUserAUD',
        data : {'data':JSON.stringify(data),info:'znx'},
        dataType : 'json',
        success:function(data){
          if (data['status'] == 0) {
                   layer.msg(data['info'], {icon: 5},function () {});
                }else{
                   layer.msg(data['info'], {icon: 6},function () {
                    // 获得frame索引
                      var index = parent.layer.getFrameIndex(window.name);
                      //关闭当前frame
                      parent.layer.close(index);
                      setInterval(repla(),2000);});
                  }
        },
        error:function(data){
          layer.msg(data);
        }
    })

  })


/*************  发送邮件  ***************/

$('.userAIEmail').click(function(){
  var e = $('input[name="userEmailName"]').val();
  var t = $('input[name="userEmailTitle"]').val();
  var c = $('#userEmailContent').val();
  var emreg = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/;
  
  if (e == '' || e == null) {
    layer.msg("邮箱不可以为空");
    return false;
  }
  if (!emreg.test(e)) {
    layer.msg("邮箱不符合规则，请重新输入");
    return false;
  }
  if (t == '' || t == null || c == '' || c == null ) {
    layer.msg('标题和内容不可以为空！！');
    return false;
  }
  $.get('/admin/user/SendEmail/',{ e: e, t: t, c: c }, function(data){
    var data = jQuery.parseJSON(data);
    if (data['status'] == 0) {
                   layer.msg(data['info']);
                }else{
                   layer.msg(data['info'], function () {
                    // 获得frame索引
                      var index = parent.layer.getFrameIndex(window.name);
                      //关闭当前frame
                      parent.layer.close(index);
                    });
    }
  })
})






})


/**********************************************************************************************************************************/
//函数区域
function speclist_wuxiao(id){
	$(id).toggleClass('speclist_order_win');
	if ($(id).parent('td').not(this).siblings().children('input').attr('disabled') == 'disabled') {
		$(id).parent('td').not(this).siblings().children('input').removeAttr("disabled");
	}else{
		$(id).parent('td').not(this).siblings().children('input').attr('disabled','disabled')
	}
}


/**
 * [deleteAdd 删除单元格]
 * @return {[type]} [description]
 */
function deleteAdd(obj){
	$(obj).parent('div').parent('th').parent('tr').remove();
	// console.log($(obj).parent('div').parent('th').parent('tr').attr('class'));
}

/**
 * [buttonadd_tj_mm_mo 添加地区]
 * @return {[type]} [description]
 */
function buttonadd_tj_mm_mo(){
  var onecity = $('.onecity').find("option:selected").val();
  var twocity = $('.twocity').find("option:selected").val();
  var thrcity = $('.thrcity').find("option:selected").val();
  var a_index = '';
  var b_index = '';
    if (onecity) {
      if (twocity) {
        if (thrcity) {
            a_index = $('.thrcity').find("option:selected").val();
            b_index = $('.thrcity').find("option:selected").html();
          }else{
            a_index = $('.twocity').find("option:selected").val();
            b_index = $('.twocity').find("option:selected").html();
        }
      }else{
        a_index = $('.onecity').find("option:selected").val();
        b_index = $('.onecity').find("option:selected").html();
      }
    }
    var bb = 0;
    $('input[name="area_list[]"]').each(function(){
      if(a_index == $(this).val()){
        bb++;
      }
    })
    if (a_index!=''&&b_index!='') {
      if (bb == 0) {
        var text = '<li><label><input type="checkbox" checked="checked" name="area_list[]" data-name="'+b_index+'" value="'+a_index+'">'+b_index+'</label></li>';
      $('#listAddcheackBoxli').append(text);
      }else{
        layer.msg("已经存在该区域", {icon: 5});
      }
    }
}

/**
 * 获取城市
 * @param t  省份select对象
 * @param city
 * @param district
 * @param twon
 */
function get_city(obj,id,index){

  $.ajax({
        type : "get",
        url  : "/admin/Configtemplate/getcity",
        data : {"id":id,'index':index},
        dataType : "json",
        success:function(data){
          var type = ".twocity";
          var city = "<option>请选择城市</option>";
          var option = '';
          if (data['info'] == '2') {
            var type = ".thrcity";
            var city = "<option>请选择地区</option>";
          }else{
            $(".thrcity").html('');
          }
          $(type).html('');
          $(type).append(city);
          $.each($.parseJSON(data['data']), function(index, data){
                var option = "<option value='" + data.Id + "'>" + data.Name + "</option>";
                $(type).append(option);
          })
        },
        error:function(){
          alert("服务器繁忙, 请联系管理员!");
        }
  })
}


/**
 * [arrayIntersection 两个数组取交集]
 * @param  {[type]} a [description]
 * @param  {[type]} b [description]
 * @return {[type]}   [description]
 */
function arrayIntersection ( a, b )
{
    var ai=0, bi=0;
    var result = new Array();
    while ( ai < a.length && bi < b.length )
    {
        if      ( a[ai] < b[bi] ) { ai++; }
        else if ( a[ai] > b[bi] ) { bi++; }
        else /* they're equal */
        {
            result.push ( a[ai] );
            ai++;
            bi++;
        }
    }
    return result;
}

function onblurNum(i){
  if (i>10 || i<1) {
    return i.substring(0,1);
  }
  return i;
}

function timeText(i){
  console.log(i);
}

          //刷新页面
function repla()
{
    parent.location.reload();
}

///////////////////////////////////////////
///
///
///

function checkPassword(str){
    var reg1 = /[!@#$%^&*()_?<>{}]{1}/;
    var reg2 = /([a-zA-Z0-9!@#$%^&*()_?<>{}]){6,16}/;
    var reg3 = /[a-zA-Z]+/;
    var reg4 = /[0-9]+/;
    if(reg1.test(str) && reg2.test(str) && reg3.test(str) && reg4.test(str)){
        return '1';
    }else if(!reg1.test(str)){
        return "需包含一个特殊字符";
    }else if(!reg2.test(str)){
        return "长度在6-16位";
    }else if(!reg3.test(str)){
        return "需含有字母";
    }else if(!reg4.test(str)){
        return "需含有数字";
    }
}


function userButton(){
    var n = $('.userAddIndexname').val();
    var p = $('.userAddIndexpw').val();
    var i = $('.userAddIndexiphone').val();
    var e = $('.userAddIndexemail').val();
    var q = $('.userAddIndexqq').val();
    var s = 0;
    if (n == null || n == '' || p == null || p == '') {
      layer.msg('请填写账号或者密码 :( ');
      return false;
    }
    if (p != '' && p != null) {
      var info = checkPassword(p);
      if (info != '1') {
        layer.msg(info);
        return false;
      }
    }


    if (i != '' && i != null) {
      var ipreg = /^1[3|4|5|8][0-9]\d{4,8}$/;
      if (!ipreg.test(i)) {
        layer.msg("手机不符合规则，请重新输入");
        $('.userAddIndexiphone').val('');
        return false;
      }
    }else{
      s++;
    }

    if (e != '' && e != null) {
      var emreg = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/;
      if (!emreg.test(e)) {
        layer.msg("邮箱不符合规则，请重新输入");
        $('.userAddIndexemail').val('');
        return false;
      }
    }else{
      s++;
    }
    if (s >1) {
      layer.msg("邮箱或者手机号必须填写一个 :)");
      return false;
    }
    var data = {
            'n':n,
            'p':p,
            'i':i,
            'e':e,
            'q':q
          };
    $.ajax({
        type : 'POST',
        url : '/admin/User/ajaxUserAUD',
        data : {'data':JSON.stringify(data),'info':'insert'},
        dataType : 'json',
        success:function(data){
          if (data['status'] == 0) {
                   layer.msg(data['info'], {icon: 5},function () {});
                }else{
                   layer.msg(data['info'], {icon: 6},function () {
                    // 获得frame索引
                      var index = parent.layer.getFrameIndex(window.name);
                      //关闭当前frame
                      parent.layer.close(index);
                      setInterval(repla(),2000);});
                  }
        },
        error:function(data){
          layer.msg('网络出错');
        },
    })
    
  }


function delAll(obj) {
    var data = tableCheck.getData();
    if ($(obj).attr('lay-event') == "getznx") {
      if (data.length == 0) {
          layer.msg("请选中会员");
      }else{
        x_admin_show('用户管理',"/admin/User/UserAdd?name="+$(obj).attr('lay-event')+"&id="+data,720,500);
      }
    }else{
       x_admin_show('用户管理',"/admin/User/UserAdd?name="+$(obj).attr('lay-event')+"&id="+data,720,500);
    }
  }

function ajaxUserAdd(id){
  if (id == "add") {
    var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 500 ? 500 : document.documentElement.clientHeight-10;
   x_admin_show('添加用户',"/admin/User/UserAdd?name=add&id="+id,w,h);
  }
    else{
    var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 710 ? 710 : document.documentElement.clientHeight-10;
      x_admin_show('用户详情',"/admin/User/UserAdd?name=update&id="+id,w,h);
    }
  }

function ajaxUserCapital(obj,id){
  var a = $(obj).attr('lay-event') != "address"?($(obj).attr('lay-event') == "zjtj"?"资金调节":"用户资金"):"会员收货地址列表";
  var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 710 ? 710 : document.documentElement.clientHeight-10;
      x_admin_show(a,"/admin/User/UserAdd?name="+$(obj).attr('lay-event')+"&id="+id,w,h);
}

function TestPhone(i){
  var ipreg = /^1[3|4|5|8][0-9]\d{4,8}$/;
  var index = '';
      if (!ipreg.test(i)) {
        index = "手机不符合规则，请重新输入";
      }else{
        index = "true"
      }
      return index;
}

function TestEmail(e){
  var emreg = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/;
  var index = '';
   if (!emreg.test(e)) {
         index = "邮箱不符合规则，请重新输入";
      }else{
        index = "true"
      }
      return index;
}

function ajaxlevelListAdd(obj,id){
  if ($(obj).attr('lay-event') == "delete") {
    layer.confirm('您确定要删除这条数据吗？', {
      btn: ['确定','取消'] //按钮
      }, function(){
        $.ajax({
          type: "post",
          url: "/admin/user/AjaxLeverLiDe",
          data: {"id":id},
          success:function(data){
            var data = jQuery.parseJSON(data);
            console.log(data['status']);
            if (data['status'] == 0) {
              layer.msg(data['info'],function(){
                layer.closeAll('dialog'); 
            });
              return false;
            }
            layer.msg(data['info'],function(){
            $(obj).parent('td').parent('tr').remove();
              layer.closeAll('dialog'); 
            });
         },error:function(){
            layer.msg("网络出错！！！");
         }
       });
  });

    return false;
  }
   var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 500 ? 500 : document.documentElement.clientHeight-10;
   x_admin_show('用户等级',"/admin/User/levelList_edit?name="+$(obj).attr('lay-event')+"&id="+id,w,h);
  
}

function ajaxWithdList(obj,id,is_tg){
  /**
   * [a 弹出详情的框框]
   * @type {[type]}
   */
  if (is_tg != 1) {
      layer.msg('请审批通过后才可以查看详情');
      return false;
  }
  var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 710 ? 710 : document.documentElement.clientHeight-10;
      x_admin_show("提现详情","/admin/User/WithdList?id="+id,w,h);
}

function ajaxAdver(obj,id='0'){
    var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 710 ? 710 : document.documentElement.clientHeight-10;
    x_admin_show("添加广告位","/admin/seo/adverAddEdit?id="+id,w,h);

  }

function ajaxkf(obj,id='0',title='',url=''){
    var w = document.documentElement.clientWidth-50 > 715 ? 520 : document.documentElement.clientWidth-230;
    var h = document.documentElement.clientHeight - 10 > 710 ? 550 : document.documentElement.clientHeight-200;
    x_admin_show(title,url+"?id="+id,w,h);
}  
// 弹出窗口i 公共
function ajaxtc(obj,id='0',title='',url=''){
    var w = document.documentElement.clientWidth-50 > 715 ? 715 : document.documentElement.clientWidth-50;
    var h = document.documentElement.clientHeight - 10 > 710 ? 710 : document.documentElement.clientHeight-10;
    x_admin_show(title,url+"?id="+id,w,h);
}
// 删除数据公共
function ajaxListDelete(obj,id,url = null){
  if(url == null){
    layui.msg("参数错误！");
    return false;
  }
  if ($(obj).attr('lay-event') == "delete") {
    layer.confirm('您确定要删除这条数据吗？', {
      btn: ['确定','取消'] //按钮
      }, function(){
        $.ajax({
          type: "post",
          url: url,
          data: {"id":id},
          success:function(data){
            var data = jQuery.parseJSON(data);
            if (data['status'] == 0) {
              layer.msg(data['info'],function(){
                layer.closeAll('dialog'); 
            });
              return false;
            }
            layer.msg(data['info'],function(){
            $(obj).parent('td').parent('tr').remove();
              layer.closeAll('dialog'); 
            });
         },error:function(){
            layer.msg("网络出错！！！");
         }
       });
  });

    return false;
  }
}