<?php
namespace app\common\extend\qrcode;
    /**
     ** Action : ���ɶ�ά��ͼƬ
     * $para : $url : ��ά�����Ϣ
     *         $fileName : ͼƬ���� Ĭ�Ϻ�׺ .png
     *         $level : �ݴ���
     *         $size  : ���ö�ά���С
     **/
public function creat_qrcode($url,$fileName,$level='L',$size='4'){
        //�����ά�����ļ�(�����ر���ʾ  ���������ļ����� ��׺Ϊ   .class.php ��ô�������Ҫ����׺����ͼ)
        // Loader::import('phpqrcode', EXTEND_PATH);
    Vendor('Phpqrcode.phpqrcode');  

        //ʵ�������ɶ�ά����
        $object = new \QRcode();

        //�������ɵĶ�ά��ͼƬ����
        $errorCorrectionLevel =intval($level) ;//�ݴ���
        $matrixPointSize = intval($size);//����ͼƬ��С

        $object->png($url,$fileName.'.png', $errorCorrectionLevel, $matrixPointSize, 2);
    }