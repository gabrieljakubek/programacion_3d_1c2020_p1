<?php
class Watermark
{
    public static function AddTextWatermark($src, $watermark, $save=NULL) { 
        list($width, $height) = getimagesize($src);
        $image_color = imagecreatetruecolor($width, $height);
        $image = imagecreatefromjpeg($src);
        imagecopyresampled($image_color, $image, 0, 0, 0, 0, $width, $height, $width, $height); //corro la imagen
        $txtcolor = imagecolorallocate($image_color, 192,192,192); //modifico el color
        $font = dirname(__FILE__).'\cabaret.ttf';
        $font_size = 50; //tamaño de la letra
        imagettftext($image_color, $font_size, 0, 50, 350, $txtcolor, $font, $watermark); //modifico el lugar donde esta la marca
        if ($save<>'') {
           imagejpeg ($image_color, $save, 100); 
           
        } else {
            header('Content-Type: image/jpeg');
            imagejpeg($image_color, null, 100);
            
        }
        imagedestroy($image); 
        imagedestroy($image_color); 
       }
    
    
       // Función para agregar marca de agua de imagen sobre imágenes
       public static function AddImageWatermark($SourceFile, $WaterMark, $DestinationFile=NULL, $opacity) {
        $main_img = $SourceFile; 
        $watermark_img = $WaterMark; 
        $padding = 5; 
        $opacity = $opacity;
        // crear marca de agua
        $watermark = imagecreatefrompng($watermark_img); 
        $image = imagecreatefromjpeg($main_img); 
        if(!$image || !$watermark) die("Error: La imagen principal o la imagen de marca de agua no se pudo cargar!");
        $watermark_size = getimagesize($watermark_img);
        $watermark_width = $watermark_size[0]; 
        $watermark_height = $watermark_size[1]; 
        $image_size = getimagesize($main_img); 
        $dest_x = $image_size[0] - $watermark_width - $padding; 
        $dest_y = $image_size[1] - $watermark_height - $padding;
        imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $opacity);
        if ($DestinationFile<>'') {
           imagejpeg($image, $DestinationFile, 100); 
        } else {
            header('Content-Type: image/jpeg');
            imagejpeg($image);
        }
        imagedestroy($image); 
        imagedestroy($watermark); 
       }
}
