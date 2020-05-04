<?php
require_once "datos.php";
require_once 'archivo.php';
class Venta
{
    public $id;
    public $tipo;
    public $sabor;
    public $monto;
    public $usuario;
    public $fecha;

    private static $direccionDatos = './data/ventas.txt';

    public function __construct($id, $tipo, $sabor, $usuario, $monto)
    {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->sabor = $sabor;
        $this->usuario = $usuario;
        $this->monto = $monto;
        $this->fecha = date('Y-m-d H:i:s');
    }

    public static function CargarVentas()
    {
        $ventas = array();
        $datos = Datos::Obtener(self::$direccionDatos);
        if (strlen($datos) > 0) {
            $ventas = unserialize($datos);
        }
        return $ventas;
    }

    public static function GuardarVentas($ventas)
    {
        Datos::Guardar(self::$direccionDatos, serialize($ventas));
    }

    public function MostrarDatos()
    {
        echo "ID:" . $this->id . "\r\n";
        echo "ID Producto:" . $this->id_producto . "\r\n";
        echo "Cantidad:" . $this->cantidad . "\r\n";
        echo "Usuario:" . $this->usuario . "\r\n";
    }

    public static function ListarVentas($ventas)
    {
        return json_encode($ventas);
    }

    public function BuscarVenta($ventas)
    {
        $retorno = false;
        foreach ($productos as $key => $value) {
            if ($this->producto == $value->producto) {
                $retorno = true;
                break;
            }
        }
        return $retorno;
    }

    public static function ResumenCaja($ventas)
    {
        $resumen = new stdClass();
        $resumen->montoCaja = 0;
        $resumen->cantidadVentas = count($ventas);
        foreach ($ventas as $key => $value) {
            $resumen->montoCaja  += $value->monto;
        }
        return $resumen;
    }

    public static function BuscarVentaUsuario($ventas, $usuario)
    {
        $retorno = array();
        foreach ($ventas as $key => $value) {
            if ($value->usuario == $usuario) {
                array_push($retorno, $value);
            }
        }
        return $retorno;
    }
}
