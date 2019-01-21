﻿<?php
// 面包屑导航配置
    return array(        
              'admin/goods'=>array(
                'name' =>'商品管理',
                'action'=>array(
                     'categoryList'=>'商品分类',
                     'goodsList'=>'商品列表',
                     'goodsTypeList'=>'商品模型',
                     'speclist'=>'商品规格',
                     'attribute'=>'商品属性',
                     'brandList'=>'商品品牌',                 
         	       )
               ),                
              'admin/order'=>array(
                'name' =>'订单管理',
                'action'=>array(
                     'index'=>'订单列表',
                     'edit_order'=>'批评管理',
         	      )
               ),        
              'admin/user'=>array(
                'name' =>'会员管理',
                'action'=>array(
                     'index'=>'会员列表',
                     'levelList'=>'等级列表',    
                     'address'=>'收货地址',
                     'recharge'=>'充值记录',   
                     'withdrawals'=>'提现申请',     
                     //'remittance'=>'汇款记录',       
                     'signList'=>'会员签到',          
         	      )
               ),        
              'admin/seo'=>array(
                'name' =>'运营管理',
                'action'=>array(
                     'adver'=>'广告版位',
                     'adver_infro'=>'广告管理', 
                     'coupon'=>'优惠券列表', 
                     'bannerimg'=>'首页海报',   
                     'kefu'=>'在线客服',                 
         	      )
               ),        
              'admin/article'=>array(
                'name' =>'文章管理',
                'action'=>array(
                     'articlelist'=>'文章列表',
                     'categorylist'=>'文章分类',  
                     'linkList'=>'友情链接列表',                                   
         	      )
               ),        
              'admin/manager'=>array(
                'name' =>'管理员管理',
                'action'=>array(
                     'index'=>'管理员列表',                   
                     'log'=>'管理员日志',
                     'role'=>'角色管理',  
                     'contorller'=>'控制器管理',                 
         	      )
               ),        
              'admin/comment'=>array(
                'name' =>'评论管理',
                'action'=>array(
                     'index'=>'评论列表',
                     'detail'=>'评论回复',
         	      )
               ),        
              // 'admin/template'=>array(
              //   'name' =>'模板管理',
              //   'action'=>array(
              //        'templatelist'=>'模板选择',                     
         	    //   )
              //  ),       
             	'admin/configTemplate'=>array(
            	   'name'=>'配置管理',
            	   'action'=>array(
                'payconfig'=>'支付配置',
                'expressconfig'=>'快递配置',
            		'supplierConfig'=>'供应商管理',
            		'poatconfig'=>'运费配置',
            		'emeimpor'=>'短信配置',
            		'emeconfig'=>'短信模板配置',
            		'emailconfig'=>'邮箱配置',
                  )
            	),     
              // 'admin/wechat'=>array(
              //   'name' =>'微信管理',
              //   'action'=>array(
              //        'index'=>'公众号管理',
              //        'setting'=>'微信配置',
              //        'menu'=>'微信菜单',
              //        'text'=>'文本回复',
              //        'add_text'=>'编辑文本回复',
              //        'img'=>'图文回复',
              //        'add_img'=>'编辑图文回复',                   
              //        'news'=>'组合图文回复',   
              //        'add_news'=>'编辑图文',
         	    //   )
              //  ),                
              // 'admin/plugin'=>array(
              //   'name' =>'插件管理',
              //   'action'=>array(
              //        'index'=>'插件列表',
              //        'setting'=>'插件配置',
         	    //   )
              //  ),
              //  'admin/topic'=>array(
              //  		'name' =>'专题管理',
              //  		'action'=>array(
              //  			'topicList'=>'专题列表',
              //  			'topic'=>'添加专题',
              //  		)
              //  ),
              //  'admin/promotion'=>array(
              //  		'name' =>'团购管理',
              //  		'action'=>array(
              //  			'group_buy_list'=>'团购列表',
              //  			'group_buy'=>'编辑团购',
              //  		)
              //  ),
               'admin/tools'=>array(
               		'name' =>'工具管理',
               		'action'=>array(
               			'index'=>'数据备份',
               			'restore'=>'数据还原',
               		)
               ),
               'admin/report'=>array(
               		'name' =>'报表统计',
               		'action'=>array(
               			'index'=>'销售概况',
               			'saleTop'=>'销售排行',
               			'userTop'=>'会员排行',
               			'saleList'=>'销售明细',
               			'user'=>'会员统计',
               			'finance'=>'财务统计',
               		)
               ),
               // 'admin/distribut'=>array(
               // 		'name' =>'分销管理',
               // 		'action'=>array(
               // 		       'tree'=>'分销关系',
               // 			'set'=>'分销设置',
               //          'withdrawals'=>'提现申请记录',
               //           'remittance'=>'汇款记录',
               //           'rebate_log'=>'分成记录',
               // 		)
               // ),  
              'admin/system'=>array(
                'name' =>'系统设置',
                'action'=>array(
                     'index'=>'网站设置',    
					           'welcome'=>'404页面',        					 
                     'navigationList'=>'导航设置',
                     'menu'=>'菜单管理',
                     'module'=>'模块管理',
         	       )
               ),			   

    );
?>
