<?php
/**
 * 【超人】超级商城模块
 *
 * @author 超人
 * @url http://bbs.we7.cc/thread-13060-1-1.html
 */
class SupermanPrinter {
    public static $providers = array(
        'feie' => array(
            'title' => '飞鹅打印机',
            'url' => 'http://www.feieyun.com/',
        ),
        '365' => array(
            'title' => '365打印机',
            'url' => 'http://www.printcenter.cn/',
        ),
    );
    public $debug = false;
    public $printer = array();
    public function __construct() {}
    public function order_print($order) {
        return false;
    }
    public function check_print($searchid) {
        return false;
    }
    public function check_status() {
        return false;
    }
}

/*
 * Usage: Printer::init(array printer)->order_print(array order);
 */
if (!class_exists('Printer')) {
    class Printer {
        static private $_instances;
        public static function init($printer) {
            $provider = $printer['provider'];
            if (!isset(Printer::$_instances[$provider])) {
                if (empty($printer['sn']) || empty($printer['key']) || !array_key_exists($provider, SupermanPrinter::$providers)) {
                    WeUtility::logging('fatal', '未配置打印机');
                    Printer::$_instances[$provider] = new SupermanPrinter($printer); //容错处理
                    Printer::$_instances[$provider]->printer = $printer;
                } else {
                    $classname = "SupermanPrinter_{$provider}";
                    $filename = MODULE_ROOT.'/class/printer/'.$provider.'.class.php';
                    if (file_exists($filename)) {
                        require $filename;
                        Printer::$_instances[$provider] = new $classname();
                        Printer::$_instances[$provider]->printer = $printer;
                    } else {
                        trigger_error('打印机接口 "'.'class/printer/'.$provider.'.class.php" 不存在', E_USER_ERROR);
                    }
                }
            }
            return Printer::$_instances[$provider];
        }
    }
} else {
    exit('class Printer conflict');
}