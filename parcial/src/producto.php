<?php
require_once "datos.php";
require_once 'archivo.php';
class Producto
{
    public $id;
    public $tipo;
    public $sabor;
    public $precio;
    public $stock;
    public $foto;

    private static $direccionDatos = './data/productos.txt';
    private static $direccionImagenes = './imagenes/';

    public function __construct($tipo, $sabor = "-", $precio = '-', $id = '-', $stock = '-', $foto = '-')
    {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->sabor = $sabor;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->foto = $foto;
    }

    public static function CargarProductos()
    {
        $productos = array();
        $datos = Datos::Obtener(self::$direccionDatos);
        if (strlen($datos) > 0) {
            $productos = unserialize($datos);
        }
        return $productos;
    }

    public function GuardarImagen($imagen)
    {
        $this->foto = Archivo::GuardarArchivo($imagen, self::$direccionImagenes, $this->id);
    }

    public static function GuardarProductos($productos)
    {
        Datos::Guardar(self::$direccionDatos, serialize($productos));
    }

    public function MostrarDatos()
    {
        echo "ID:" . $this->id . "\r\n";
        echo "Sabor:" . $this->marca . "\r\n";
        echo "Tipo:" . $this->tipo . "\r\n";
        echo "Precio:" . $this->precio . "\r\n";
        echo "Stock:" . $this->stock . "\r\n";
        echo "Foto:" . $this->foto . "\r\n";
    }

    public static function ListarProductos($productos, $full = false)
    {
        $retorno = array();
        if ($full) {
            $retorno = $productos;
        } else {
            foreach ($productos as $key => $value) {
                $producto = new stdClass();
                $producto->id = $value->id; 
                $producto->tipo = $value->tipo; 
                $producto->sabor = $value->sabor; 
                $producto->precio = $value->precio;
                array_push($retorno,$producto); 
            }
        }
        return json_encode($retorno);
    }

    public function BuscarProducto($productos)
    {
        $retorno = false;
        foreach ($productos as $key => $value) {

            if ($this->tipo == $value->tipo && $this->sabor == $value->sabor) {
                $retorno = true;
                break;
            }
        }
        return $retorno;
    }

    public static function BuscarProductObj($productos, $tipo,$sabor)
    {
        $retorno = null;
        foreach ($productos as $key => $value) {
            if ($tipo == $value->tipo && $sabor == $value->sabor) {
                $retorno = $value;
                break;
            }
        }
        return $retorno;
    }

    public static function ModificarProductos($productos, $producto)
    {
        $retorno = $productos;
        foreach ($retorno as $key => $value) {
            if ($value->id == $producto->id) {
                $value->tipo = $producto->tipo;
                $value->sabor = $producto->sabor;
                $value->precio = $producto->precio;
                $value->stock = $producto->stock;
                $value->foto = $producto->foto;
                break;
            }
        }
        return $retorno;
    }
}
