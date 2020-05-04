<?php
use Firebase\JWT\JWT;

require_once './vendor/firebase/php-jwt/src/JWT.php';

class ValidadorJWT
{
    private static $claveSecreta = 'pro3-parcial';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "API REST TP2",
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        $retorno = false;
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
            $retorno = true;
        } catch (\Firebase\JWT\SignatureInvalidException $b) {
            throw new Exception("El token es incorrecto!!!");
        } catch (\Exception $e) {
            throw $e;
        }
        if ($retorno && $decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
        return $retorno;
    }

    public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}
