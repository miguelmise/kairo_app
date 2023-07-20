<?php

class Planificador extends Conectar{

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
            $query = "SELECT c.cat_pro_id, c.cat_pro_nombre,SUM(p.producto_peso_estandar) as suma
            FROM categoria_producto c
            LEFT JOIN producto p ON p.producto_categoria_id = c.cat_pro_id
            WHERE c.cat_pro_estado = 1 AND p.producto_estado = 1
            GROUP BY cat_pro_id, cat_pro_nombre";

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

                $item['productos'] = $response;

            }

            return json_encode($result);

        } catch(Exception $e){
            return json_encode(["error" => $e->getMessage()]);
        }
    }

}

?>