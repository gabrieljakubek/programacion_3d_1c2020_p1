<?php
class Response
{
    public static function Respuesta($tipoid, $mensage)
    {
        $status = "";
        $retorno = new stdClass();
        switch ($tipoid) {
            case 1:
                $status = "succes";
                break;
            case 0:
                $status = "fail";
                break;
            case -1:
                $status = "error";
                break;
            default:
                # code...
                break;
        }
        $retorno->status = $status;
        $retorno->message = $mensage;
        return json_encode($retorno);
    }
}
