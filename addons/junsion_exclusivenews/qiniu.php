<?php
//图乐源码：www.tule5.com
require_once 'qiniu/io.php';
require_once 'qiniu/rs.php';
class Qiniu{
    public function save($url, $config, $ext = '', $mask = ''){
        set_time_limit(0);
        if (empty($url)){
            return '';
        }
        if (empty($ext)) $ext = strrchr($url, ".");
        $filename = random(30) . $ext;
        $contents = @file_get_contents($url);
        $storename = $filename;
        $bu = $config['bucket'] . ":" . $storename;
        $accessKey = $config['access_key'];
        $secretKey = $config['secret_key'];
        Qiniu_SetKeys($accessKey, $secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($bu);
        if ($ext == '.amr'){
            $pipeline = $config['pipeline'];
            $fops = "avthumb/mp3/ab/128k";
            $bu = str_replace('.amr', '.mp3', $bu);
            $filename = str_replace('.amr', '.mp3', $filename);
            $savekey = $this -> base64_urlSafeEncode($bu);
            $fops = $fops . '|saveas/' . $savekey;
            $putPolicy -> PersistentOps = $fops;
            $putPolicy -> PersistentPipeline = $pipeline;
        }elseif ($ext == '.mp4'){
            $pipeline = $config['pipeline'];
            $fops = "avthumb/mp4/wmGravity/NorthWest/vcodec/libx264/rotate/auto";
            if ($mask) $fops .= "/wmImage/" . $this -> base64_urlSafeEncode($mask);
            $savekey = $this -> base64_urlSafeEncode($bu);
            $fops = $fops . '|saveas/' . $savekey;
            $putPolicy -> PersistentOps = $fops;
            $putPolicy -> PersistentPipeline = $pipeline;
        }
        $upToken = $putPolicy -> Token(null);
        $putExtra = new Qiniu_PutExtra();
        $putExtra -> Crc32 = 1;
        list($ret, $err) = Qiniu_Put($upToken, $storename, $contents, $putExtra);
        if (!empty($err)){
            return "";
        }
        return trim($config['url']) . "/" . $filename;
    }
    function base64_urlSafeEncode($data){
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }
}

?>