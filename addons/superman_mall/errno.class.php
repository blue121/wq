<?php

//decode by  http://www.tule5.com/
defined('IN_IA') or die('Access Denied');
class ERRNO
{
	const OK = 0;
	const SYSTEM_ERROR = 1;
	const PARAM_ERROR = 2;
	const INVALID_REQUEST = 3;
	const NOT_LOGIN = 4;
	const NOT_IN_WECHAT = 5;
	const NICKANME_NULL = 100;
	const NICKANME_INVALID = 101;
	const MOBILE_NULL = 102;
	const MOBILE_INVALID = 103;
	const MOBILE_EXISTS = 104;
	const EMAIL_NULL = 105;
	const EMAIL_INVALID = 106;
	const EMAIL_EXISTS = 107;
	const MSG_INVALID = 108;
	const USERNAME_NULL = 109;
	const CITY_NULL = 110;
	const CITY_INVALID = 111;
	const USERNAME_INVALID = 112;
	const USERNAME_REPEAT = 113;
	const PASSWORD_INVALID = 114;
	const CHECKCODE_NULL = 115;
	const CHECKCODE_ERROR = 116;
	const ITEM_NOT_FOUND = 1000;
	const ITEM_NOT_TOTAL = 1001;
	const ITEM_BUY_LIMIT = 1002;
	const ITEM_OFFLINE = 1003;
	const ITEM_NOT_INTIME = 1004;
	const ORDER_NOT_EXIST = 2000;
	const ORDER_NOT_NEED_PAY = 2001;
	const ORDER_NOT_FOUND_PAYTYPE = 2002;
	const ORDER_PAYED = 2003;
	const ORDER_HAD_CHECKOUT = 2004;
	const ORDER_CANNOT_CHECKOUT = 2005;
	const COMMENT_NOT_EXIST = 3000;
	const CART_NO_ITEM = 4000;
	const SKU_NOT_EXIST = 5000;
	const SKU_NOT_TOTAL = 5001;
	const ADDRESS_NOT_EXIST = 6000;
	const CREDIT_BALANCE_NOT_ENOUGH = 7000;
	const MYFETCH_CITY_ERROR = 8000;
	const MYFETCH_NO_EXIST = 8001;
	const CODE_NOT_EXIST = 9000;
	const SHOP_TITLE_NULL = 10000;
	const SHOP_ADDRESS_NULL = 10001;
	const SHOP_PHONE_NULL = 10002;
	const SHOP_BUSINESS_SCOPE_NULL = 10003;
	const SHOP_DESCRIPTION_NULL = 10004;
	const SHOP_NOT_FOUND = 10005;
	const SHOP_AUDITING = 10006;
	const SHOP_DISABLED = 10007;
	const SMS_SWITCH_CLOSE = 11000;
	const SMS_ACCOUNT_NULL = 11001;
	const SMS_SENDTIME_QUICK = 11002;
	public static $ERRMSG = array(self::OK => 'ok', self::SYSTEM_ERROR => '系统错误', self::PARAM_ERROR => '参数错误', self::INVALID_REQUEST => '非法请求', self::NOT_LOGIN => '未登录，跳转中...', self::NOT_IN_WECHAT => '请使用微信访问', self::NICKANME_NULL => '请输入昵称', self::NICKANME_INVALID => '请输入合法昵称', self::MOBILE_NULL => '请输入手机号', self::MOBILE_INVALID => '请输入合法手机号', self::MOBILE_EXISTS => '手机号已存在，请更换', self::EMAIL_NULL => '请输入邮箱', self::EMAIL_INVALID => '请输入合法邮箱', self::EMAIL_EXISTS => '邮箱已存在，请更换', self::MSG_INVALID => '字数不符合条件', self::USERNAME_NULL => '请输入用户名', self::USERNAME_INVALID => '用户名不合法', self::CITY_NULL => '请选择城市', self::CITY_INVALID => '城市信息非法', self::USERNAME_REPEAT => '用户名已存在', self::PASSWORD_INVALID => '密码不合法', self::CHECKCODE_NULL => '请输入验证码', self::CHECKCODE_ERROR => '验证码错误', self::ITEM_NOT_FOUND => '商品不存在或已删除', self::ITEM_NOT_TOTAL => '商品库存不足', self::ITEM_BUY_LIMIT => '超过兑换限制', self::ITEM_OFFLINE => '商品已下架', self::ITEM_NOT_INTIME => '商品不在可秒杀时间段内', self::ORDER_NOT_EXIST => '订单不存在或已删除', self::ORDER_NOT_NEED_PAY => '该订单不需要支付', self::ORDER_NOT_FOUND_PAYTYPE => '支付方式错误', self::ORDER_PAYED => '订单已支付', self::ORDER_HAD_CHECKOUT => '订单已核销', self::ORDER_CANNOT_CHECKOUT => '订单无法核销', self::COMMENT_NOT_EXIST => '评论不存在', self::CART_NO_ITEM => '提交订单的商品不存在', self::SKU_NOT_EXIST => '商品规格不存在', self::SKU_NOT_TOTAL => '商品库存不足', self::ADDRESS_NOT_EXIST => '收获地址不存在', self::CREDIT_BALANCE_NOT_ENOUGH => '余额不足', self::MYFETCH_CITY_ERROR => '自取地址出错', self::MYFETCH_NO_EXIST => '该地区没有自提点', self::CODE_NOT_EXIST => '验证码有误，请重新输入', self::SHOP_TITLE_NULL => '请输入商户名称', self::SHOP_ADDRESS_NULL => '请输入商户地址', self::SHOP_PHONE_NULL => '请输入联系电话', self::SHOP_BUSINESS_SCOPE_NULL => '请输入经营品类', self::SHOP_DESCRIPTION_NULL => '请输入商户简介', self::SHOP_NOT_FOUND => '商户不存在或已删除', self::SHOP_AUDITING => '商户审核中，请稍后访问', self::SHOP_DISABLED => '商户已下架', self::SMS_SWITCH_CLOSE => '未开启短信验证功能', self::SMS_ACCOUNT_NULL => '未配置短信账号', self::SMS_SENDTIME_QUICK => '验证码申请60秒后可以再次申请');
}