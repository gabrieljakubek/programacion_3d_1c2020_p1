<?php
class Datos
{
    public static function Guardar($destino, $datos)
    {
        $archivo = fopen($destino, 'w');
        $retorno = fwrite($archivo, $datos);
        fclose($archivo);
        return $retorno;
    }

    public static function Obtener($destino)
    {
        $datos = "";
        if (!file_exists($destino)) {
            $archivo = fopen($destino, "w");
            fclose($archivo);
        } else {
            $archivo = fopen($destino, "r");
            $largo = filesize($destino);
            if ($largo > 0) {
                $datos = fread($archivo, $largo);
            }
            fclose($archivo);
        }
        return $datos;
    }

}
