<?php

error_reporting(E_ERROR);
ini_set("memory_limit","-1");

class Planificador extends Conectar{

    public function generarOrden($data){
        try {

            

            $conectar = parent::db();
            $conectar->beginTransaction();

            $query_orden = "SELECT MAX(orden_codigo) as ultimo_codigo FROM orden";
            $query_orden = $conectar->prepare($query_orden);
            $query_orden->execute();
            $resultado = $query_orden->fetch(PDO::FETCH_ASSOC);
            $codigo_orden = $resultado['ultimo_codigo']+1;

            foreach ($data as $beneficiado) {
                
                $orden_beneficiado_id = $beneficiado['beneficiado_id'];
                $orden_beneficiado_nombre = $beneficiado['beneficiado_nombre'];        

                //recorro las categorias requeridas
                foreach($beneficiado['productos'] as $requerido){

                    $categoria_id = $requerido['cat_pro_id'];
                    $cantidad_requerida = $requerido['suma'];       

                    //obtener listado producto inventario
                    $query = "SELECT c.cat_pro_id, c.cat_pro_nombre,p.producto_id,p.producto_precio,p.producto_peso_estandar,p.producto_codigo, i.*
                    FROM categoria_producto c
                    LEFT JOIN producto p ON p.producto_categoria_id = c.cat_pro_id
                    LEFT JOIN inventario i ON i.inventario_codigo = p.producto_codigo
                    WHERE cat_pro_id = {$requerido['cat_pro_id']} AND cat_pro_estado = 1 AND p.producto_estado = 1 AND i.inventario_stock > 0 AND p.producto_peso_estandar > 0
                    ORDER BY i.inventario_caducidad ASC, p.producto_peso_estandar ASC";

                    $query = $conectar->prepare($query);
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    if ($result == null || !$result) {
                        $result = array();
                    }

                    foreach ($result as $producto) {
                        $stock_entregar = 0;
                        $stock_temporal = $producto['inventario_stock_temporal'];

                        while ($stock_temporal > 0) {
                            if($cantidad_requerida > 0){
                                $stock_temporal--;
                                $cantidad_requerida-=$producto['producto_peso_estandar'];
                                $stock_entregar++;
                            }else{
                                break;
                            }
                        }

                        if($stock_entregar > 0){
                            //actualizar inventario
                            $query_update = "UPDATE inventario SET inventario_stock_temporal = {$stock_temporal}, inventario_update = NOW() 
                            WHERE inventario_id = {$producto['inventario_id']};";

                            $query_update = $conectar->prepare($query_update);
                            $query_update->execute();

                            $query_update = "INSERT INTO orden (orden_codigo, orden_beneficiado_id, orden_beneficiado_nombre, orden_producto_ubicacion, orden_producto_caducidad, 
                            orden_producto_codigo, orden_producto_descripcion, orden_proveedor_nombre, orden_producto_precio, orden_producto_cantidad, orden_fecha_emision, orden_estado)
                            VALUES('{$codigo_orden}','{$orden_beneficiado_id}','{$orden_beneficiado_nombre}','{$producto['inventario_ubicacion']}','{$producto['inventario_caducidad']}',
                            '{$producto['producto_codigo']}','{$producto['inventario_descripcion']}','{$producto['inventario_proveedor']}',{$producto['inventario_precio_promedio']},
                            {$stock_entregar},NOW(),'0');";

                            $query_update = $conectar->prepare($query_update);
                            $query_update->execute();

                            $qb = "UPDATE beneficiado SET beneficiado_ultima_entrega = NOW() WHERE beneficiado_id = {$orden_beneficiado_id}";
                            $qb = $conectar->prepare($qb);
                            $qb->execute();
                        }
                    }

                    //llenar informe
                    $cantidad_requerida_kg = $cantidad_requerida / 1000;
                    $cantidad_entregada_kg = ($requerido['suma'] -$cantidad_requerida) / 1000;

                    if($cantidad_requerida > 0){
                        $salida['informe'][$orden_beneficiado_nombre][$requerido['cat_pro_nombre']] = 'Asignado: '.$cantidad_entregada_kg.'KG , Falta: '.$cantidad_requerida_kg.' KG para la cantidad requerida';
                    }else{
                        $salida['informe'][$orden_beneficiado_nombre][$requerido['cat_pro_nombre']] = 'Asignado:'.$cantidad_entregada_kg.' KG';
                    }
                }
            }

            $salida['orden'] = $codigo_orden;

            $conectar->commit();
   
            return json_encode($salida);
        } catch(Exception $e){
            $conectar->rollback();
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function listar_productos_incompletos(){

        try {
            $conectar = parent::db();
            $query = "SELECT producto_id, producto_codigo, producto_peso, producto_sku, producto_categoria_id FROM producto 
                WHERE producto_categoria_id = 26 OR producto_peso = 0 AND producto_estado = 1";

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

    public function listarExistencias(){
        try {
            $conectar = parent::db();
            $query = "SELECT c.cat_pro_id, c.cat_pro_nombre, SUM((COALESCE(p.producto_peso_estandar, 0) * COALESCE(s.stock, 0))) AS suma
            FROM categoria_producto C
            LEFT JOIN producto p ON p.producto_categoria_id = c.cat_pro_id
            LEFT JOIN(SELECT inventario_codigo, SUM(inventario_stock) as stock FROM inventario GROUP BY inventario_codigo) s ON p.producto_codigo = s.inventario_codigo
            WHERE c.cat_pro_estado = 1 AND p.producto_estado = 1
            GROUP BY c.cat_pro_id, c.cat_pro_nombre";

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

    public function listar_beneficiados(){
        try {
            $conectar = parent::db();
            $query = "SELECT beneficiado_id,beneficiado_nombre,beneficiado_periodo,beneficiado_dia_entrega,beneficiado_ultima_entrega 
                        FROM beneficiado WHERE beneficiado_estado = 1";

            $query = $conectar->prepare($query);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if ($result == null || !$result) {
                $result = array();
            }

            $hoy = date("Y-m-d");
            $diaSemanaActual = date("l");

            foreach($result as &$item){
                // Comprobamos si la última entrega es nula
                if ($item['beneficiado_ultima_entrega'] === null) {
                    $item['turno'] = 0;
                } else {
                    // Calculamos la fecha límite según el período
                    $fechaLimite = date("Y-m-d", strtotime($item['beneficiado_ultima_entrega'] . " +{$item['beneficiado_periodo']} days"));
                    
                    // Comprobamos si hoy es el día de entrega y si ha pasado la fecha límite
                    if ($diaSemanaActual == $item['beneficiado_dia_entrega'] && $hoy >= $fechaLimite) {
                        $item['turno'] = 1;
                    } else {
                        $item['turno'] = 0;
                    }
                }

                //Buscar Porciones
                $q="SELECT cp.cat_pro_id,cp.cat_pro_nombre, SUM(c.cat_persona_beneficiado_cantidad * p.porciones_cantidad) as suma
                FROM beneficiado b 
                LEFT JOIN cat_persona_beneficiado c ON b.beneficiado_id = c.beneficiado_id
                LEFT JOIN porciones p ON c.categoria_persona_id = p.porciones_persona_id
                LEFT JOIN categoria_producto cp ON cp.cat_pro_id = p.porciones_producto_id
                WHERE b.beneficiado_id = {$item['beneficiado_id']}
                GROUP BY cp.cat_pro_id,cp.cat_pro_nombre";

                $q = $conectar->prepare($q);
                $q->execute();

                $response = $q->fetchAll(PDO::FETCH_ASSOC);
                if ($response == null || !$response) {
                    $response = array();
                }

                //quincenal
                if($item['beneficiado_periodo'] == 15){
                    foreach($response as &$detalle){
                        $detalle['suma'] = $detalle['suma'] * 2;
                    }
                }

                //mensual
                if($item['beneficiado_periodo'] == 30){
                    foreach($response as &$detalle){
                        $detalle['suma'] = $detalle['suma'] * 4;
                    }
                }

                $item['productos'] = $response;

            }

            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function aceptar_orden($data){

        try {

            $conectar = parent::db();
            $query="UPDATE inventario SET inventario_stock = inventario_stock_temporal";
            $query = $conectar->prepare($query);
            $query->execute();

            $query="UPDATE orden SET orden_estado = 1 WHERE orden_codigo = {$data}";
            $query = $conectar->prepare($query);
            $query->execute();
            
            $result = ["mensaje" => "Orden Confirmada."];
            
            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }

    }

    public function mostrar_orden($data){
        try {
            $conectar = parent::db();
            $query = "SELECT * FROM orden WHERE orden_codigo = {$data}";

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

    public function listar_ordenes(){
        try {
            $conectar = parent::db();
            $query = "SELECT DISTINCT(orden_codigo) orden_codigo, orden_fecha_emision FROM orden WHERE orden_estado = 1 ORDER BY orden_codigo DESC";

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

    public function rechazar_orden(){
        try {

            $conectar = parent::db();

            $query = "DELETE FROM orden WHERE orden_estado = 0";
            $query = $conectar->prepare($query);
            $query->execute();

            $query="UPDATE inventario SET inventario_stock_temporal = inventario_stock";
            $query = $conectar->prepare($query);
            $query->execute();
            
            $result = ["mensaje" => "Se descartó las órdenes no confirmadas."];
            
            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>