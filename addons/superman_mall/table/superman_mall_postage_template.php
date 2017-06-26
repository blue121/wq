<?php
/**
 * 【超人】超级商城模块微站定义
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
defined('IN_IA') or exit('Access Denied');
class table_superman_mall_postage_template extends SupermanMallTable {
	public function __construct() {
		$this->_table = 'superman_mall_postage_template';
	}
    public function get_thead_by_valuation($val, $thead_column) {
        switch ($thead_column) {
            case 1:
                switch ($val) {
                    case 1:
                        return '首件(个)';
                        break;
                    case 2:
                        return '首重(kg)';
                        break;
                    case 3:
                        return '首体积(m³)';
                        break;
                }
                break;
            case 2:
            case 4:
                return '邮费(元)';
                break;
            case 3:
                switch ($val) {
                    case 1:
                        return '续件(个)';
                        break;
                    case 2:
                        return '续重(kg)';
                        break;
                    case 3:
                        return '续体积(m³)';
                        break;
                }
                break;
            default:
                return '未知';
                break;
        }
    }
}