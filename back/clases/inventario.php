<?php

class Inventario extends Conectar{

    public function listarInventario(){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM inventario WHERE inventario_stock > 0";

            $query = $conectar->prepare($query);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if ($result == null || !$result) {
                $result = array();
            }
            return json_encode($result);
        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function actualizar_producto_inventario($data){
        try {
            // Validación de datos
            if(!isset($data['inventario_id'])){return json_encode(["mensaje" => "Falta inventario_id"]);}
            if(!isset($data['inventario_codigo'])){return json_encode(["mensaje" => "Falta inventario_codigo"]);}
            if(!isset($data['inventario_ubicacion'])){return json_encode(["mensaje" => "Falta inventario_ubicacion"]);}
            if(!isset($data['inventario_descripcion'])){return json_encode(["mensaje" => "Falta inventario_descripcion"]);}
            if(!isset($data['inventario_lote'])){return json_encode(["mensaje" => "Falta inventario_lote"]);}
            if(!isset($data['inventario_stock'])){return json_encode(["mensaje" => "Falta inventario_stock"]);}
            if(!isset($data['inventario_proveedor'])){return json_encode(["mensaje" => "Falta inventario_proveedor"]);}
            if(!isset($data['inventario_precio_promedio'])){return json_encode(["mensaje" => "Falta inventario_precio_promedio"]);}
            if(!isset($data['inventario_costo_total'])){return json_encode(["mensaje" => "Falta inventario_costo_total"]);}

            $conectar = parent::db();
            $conectar->beginTransaction();
            $query = "UPDATE inventario SET inventario_ubicacion = '{$data['inventario_ubicacion']}',
            inventario_descripcion = '{$data['inventario_descripcion']}',
            inventario_lote = '{$data['inventario_lote']}',
            inventario_proveedor = '{$data['inventario_proveedor']}',
            inventario_precio_promedio = {$data['inventario_precio_promedio']},
            inventario_stock = {$data['inventario_stock']},
            inventario_costo_total = {$data['inventario_costo_total']},
            inventario_update = NOW()
            WHERE inventario_id = '{$data['inventario_id']}'";

            $query = $conectar->prepare($query);
            $query->execute();

            if ($query->rowCount() > 0) {

                $query_producto = "UPDATE producto SET producto_precio = {$data['inventario_precio_promedio']} WHERE producto_codigo = {$data['inventario_codigo']}";
                $query_producto = $conectar->prepare($query_producto);
                $query_producto->execute();

                $conectar->commit();

                $result = ["mensaje" => "Producto actualizado exitosamente."];
                
            } else {
                $result = ["mensaje" => "No hubo cambios en los datos del Producto."];
            }

            return json_encode($result);

        } catch(Exception $e){
            $conectar->rollBack();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function cargar_inventario($data){

        $conectar = parent::db();

        try {
            $resultados = [
                'productos_nuevos' => 0,
                'proveedores_nuevos' => 0,
                'productos_ingresados' => 0
            ];
             //Validacion encabezados archivo
            $encabezados_requeridos = array('Código',
            'Ubicación',
            'Caducidad',
            'Descripción',
            'Lote',
            'Proveedor',
            'U/M',
            'Stock',
            'Precio Promedio',
            'Costo Total');

            $faltantes = array();
            foreach ($data as $json){
                foreach ($encabezados_requeridos as $campo) {
                    if (!array_key_exists($campo, $json)) {
                    $faltantes[] = $campo;
                    }
                }
            break;
            }   

            if(!empty($faltantes)){return json_encode(["error" => "Falta los siguientes encabezados en el archivo: ".implode(', ', $faltantes)]);}

            $conectar->beginTransaction();

            //Recorrer los elementos
            foreach ($data as $producto) {
                //verificar producto existe
                
                

                // Verificar si el producto ya existe
                $queryVerificar = "SELECT COUNT(*) AS count FROM producto WHERE producto_codigo = :producto_codigo";
                $queryVerificar = $conectar->prepare($queryVerificar);
                $queryVerificar->execute([':producto_codigo' => $producto['Código']]);
                $rowCount = $queryVerificar->fetch(PDO::FETCH_ASSOC)['count'];

                //Sino existe se crea el producto
                if ($rowCount == 0) {

                    //obtener peso y unidad de medida
                    $patron = "/(\d+(?:\.\d+)?)-(\d+(?:\.\d+)?) ([A-Z]+)$/i";

                    $promedio = 0;
                    $unidadMedida = "";
                    $rango="0";
                    $medidas = array('KG','GR','ML','LT');

                    // Realizar la búsqueda del patrón en el texto
                    if (preg_match($patron, $producto['Descripción'], $matches)) {
                        // El patrón se encontró en el texto
                        if(is_numeric($matches[1]) && is_numeric($matches[2])){
                            $promedio = ($matches[1] + $matches[2]) / 2;
                            $rango = $matches[1]."-".$matches[2];
                        }
                        if(in_array(strtoupper($matches[3]),$medidas)){
                            $unidadMedida = strtoupper($matches[3]);
                        }
                    }

                    
                    // Insertar el producto
                    $queryInsertar = "INSERT INTO producto (producto_categoria_id, producto_codigo, producto_descripcion, producto_medida, producto_peso, producto_precio, producto_rango_peso, producto_sku, producto_update, producto_estado) 
                    VALUES (:producto_categoria_id, :producto_codigo, :producto_descripcion, :producto_medida, :producto_peso, :producto_precio, :producto_rango_peso, :producto_sku, NOW(), 1)";
                    $queryInsertar = $conectar->prepare($queryInsertar);
                    $queryInsertar->execute([
                    ':producto_categoria_id' => 26,
                    ':producto_codigo' => $producto['Código'],
                    ':producto_descripcion' => $producto['Descripción'],
                    ':producto_medida' => $unidadMedida,
                    ':producto_peso' => $promedio,
                    ':producto_rango_peso' => $rango,
                    ':producto_precio' => $producto['Precio Promedio'],
                    ':producto_sku' => $producto['Descripción']
                    ]);

                    if ($queryInsertar->rowCount() == 0) {
                        throw new Exception("Error al procesar el producto: " . $producto['Descripción']);
                    }else{
                        $resultados['productos_nuevos']++;
                    }
                    
                                
                }

                $proveedor = $producto['Proveedor'] == "" ? "SIN DATOS" : $producto['Proveedor'];

                //verificar si existe el proveedor
                $queryVerificar = "SELECT COUNT(*) AS count FROM donante WHERE donante_nombre = :nombre";
                $queryVerificar = $conectar->prepare($queryVerificar);
                $queryVerificar->execute([':nombre' => strtoupper($proveedor)]);
                $rowCount = $queryVerificar->fetch(PDO::FETCH_ASSOC)['count'];
        
                //Sino existe se crea el proveedor
                if ($rowCount == 0) {
                    $queryInsertar = "INSERT INTO donante (donante_nombre, donante_tipo, donante_descripcion, donante_fecha_update, donante_estado)
                        VALUES (:nombre, :tipo, :descripcion, NOW(), 1)";
                    $queryInsertar = $conectar->prepare($queryInsertar);
                    $queryInsertar->execute([
                        ':nombre' => strtoupper($proveedor),
                        ':tipo' => "OTRO",
                        ':descripcion' => ""
                    ]);
            
                    if ($queryInsertar->rowCount() == 0) {
                        throw new Exception("Error al procesar el producto: " . $producto['Descripción']);
                    }else{
                        $resultados['proveedores_nuevos']++;
                    }
                    
                }

                //guardar producto en inventario
                $caducidad = $producto['Caducidad'] != "" ? date('Y-m-d', strtotime($producto['Caducidad'])) : null;

                if(is_numeric($producto['Stock'])){
                    $stock = intval($producto['Stock']);
                }else{
                    throw new Exception("En el archivo el valor de Stock para el producto con còdigo: " . $producto['Código']. " No es vàlido");
                }

                if(is_numeric($producto['Precio Promedio']) || $producto['Precio Promedio'] == ""){
                    $precio = $producto['Precio Promedio'] != "" ? floatval($producto['Precio Promedio']) : 0;
                }else{
                    throw new Exception("En el archivo el valor de Precio para el producto con còdigo: " . $producto['Código']. " No es vàlido");
                }

                if(is_numeric($producto['Costo Total']) || $producto['Costo Total'] == ""){
                    $costo = $producto['Costo Total'] != "" ? floatval($producto['Costo Total']) : 0;
                }else{
                    throw new Exception("En el archivo el valor de Costo Total para el producto con còdigo: " . $producto['Código']. " No es vàlido");
                }

                $query_i = "INSERT INTO inventario (inventario_codigo,inventario_ubicacion,inventario_caducidad,inventario_descripcion,inventario_lote,
                inventario_proveedor,inventario_um,inventario_stock,inventario_stock_temporal,inventario_precio_promedio,inventario_costo_total,inventario_update) 
                VALUES(:inventario_codigo,:inventario_ubicacion,:inventario_caducidad,:inventario_descripcion,:inventario_lote,
                :inventario_proveedor,:inventario_um,:inventario_stock,:inventario_stock_temporal,:inventario_precio_promedio,:inventario_costo_total,NOW())";

                $query_i = $conectar->prepare($query_i);
                $query_i->execute([
                    ':inventario_codigo' => $producto['Código'],
                    ':inventario_ubicacion' => $producto['Ubicación'],
                    ':inventario_caducidad' => date('Y-m-d', strtotime($producto['Caducidad'])),
                    ':inventario_descripcion' => $producto['Descripción'],
                    ':inventario_lote' => $producto['Lote'],
                    ':inventario_proveedor' => $producto['Proveedor'],
                    ':inventario_um' => $producto['U/M'],
                    ':inventario_stock' => $stock,
                    ':inventario_stock_temporal' => $stock,
                    ':inventario_precio_promedio' => $precio,
                    ':inventario_costo_total' => $costo
                ]);
                $resultados['productos_ingresados']++;

                
            }

            $conectar->commit();
            
            $jsonResult = json_encode($resultados);
            return $jsonResult;

        } catch(Exception $e){
            $conectar->rollBack();
            return json_encode(["error" => $e->getMessage()]);
        }

       
    }

}

?>