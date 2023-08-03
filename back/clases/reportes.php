<?php

class Reporte extends Conectar{

    public function obtenerOrdenes(){
        try {
            $conectar = parent::db();
            $query = "select orden_beneficiado_nombre,orden_producto_ubicacion,orden_producto_caducidad, orden_producto_codigo, orden_producto_descripcion,orden_proveedor_nombre,orden_producto_precio,orden_producto_cantidad,orden_fecha_emision from orden where orden_estado=1";

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

    public function buscar_ordenes($data){
        try {

            if(!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])){
                $q_fecha = " AND orden_fecha_emision BETWEEN '{$data['fecha_inicio']} 00:00:01' AND '{$data['fecha_fin']} 23:59:59' ";
            }else{
                $q_fecha = "";
            }

            if(!empty($data['proveedor'])){
                $q_proveedor = " OR orden_proveedor_nombre LIKE '%{$data['proveedor']}%' ";
            }else{
                $q_proveedor = "";
            }

            if(!empty($data['codigo_producto'])){
                $q_codigo = " orden_producto_codigo LIKE '%{$data['codigo_producto']}%' ";
            }else{
                $q_codigo = "";
            }

            if(!empty($data['beneficiario'])){
                $q_beneficiado = " OR orden_beneficiado_nombre LIKE '%{$data['beneficiario']}%' ";
            }else{
                $q_beneficiado = "";
            }

            if(!empty($data['beneficiario']) || !empty($data['codigo_producto']) || !empty($data['proveedor'])){
                $filtros = "AND ({$q_codigo} {$q_proveedor} {$q_beneficiado})";
            }else{
                $filtros = "";
            }
            
            $conectar = parent::db();
            $query = "SELECT orden_beneficiado_nombre, orden_producto_ubicacion, orden_producto_caducidad, 
            orden_producto_codigo, orden_producto_descripcion, orden_proveedor_nombre, orden_producto_precio, 
            orden_producto_cantidad, orden_fecha_emision
            FROM orden
            WHERE orden_estado = 1 {$filtros} {$q_fecha}";

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

    public function reporte_donadores($data){
        try {
            $conectar = parent::db();
            $query = "SELECT 
            COALESCE(NULLIF(o.orden_proveedor_nombre, ''), 'SIN NOMBRE') AS orden_proveedor_nombre,
            o.orden_producto_codigo,
            o.orden_producto_descripcion,
            SUM((COALESCE(p.producto_peso_estandar, 0) * COALESCE(o.orden_producto_cantidad, 0))) AS peso,
            SUM((COALESCE(p.producto_precio, 0) * COALESCE(o.orden_producto_cantidad, 0))) AS precio
            FROM orden o
            LEFT JOIN producto p ON p.producto_codigo = o.orden_producto_codigo
            WHERE o.orden_estado = 1 AND o.orden_fecha_emision BETWEEN '{$data['fecha_inicio']} 00:00:01' AND '{$data['fecha_fin']} 23:59:59'
            GROUP BY COALESCE(NULLIF(o.orden_proveedor_nombre, ''), 'SIN NOMBRE'), o.orden_producto_codigo, o.orden_producto_descripcion";

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
}

?>
