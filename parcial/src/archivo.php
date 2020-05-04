<?php
require_once 'watermark.php';
class Archivo
{
    public static function GuardarArchivo($archivo, $destino, $nombre)
    {        
        if (!file_exists($destino) ){
            mkdir($destino);
        }
        $archivoTmp = $archivo["tmp_name"];
        $nombreFormato = Archivo::GenerarNombre($archivo,$nombre);
        $rta = move_uploaded_file($archivoTmp, $destino.$nombreFormato);
        if (!$rta) {
            $nombreFormato = null;
        }
        else{
            Watermark::AddTextWatermark($destino.$nombreFormato,"Gabriel Jakubek",$destino.$nombreFormato);
        }
        return $nombreFormato;
    }

    public static function GenerarNombre($archivo, $nombre)
    {
        return $nombre . "." . explode("/", $archivo["type"])[1];
    }
    public static function BackUpArchivo($raiz, $nombreOriginal,$destino,$nombreBackUp)
    {
        if (!file_exists($destino) ){
            mkdir($destino);
        }
        copy($raiz.$nombreOriginal,$destino.$nombreBackUp);
    }

    public static function BorrarArchivo($raiz,$nombre)
    {
        if (is_dir($raiz) && file_exists($raiz.$nombre) ) {
            unlink($raiz.$nombre);
        }
    }
}
