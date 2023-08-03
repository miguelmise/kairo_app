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
}

?>