
 $(document).ready(function(){

     $(function(){
         $(".add").click(function(){
          var t=$(this).parent().find('input[class*=text_box]');
         t.val(parseInt(t.val())+1)
         $('.J_ItemSum').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 商品详情
         $('.pay-sum').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 商品详情
         $('#J_ActualFee').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 总价格
        })
      $(".min").click(function(){
       var t=$(this).parent().find('input[class*=text_box]');
         t.val(parseInt(t.val())-1)
         if(parseInt(t.val())<1){
          t.val(1);
          }
         $('.J_ItemSum').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 商品详情
         $('.pay-sum').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 商品详情
         $('#J_ActualFee').html(parseInt($('.J_ItemSum').attr('data-price'))*parseInt(t.val())); // 总价格
       })
    }) 

	
	    if (!document.getElementsByClassName) {
        document.getElementsByClassName = function (cls) {
            var ret = [];
            var els = document.getElementsByTagName('*');
            for (var i = 0, len = els.length; i < len; i++) {

                if (els[i].className.indexOf(cls + ' ') >=0 || els[i].className.indexOf(' ' + cls + ' ') >=0 || els[i].className.indexOf(' ' + cls) >=0) {
                    ret.push(els[i]);
                }
            }
            return ret;
        }
    }
 
 
//地址选择
				$(function() {
					/**
					 * [buy-user   名字]
					 * [buy-phone   电话]
					 * [province   名字]
					 * [city   名字]
					 * [dist   名字]
					 * [street   名字]
					 * @param  {[type]} ) {					
					 * @return {[type]}   [description]
					 */
					$(".user-addresslist").click(function() {
						$(this).addClass("defaultAddr").siblings().removeClass("defaultAddr");
						// 修改地址信息
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.province').html($(this).children('.address-left').children('.default-address').children('.buy-address-detail').children('.province').html());
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.city').html($(this).children('.address-left').children('.default-address').children('.buy-address-detail').children('.city').html());
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.dist').html($(this).children('.address-left').children('.default-address').children('.buy-address-detail').children('.dist').html());
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.street').html($(this).children('.address-left').children('.default-address').children('.buy-address-detail').children('.street').html());
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.buy-user').html($(this).children('.address-left').children('.user').children('.buy-address-detail').children('.buy-user').html());
						$('#holyshit268').children('.buy-footer-address').children('.buy-address-detail').children('.buy-phone').html($(this).children('.address-left').children('.user').children('.buy-address-detail').children('.buy-phone').html());
					});
					$(".logistics").each(function() {
						var i = $(this);
						var p = i.find("ul>li");
						p.click(function() {
							if (!!$(this).hasClass("selected")) {
								$(this).removeClass("selected");
							} else {
								$(this).addClass("selected").siblings("li").removeClass("selected");
							}
						})
					})
				});
 			})
 
// 弹出地址选择
 
			$(document).ready(function($) {
	
				var $ww = $(window).width();
	
				$('.theme-login').click(function() {
//					禁止遮罩层下面的内容滚动
					$(document.body).css("overflow","hidden");
				
					$(this).addClass("selected");
					$(this).parent().addClass("selected");

									
					$('.theme-popover-mask').show();
					$('.theme-popover-mask').height($(window).height());
					$('.theme-popover').slideDown(200);																		
					
				})
				$('.theme-popover-mask').click(function() {

					$(document.body).css("overflow","visible");
					$('.theme-login').removeClass("selected");
					$('.item-props-can').removeClass("selected");					
					$('.theme-popover-mask').hide();
					$('.theme-popover').hide();
				})
				$('.theme-poptit .close,.btn-op .close').click(function() {

					$(document.body).css("overflow","visible");
					$('.theme-login').removeClass("selected");
					$('.item-props-can').removeClass("selected");					
					$('.theme-popover-mask').hide();
					$('.theme-popover').slideUp(200);
				})

				
			}); 
 
 
 
 
 
 
 
 
 
 
 
 

