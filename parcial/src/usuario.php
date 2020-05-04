<?php
require_once "datos.php";
class Usuario
{
    public $id;
    public $email;
    private $clave;
    public $tipo;

    private static $direccionDatos = './data/usuarios.txt';

    public function __construct($email, $clave = "-", $tipo = '-', $id = '-')
    {
        $this->id = $id;
        $this->clave = $clave;
        $this->email = $email;
                $this->tipo = $tipo;
    }

    public static function CargarUsuarios()
    {
        $usuarios = array();
        $datos = Datos::Obtener(self::$direccionDatos);
        if (strlen($datos) > 0) {
            $usuarios = unserialize($datos);
        }
        return $usuarios;
    }

    public static function GuardarUsuarios($usuarios)
    {
        Datos::Guardar(self::$direccionDatos, serialize($usuarios));
    }

    public function BuscarUsuario($usuarios, $token = false)
    {
        $retorno;
        foreach ($usuarios as $key => $value) {
            if ($this->email == $value->email) {
                if ($this->clave == $value->clave && !$token) {
                    $retorno = $value;
                    break;
                } else {
                    $retorno = $value;
                    break;
                }
            }
        }
        return $retorno;
    }

    public function BuscarEmail($usuarios)
    {
        $retorno = false;
        foreach ($usuarios as $key => $value) {
            if ($this->email == $value->email) {
                $retorno = true;
                break;
            }
        }
        return $retorno;
    }

    public function ValidarUsuario($usuarios)
    {
        $retorno = false;
        foreach ($usuarios as $key => $value) {
            if ($this->email == $value->email && $this->clave == $value->clave) {
                $retorno = true;
                break;
            }
        }
        return $retorno;
    }

    public function MostrarDatos()
    {
        echo "ID:" . $this->id . "\r\n";
        echo "Email:" . $this->email . "\r\n";
        echo "Clave:" . $this->clave . "\r\n";
        echo "Tipo:" . $this->tipo . "\r\n";
    }

    public function ListarPersonas($usuarios)
    {
        switch ($this->tipo) {
            case 'user':
                $this->MostrarDatos();
                break;
            case 'admin':
                foreach ($usuarios as $key => $value) {
                    $value->MostrarDatos();
                }
                break;
        }
    }

    public static function BuscarUsuarioTkn($usuarios, $email, $clave)
    {
        $retorno = null;
        $dataToken = new stdClass();
        foreach ($usuarios as $key => $value) {
            if ($email == $value->email && $clave == $value->clave) {
                $dataToken->email = $value->email;
                $dataToken->tipo = $value->tipo;
                $retorno = $dataToken;
                break;
            }
        }
        return $retorno;
    }

}
