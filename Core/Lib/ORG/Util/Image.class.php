<?php

class Image extends Base
{
    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if( $imageInfo!== false) {
            if(function_exists(image_type_to_extension)){
                $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            }else{
                $imageType = strtolower(substr($img,strrpos($img,'.')+1));
            }
            $imageSize = filesize($img);
            $info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

    static function showImg($imgFile,$text='',$width=80,$height=30) {
        $info = Image::getImageInfo($imgFile);
        if($info !== false) {
            $createFun  =   str_replace('/','createfrom',$info['mime']);
            $im = $createFun($imgFile);
            if($im) {
                $ImageFun= str_replace('/','',$info['mime']);
                if(!empty($text)) {
                    $tc  = imagecolorallocate($im, 0, 0, 0);
                    imagestring($im, 3, 5, 5, $text, $tc);
                }
                if($info['type']=='png' || $info['type']=='gif') {
                imagealphablending($im, false);
                imagesavealpha($im,true);
                }
                Header("Content-type: ".$info['mime']);
                $ImageFun($im);
                @ImageDestroy($im);
                return ;
            }
        }
        $im  = imagecreatetruecolor($width, $height);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 4, 5, 5, "NO PIC", $tc);
        Image::output($im);
        return ;
    }

    static function thumb($image,$type='',$filename='',$maxWidth=200,$maxHeight=50,$interlace=true,$suffix='_thumb')
    {
        $info  = Image::getImageInfo($image);
         if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $pathinfo = pathinfo($image);
            $type =  $pathinfo['extension'];
            $type = empty($type)?$info['type']:$type;
            $type = strtolower($type);
            $interlace  =  $interlace? 1:0;
            unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); 
            if($scale>=1) {
                $width   =  $srcWidth;
                $height  =  $srcHeight;
            }else{
                $width  = (int)($srcWidth*$scale);
                $height = (int)($srcHeight*$scale);
            }

            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image);

            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);

            if(function_exists("ImageCopyResampled"))
                ImageCopyResampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
            else
                ImageCopyResized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight);
            if('gif'==$type || 'png'==$type) {
                $background_color  =  imagecolorallocate($thumbImg,  0,255,0); 
                imagecolortransparent($thumbImg,$background_color); 
            }

            if('jpg'==$type || 'jpeg'==$type)   imageinterlace($thumbImg,$interlace);

            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            $filename  = empty($filename)? substr($image,0,strrpos($image, '.')).$suffix.'.'.$type : $filename;

            $imageFun($thumbImg,$filename);
            ImageDestroy($thumbImg);
            ImageDestroy($srcImg);
            return $filename;
         }
         return false;
    }

    static function buildImageVerify($length=4,$mode=1,$type='png',$width=48,$height=22,$verifyName='verify')
    {
        $randval = build_verify($length,$mode);
        $_SESSION[$verifyName]= md5($randval);
        $width = ($length*9+10)>$width?$length*9+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);

        $backColor = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);   
        $borderColor = imagecolorallocate($im, 100, 100, 100);                 
        $pointColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));           

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor = imagecolorallocate($im,mt_rand(0,200),mt_rand(0,120),mt_rand(0,120));

        for($i=0;$i<10;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
        }
        for($i=0;$i<25;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pointColor);
        }

        @imagestring($im, 5, 5, 3, $randval, $stringColor);
        Image::output($im,$type);
    }

    static function GBVerify($length=4,$type='png',$width=180,$height=50,$fontface='simhei.ttf',$verifyName='verify') {
        $code   =   rand_string($length,4);
        $width = ($length*45)>$width?$length*45:$width;
        $_SESSION[$verifyName]= md5($code);
        $im=imagecreatetruecolor($width,$height);
        $borderColor = imagecolorallocate($im, 100, 100, 100);               
        $bkcolor=imagecolorallocate($im,250,250,250);
        imagefill($im,0,0,$bkcolor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);

        for($i=0;$i<15;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
        }
        for($i=0;$i<255;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
        }
        if(!is_file($fontface)) {
            $fontface = dirname(__FILE__)."/".$fontface;
        }
        for($i=0;$i<$length;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,120),mt_rand(0,120),mt_rand(0,120)); 
            $codex= msubstr($code,$i,1);
            imagettftext($im,mt_rand(16,20),mt_rand(-60,60),40*$i+20,mt_rand(30,35),$fontcolor,$fontface,$codex);
        }
        Image::output($im,$type);
    }

    static function showASCIIImg($image,$string='',$type='')
    {
        $info  = Image::getImageInfo($image);
        if($info !== false) {
            $type = empty($type)?$info['type']:$type;
            unset($info);
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $im     = $createFun($image);
            $dx = imagesx($im);
            $dy = imagesy($im);
            $i  =   0;
            $out   =  '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
            set_time_limit(0);
            for($y = 0; $y < $dy; $y++) {
              for($x=0; $x < $dx; $x++) {
                  $col = imagecolorat($im, $x, $y);
                  $rgb = imagecolorsforindex($im,$col);
                  $str   =   empty($string)?'*':$string[$i++];
                  $out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">'.$str.'</span>',$rgb['red'],$rgb['green'],$rgb['blue']);
             }
             $out .= "<br>\n";
            }
            $out .=  '</span>';
            imagedestroy($im);
            return $out;
        }
        return false;
    }

    static function showAdvVerify($type='png',$width=180,$height=40)
    {
        $rand   =   range('a','z');
        shuffle($rand);
        $verifyCode =   array_slice($rand,0,10);
        $letter = implode(" ",$verifyCode);
        $_SESSION['verifyCode'] = $verifyCode;
        $im = imagecreate($width,$height);
        $r = array(225,255,255,223);
        $g = array(225,236,237,255);
        $b = array(225,236,166,125);
        $key = mt_rand(0,3);
        $backColor = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);
        $borderColor = imagecolorallocate($im, 100, 100, 100);                
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $numberColor = imagecolorallocate($im, 255,rand(0,100), rand(0,100));
        $stringColor = imagecolorallocate($im, rand(0,100), rand(0,100), 255);
        for($i=0;$i<10;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
        }
        for($i=0;$i<255;$i++){
            $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
        }
        imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9", $numberColor);
        imagestring($im, 5, 5, 20, $letter, $stringColor);
        Image::output($im,$type);
    }

    static function UPCA($code,$type='png',$lw=2,$hi=100) {
        static $Lencode = array('0001101','0011001','0010011','0111101','0100011',
                         '0110001','0101111','0111011','0110111','0001011');
        static $Rencode = array('1110010','1100110','1101100','1000010','1011100',
                         '1001110','1010000','1000100','1001000','1110100');
        $ends = '101';
        $center = '01010';
        /* UPC-A Must be 11 digits, we compute the checksum. */
        if ( strlen($code) != 11 ) { die("UPC-A Must be 11 digits."); }
        /* Compute the EAN-13 Checksum digit */
        $ncode = '0'.$code;
        $even = 0; $odd = 0;
        for ($x=0;$x<12;$x++) {
          if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
        }
        $code.=(10 - (($odd * 3 + $even) % 10)) % 10;
        /* Create the bar encoding using a binary string */
        $bars=$ends;
        $bars.=$Lencode[$code[0]];
        for($x=1;$x<6;$x++) {
          $bars.=$Lencode[$code[$x]];
        }
        $bars.=$center;
        for($x=6;$x<12;$x++) {
          $bars.=$Rencode[$code[$x]];
        }
        $bars.=$ends;
        /* Generate the Barcode Image */
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw*95+30,$hi+30);
        }else {
            $im = imagecreate($lw*95+30,$hi+30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw*95+30, $hi+30, $bg);
        $shift=10;
        for ($x=0;$x<strlen($bars);$x++) {
          if (($x<10) || ($x>=45 && $x<50) || ($x >=85)) { $sh=10; } else { $sh=0; }
          if ($bars[$x] == '1') { $color = $fg; } else { $color = $bg; }
          ImageFilledRectangle($im, ($x*$lw)+15,5,($x+1)*$lw+14,$hi+5+$sh,$color);
        }
        /* Add the Human Readable Label */
        ImageString($im,4,5,$hi-5,$code[0],$fg);
        for ($x=0;$x<5;$x++) {
          ImageString($im,5,$lw*(13+$x*6)+15,$hi+5,$code[$x+1],$fg);
          ImageString($im,5,$lw*(53+$x*6)+15,$hi+5,$code[$x+6],$fg);
        }
        ImageString($im,4,$lw*95+17,$hi-5,$code[11],$fg);
        /* Output the Header and Content. */
        Image::output($im,$type);
    }

    static public function water($source,$water,$savename=null,$alpha=80)
    {
        if(!is_file($source)||!is_file($water))
            return false;

        $sInfo=self::getImageInfo($source);
        $wInfo=self::getImageInfo($water);

        if($sInfo["width"]<$wInfo["width"] || $sInfo['height']<$wInfo['height'])
            return false;

        $sCreateFun="imagecreatefrom".$sInfo['type'];
        $sImage=$sCreateFun($source);
        $wCreateFun="imagecreatefrom".$wInfo['type'];
        $wImage=$wCreateFun($water);

        imagealphablending($wImage, true);

        $posY=$sInfo["height"]-$wInfo["height"];
        $posX=$sInfo["width"]-$wInfo["width"];

         imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'],$wInfo['height'],$alpha);

        $ImageFun='Image'.$sInfo['type'];
        if(!$savename){
            $savename=$source;
            @unlink($source);
        }
        $ImageFun($sImage,$savename);
        imagedestroy($sImage);

    }

    static function output($im,$type='png')
    {
        header("Content-type: image/".$type);
        $ImageFun='Image'.$type;
        $ImageFun($im);
        imagedestroy($im);
    }
}

?>