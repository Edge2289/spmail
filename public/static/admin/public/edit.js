$(document).ready(function(){




	//热门
    $('.hot').each(function(){
    	$(this).click(function(){
    		//id : $(this).attr('value')
    	$.ajax({
    			type : 'POST',
    			url : 'updat_column_hotstate',
    			data : {'id':$(this).attr('value'),'cate':'hot'},
    			dataType : 'json',
    			success:function(data){
    				var data = jQuery.parseJSON(data);
    				if(data['static'] == 0){
                       layer.msg(data['message'],function(){});
                    }
    			},
    		})
    	//
    	})
    })
function repla()
      {
        javascript:location.replace(location.href);
      }
    //状态
    $('.static').each(function(){
    	$(this).click(function(){
    	$.ajax({
    			type : 'POST',
    			url : 'updat_column_hotstate',
    			data : {'id':$(this).attr('value'),'cate':'state'},
    			dataType : 'json',
    			success:function(data){
    				var data = jQuery.parseJSON(data);
    				if(data['static'] == 0){
                       layer.msg(data['message'],function(){
                       	setInterval(repla(),2000);
                       });
                    }
    			},
    		})
    	})
    })
//跟新栏目
    $('input[name="text"]').each(function(){
    	$(this).blur(function(){
    		var val = $(this).val();
    		var id = $(this).attr('cate_id');
    		$.ajax({
    			type : 'POST',
    			url : 'updat_column_name',
    			data : {'id':id,'val':val},
    			dataType : 'json',
    			success:function(data){
    				var data = jQuery.parseJSON(data);
    				if(data['static'] == 0){
                       layer.msg(data['message'],function(){
                       	setInterval(repla(),2000);
                       });
                    }
    			},
    		})
    	})
    })
/** ------seacer----- */
/**
 * seacer  搜索
 */
    $('#seacer').click(function(){
        //$('.speclist').val()
        // window.location.href = "speclist?seacer="+$('.speclist').val();
       window.location.href = "speclist?page="+$('.page_hidden').val()+"&page_list="+$('.page_list_hidden').val()+"&seacer="+$('.speclist').val();
    })

    
    /**
     *  刷新名称
     */
 var reg = /^([\u0391-\uFFE5\d\w])*([\u0391-\uFFE5\d\w]+)$/; 
    $('input[name="goods_update_name"]').each(function(){
        $(this).blur(function(){
            var fied = $(this).val();
            var index = $(this).attr('index');
            if (fied != '' || reg.exec(fied)) {
                $.ajax({
                    type : 'get',
                    url : 'goodstypelist_update',
                    data: {'fied':fied,'index':index},
                    dataType : 'json',
                    success:function(data){
                        var data = jQuery.parseJSON(data);
                        if (data['static'] == 0) {
                layer.msg(data['message'],function(){
                        setInterval(repla());
                       });
                        }
                    }
                })
            }else{
                layer.msg("更新失败，请检查一下输入的模型名称",function(){
                        setInterval(repla());
                       });
            }
        })
    })


/*
 * 检索search_index
 * 是 <span class="search_index_value" style="color: #009688;font-weight: bold;" value="1"><i class="layui-icon">&#x1005;</i>&nbsp;是</span>
 * 否 <span class="search_index_value" style="color: #999999;" value="0"><i class="layui-icon">&#x1007;</i>&nbsp;否</span>
 */

    $('.search_index').each(function(){
        $(this).click(function(){
            var yes = '<span class="search_index_value" style="color: #009688;font-weight: bold;" value="1"><i class="layui-icon">&#x1005;</i>&nbsp;是</span>';
            var no = '<span class="search_index_value" style="color: #999999;" value="0"><i class="layui-icon">&#x1007;</i>&nbsp;否</span>';
            var speclist_id = $(this).children('.search_index_value').attr('index');
            var search_index = $(this).children('.search_index_value').attr('value');
            /** 改变显示的状态 */
                        if (search_index == 0) {
                            $(this).html(yes);
                        }else{
                            $(this).html(no);
                        }
                /** 异步更改数据 */
            $.ajax({
                type : 'POST',
                url : 'search_index',
                data : {'speclist_id':speclist_id,'search_index':search_index},
                dataType : 'json',
                success:function(){}
            })
        })
    })
/*
 * 检索attribute_index
 * 是 <span class="search_index_value" style="color: #009688;font-weight: bold;" value="1"><i class="layui-icon">&#x1005;</i>&nbsp;是</span>
 * 否 <span class="search_index_value" style="color: #999999;" value="0"><i class="layui-icon">&#x1007;</i>&nbsp;否</span>
 */

    $('.attribute_index').each(function(){
        $(this).click(function(){
            var yes = '<span class="attribute_index_value" style="color: #009688;font-weight: bold;" value="1"><i class="layui-icon">&#x1005;</i>&nbsp;是</span>';
            var no = '<span class="attribute_index_value" style="color: #999999;" value="0"><i class="layui-icon">&#x1007;</i>&nbsp;否</span>';
            var attribute_id = $(this).children('.attribute_index_value').attr('index');
            var attribute_index = $(this).children('.attribute_index_value').attr('value');
            /** 改变显示的状态 */
                        if (attribute_index == 0) {
                            $(this).html(yes);
                        }else{
                            $(this).html(no);
                        }
                /** 异步更改数据 */
            $.ajax({
                type : 'POST',
                url : 'attribute_index',
                data : {'attribute_id':attribute_id,'attribute_index':attribute_index},
                dataType : 'json',
                success:function(){}
            })
        })
    })


    /**
     * 编辑规格的文本域输出
     */
     var aa = $('.textarea_hidden').val()||'';
    var text = aa.split("_");//$('.textarea_hidden').val()
    var sum = '';
    for (var i = 0; i < text.length; i++) {
       sum = sum+text[i]+'\n';
    }
    $('#textarea_class').val(sum.substr(0,sum.length-1));

    

    /** ------seacer----- */
/**
 * seacer  搜索
 */
    $('#attri_seacer').click(function(){
        //$('.speclist').val()
        // window.location.href = "speclist?seacer="+$('.speclist').val();
       window.location.href = "attribute?page="+$('.page_hidden').val()+"&page_list="+$('.page_list_hidden').val()+"&seacer="+$('.speclist').val();
    })

    /**
     * [description]
     * @param  {[type]}                                                          
     * @param  {[type]}                                                                     console.log(data);                  }              })                          }else{               layer.msg("请修改模型名称",{icon:2,time:200000});              return false;            }           }else{            layer.msg("请输入模型",{icon:2,time:2000} [description]
     * @return {[type]}  返回状态 和 内容  
     */
    $('#submita').click(function(){
        var refid = $('#goods_type_name').val(); 
        var reg =/^([\u0391-\uFFE5\d\w])*([\u0391-\uFFE5\d\w]+)$/; 
            if(refid != "")
          { 
            if(reg.exec(refid))
            { 
              //成功 异步传输
              $.ajax({
                  type : 'POST',
                  url : 'goodstypelist_insert',
                  data : {'data':refid},
                  dataType : 'json',
                  success:function(data){
                   var data = jQuery.parseJSON(data);
                   if(data['static'] == 0)
                   {
                        layer.msg(data['message'],{icon:2,time:2000});
                   }else{
                    layer.msg(data['message'],{icon:1,time:2000},function(){
                setInterval(repla(),2000);
            });
                   }
                  }
              })
              //异步传输
            }else{ 
              layer.msg("请修改模型名称",{icon:2,time:2000});
              return false;
            } 
          }else{
            layer.msg("请输入模型",{icon:2,time:2000});
            return false;
          }
        })


    //更新品牌  推荐 brank_tj
    $('.brank_tj').each(function(){
      $(this).click(function(){

         //  例子
                var yes = '<span class="search_index_value brank_tj" style="color: #009688;font-weight: bold;" index="'+$(this).attr('index')+'" value="0"><i class="layui-icon">&#x1005;</i>&nbsp;是</span>';
                var no = '<span class="search_index_value brank_tj" style="color: #999999;" index="'+$(this).attr('index')+'" value="1"><i class="layui-icon">&#x1007;</i>&nbsp;否</span>';
                
                  if ($(this).attr('value') == 1) {
                    $(this).parent('.space').html(yes);
                  }else{
                    $(this).parent('.space').html(no);
                  }
         // 异步传输  index : id    value : 状态 1 否   0  是
            $.ajax({
              type : 'get',
              url : 'brandlist_update',
              data : {index:$(this).attr('index'),type: "tj",data: $(this).attr('value')},
              dataType : 'json',
              success:function(data)   //  成功
              {
                var data = jQuery.parseJSON(data);
                  if (data['static'] == 1) {
                    layer.msg(data['message'],{icon:1,time:2000});
                  }else{
                      layer.msg(data['message'],{icon:2,time:2000});
                  }
              },
              error:function(data)     //错误
              {
                var data = jQuery.parseJSON('data');
                  layer.msg(data['message'],{icon:1,time:2000});
              }
            })
          // 异步传输
      })
    })
$("img").error(function() { 
$(this).attr("src", "/static/image/goods/brand/error.png"); 
$(this).parent('.a_mmnn').attr("href", "/static/image/goods/brand/error.png"); 
}); 

    //更新品牌  排序 brank_order
    $('.brank_order').each(function(){
      $(this).blur(function(){
        if(isNaN($(this).val())){
                     layer.msg('输入无效',function(){});
                  return false;
                } 
          // 异步传输
            $.ajax({
              type : 'get',
              url : 'brandlist_update',
              data : {index:$(this).attr('index'),type: "order",data: $(this).val()},
              dataType : 'json',
              success:function(data)   //  成功
              {
                  var data = jQuery.parseJSON(data);
                  if (data['static'] == 1) {

                    layer.msg(data['message'],{icon:1,time:2000});
                  }else{
                      layer.msg(data['message'],{icon:2,time:2000});
                  }
              },
              error:function()     //错误
              {
                  var data = jQuery.parseJSON('data');
                  layer.msg(data['message'],{icon:1,time:2000});
              }
            })
          // 异步传输
        })
    })
})






