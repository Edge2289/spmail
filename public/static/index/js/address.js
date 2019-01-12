
 $(document).ready(function(){
 ///////////////////////////////////////////////
	$("#J_Go").click(function(){
		var wuliu = '';
		var pay = '';
		var address = '';
		var goodsNum = $('input[name="goodsNum"]').val();
		var goodsid  = $('input[name="goodsid"]').val();
		var liuyan  = $('input[name="liuyan"]').val();


		$fujian = {
			'wuliu' : "请选择物流方式",
			'pay' : "请选择支付方式",
			'address' : "请选择收货地址",
			'goodsNum' : "商品数量有误",
			'goodsid' : "商品错误，请重新购买",
		}

		$('.op_express_delivery_hot li').each(function(){
			if ($(this).hasClass("selected")) {
				wuliu = $(this).data("value");
			}
		})
		$('.pay-list li').each(function(){
			if ($(this).hasClass("selected")) {
				pay = $(this).data("value");
			}
		})
		$('.address-select li').each(function(){
			if ($(this).hasClass("defaultAddr")) {
				address = $(this).data("addr_id");
			}
		})

		/**
		 * [if 判断]
		 * @param  {[type]} wuliu [description]
		 * @return {[type]}       [description]
		 */
		if (wuliu == '') {
			alert($fujian['wuliu']);
			return false;
		}
		if (pay == '') {
			alert($fujian['pay']);
			return false;
		}
		if (address == '') {
			alert($fujian['address']);
			return false;
		}
		if (goodsid == '') {
			alert($fujian['goodsid']);
			return false;
		}
		if (goodsNum == '') {
			alert($fujian['goodsNum']);
			return false;
		}

		$('input[name="order_goodsNum"]').val(goodsNum);
		$('input[name="order_goodsid"]').val(goodsid);
		$('input[name="order_liuyan"]').val(liuyan);
		$('input[name="order_wuliu"]').val(wuliu);
		$('input[name="order_pay"]').val(pay);
		$('input[name="order_address"]').val(address);

		

	})


 //////////////////////////////////////////////
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
					$('.item-content').each(function(){
						var objmin = $(this).children('.td-amount').children('.amount-wrapper').children('.item-amount').children('.sl').children('#min');
						var number = $(this).children('.td-sum').children('.td-inner').children('.J_ItemSum');
						var Price = $(this).children('.pay-phone').children('.td-price').children('.item-price').children('.price-content').children('.J_Price');
						var objadd = $(this).children('.td-amount').children('.amount-wrapper').children('.item-amount').children('.sl').children('#add');
						$(objmin).click(function(){
							$(objmin).next().val(parseInt($(objmin).next().val())-1);
							$(number).html(parseInt($(objmin).next().val())*parseInt($(Price).html()));
							countItem();
						})
						$(objadd).click(function(){
							$(objadd).prev().prev().val(parseInt($(objadd).prev().prev().val())+1);
							$(number).html(parseInt($(objmin).next().val())*parseInt($(Price).html()));
							countItem();
						})
		      		})
		      		/******************    总价格显示  **************************/
		      function countItem(){
		      		var count = 0;
		      		var IntCount = 0;
		      		$('.item-content').each(function(){
		      			var number = $(this).children('.td-sum').children('.td-inner').children('.J_ItemSum');
						count += parseInt($(number).html());
		      		})
		      		$('#J_ActualFee').html(count);
		      		$('.pay-sum').html(count);
		      }
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
 
 
 
 
 
 
 
 
 
 
 
 

