<?php
require __DIR__ . '/vendor/autoload.php';
require_once "./src/usuario.php";
require_once "./src/response.php";
require_once "./src/producto.php";
require_once "./src/venta.php";
require_once "./src/validadorjwt.php";
$request_method = $_SERVER["REQUEST_METHOD"];
$opciones_get = "Acciones permitidas /stock o /ventas";
$opciones_post = "Acciones permitidas /usuario, /login, /stock o /ventas";
$error_usuario = "Se requieren del email, clave, tipo (cliente o encargado) para poder crear un usuario";
$error_producto = "Se requieren del producto, marca, precio, stock y foto para poder crear un producto";
$error_venta = "Se requieren de id_producto, cantidad y usuario para poder realizar una venta";
$error_login = "Se requieren de el email y clave para poder loggear";
$usuarios = Usuario::CargarUsuarios();
$productos = Producto::CargarProductos();
$ventas = Venta::CargarVentas();
$headers = apache_request_headers();
//var_dump($usuarios);
switch ($request_method) {
    case 'POST':
        if (empty($_SERVER['PATH_INFO'])) {
            echo Response::Respuesta(-1, $opciones_post);
        } else {
            $path_info = $_SERVER["PATH_INFO"];
            switch ($path_info) {
                case '/usuario':
                    if (isset($_POST['email']) == true && $_POST['email'] != '' && isset($_POST['clave']) == true && $_POST['clave'] != '' &&
                        isset($_POST['tipo']) == true && $_POST['tipo'] != '') {
                        $id = rand();
                        $email = $_POST['email'];
                        $clave = $_POST['clave'];
                        $tipo = $_POST['tipo'];
                        $usuario = new Usuario($email, $clave, $tipo, $id);
                        if (!$usuario->BuscarEmail($usuarios)) {
                            array_push($usuarios, $usuario);
                            Usuario::GuardarUsuarios($usuarios);
                            echo Response::Respuesta(1, "Usuario Registrado");
                        } else {
                            echo Response::Respuesta(0, "Email ya registrado");
                        }
                    } else {
                        echo Response::Respuesta(-1, $error_usuario);
                    }
                    break;
                case '/login':
                    if (isset($_POST['email']) == true && $_POST['email'] != '' && isset($_POST['clave']) == true && $_POST['clave'] != '') {
                        $email = $_POST['email'];
                        $clave = $_POST['clave'];
                        $datatoken = Usuario::BuscarUsuarioTkn($usuarios, $email, $clave);
                        if (!is_null($datatoken)) {
                            echo Response::Respuesta(1, "Token: " . ValidadorJWT::CrearToken($datatoken));
                        } else {
                            echo Response::Respuesta(0, "Usuario o Clave incorrecta!!!");
                        }
                    } else {
                        echo Response::Respuesta(-1, $error_login);
                    }
                    break;
                case '/pizzas':
                    if (isset($headers['token']) == true) {
                        try {
                            $resp = ValidadorJWT::VerificarToken($headers['token']);
                            if ($resp) {
                                $datos = ValidadorJWT::ObtenerData($headers['token']);
                                if ($datos->tipo == 'encargado') {
                                    if (isset($_POST['tipo']) == true && $_POST['tipo'] != '' && isset($_POST['sabor']) == true && $_POST['sabor'] != '' &&
                                        isset($_POST['precio']) == true && $_POST['precio'] != '' && isset($_POST['stock']) == true && $_POST['stock'] != '' &&
                                        isset($_FILES['foto']) == true && $_FILES['foto']['name'] != '') {
                                        $id = rand();
                                        $tipo = $_POST['tipo'];
                                        $sabor = $_POST['sabor'];
                                        $precio = $_POST['precio'];
                                        $stock = $_POST['stock'];
                                        $foto = $_FILES['foto'];
                                        $producto = new Producto($tipo, $sabor, $precio, $id, $stock);
                                        if (!$producto->BuscarProducto($productos)) {
                                            $producto->GuardarImagen($foto);
                                            array_push($productos, $producto);
                                            Producto::GuardarProductos($productos);
                                            echo Response::Respuesta(1, "Se registro la Pizza");
                                        } else {
                                            echo Response::Respuesta(0, "Combinacion de pizza ya registrada");
                                        }
                                    } else {
                                        echo Response::Respuesta(-1, $error_producto);
                                    }
                                } else {
                                    echo Response::Respuesta(-1, "Solo encargados pueden acceder");
                                }

                            }
                        } catch (\Throwable $th) {
                            echo Response::Respuesta(-1, $th->getMessage());
                        }
                    } else {
                        echo Response::Respuesta(-1, "Se requiere de Token");
                    }
                    break;
                case '/ventas':
                    if (isset($headers['token']) == true) {
                        try {
                            $resp = ValidadorJWT::VerificarToken($headers['token']);
                            if ($resp) {
                                $datos = ValidadorJWT::ObtenerData($headers['token']);
                                if ($datos->tipo == 'cliente') {
                                    if (isset($_POST['tipo']) == true && $_POST['tipo'] != '' && isset($_POST['sabor']) == true && $_POST['sabor'] != '') {
                                        $id = rand();
                                        $tipo = $_POST['tipo'];
                                        $sabor = $_POST['sabor'];
                                        $usuario = $datos->email;
                                        $producto = Producto::BuscarProductObj($productos, $tipo, $sabor);
                                        if (!is_null($producto)) {
                                            $resta = $producto->stock - 1;
                                            if ($resta >= 0) {
                                                $venta = new Venta($id, $tipo, $sabor, $usuario, $producto->precio);
                                                $producto->stock = $resta;
                                                $productos = Producto::ModificarProductos($productos, $producto);
                                                Producto::GuardarProductos($productos);
                                                array_push($ventas, $venta);
                                                Venta::GuardarVentas($ventas);
                                                echo Response::Respuesta(1, "Se realizo la venta");
                                            } else {
                                                echo Response::Respuesta(0, "Stock insuficiente");
                                            }
                                        } else {
                                            echo Response::Respuesta(0, "Producto inexistente");
                                        }
                                    } else {
                                        echo Response::Respuesta(-1, $error_venta);
                                    }
                                } else {
                                    echo Response::Respuesta(-1, "Solo usuarios user pueden acceder");
                                }

                            }
                        } catch (\Throwable $th) {
                            echo Response::Respuesta(-1, $th->getMessage());
                        }
                    } else {
                        echo Response::Respuesta(-1, "Se requiere de Token");
                    }
                    break;
                default:
                    echo Response::Respuesta(-1, $opciones_post);
                    break;
            }
        }
        break;

    case 'GET':
        if (empty($_SERVER['PATH_INFO'])) {
            echo Response::Respuesta(-1, $opciones_get);
        } else {
            $path_info = $_SERVER["PATH_INFO"];
            switch ($path_info) {
                case '/pizzas':
                    if (isset($headers['token']) == true) {
                        try {
                            $resp = ValidadorJWT::VerificarToken($headers['token']);
                            if ($resp) {
                                $datos = ValidadorJWT::ObtenerData($headers['token']);
                                switch ($datos->tipo) {
                                    case 'encargado':
                                        echo Response::Respuesta(1, Producto::ListarProductos($productos, true));
                                        break;
                                    case 'cliente':
                                        echo Response::Respuesta(1, Producto::ListarProductos($productos));
                                        break;

                                    default:
                                        echo Response::Respuesta(-1, "Tipo de usuario no permitido");
                                        break;
                                }
                            }
                        } catch (\Throwable $th) {
                            echo Response::Respuesta(-1, $th->getMessage());
                        }
                    } else {
                        echo Response::Respuesta(-1, "Se requiere de Token");
                    }

                    break;
                case '/ventas':
                    if (isset($headers['token']) == true) {
                        try {
                            $resp = ValidadorJWT::VerificarToken($headers['token']);
                            if ($resp) {
                                $datos = ValidadorJWT::ObtenerData($headers['token']);
                                switch ($datos->tipo) {
                                    case 'encargado':
                                        echo Response::Respuesta(1, json_encode(Venta::ResumenCaja($ventas)));
                                        break;
                                    case 'cliente':
                                        echo Response::Respuesta(1, json_encode(Venta::BuscarVentaUsuario($ventas, $datos->email)));
                                        break;

                                    default:
                                        echo Response::Respuesta(-1, "Tipo de usuario no permitido");
                                        break;
                                }
                            }
                        } catch (\Throwable $th) {
                            echo Response::Respuesta(-1, $th->getMessage());
                        }
                    } else {
                        echo Response::Respuesta(-1, "Se requiere de Token");
                    }
                    break;
                default:
                    echo Response::Respuesta(-1, $opciones_get);
                    break;
            }
        }
        break;

    default:
        echo Response::Respuesta(-1, "Solo se permite GET o POST");
        break;
}
