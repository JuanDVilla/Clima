<?php

require_once('general.php');

$Servicio = Req::Request('Servicio');

if(empty($Servicio)):
    die(json_encode(array('success' => false, 'message' => "No se especifico servicio", 'response' => "", 'date' => $nowserver)));
    exit;
endif;

switch($Servicio):    
    
    case 'ciudades':        
        $respuesta = WebServiceGeneral::ciudades();
        die(json_encode(array('success' => $respuesta['success'], 'message' => $respuesta['message'], 'response' => $respuesta['response'])));
        exit;
    break;   

    case 'humedad':
        $id = Req::Request('id');
        $respuesta = WebServiceGeneral::humedadCiudad($id);
        die(json_encode(array('success' => $respuesta['success'], 'message' => $respuesta['message'], 'response' => $respuesta['response'])));
        exit;
    break; 

    case 'historial':
        $id = Req::Request('id');
        $respuesta = WebServiceGeneral::historial($id);
        die(json_encode(array('success' => $respuesta['success'], 'message' => $respuesta['message'], 'response' => $respuesta['response'])));
        exit;
    break;   

    default:

        die(json_encode(array('success' => false, 'message' => 'no existe el servicio ('.$Servicio.')', 'response' => "")));
        exit;

    break;

endswitch;



