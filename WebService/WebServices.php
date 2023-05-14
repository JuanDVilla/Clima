<?php


class Req {
    // FUNCION PARA VALIDAR EL ENVIO DE LAS VARIABLES
    public static function Request($Variable) {
        if(isset($_GET[$Variable])):
            return $_GET[$Variable];
        elseif(isset($_POST[$Variable])):
            return $_POST[$Variable];
        else:
            return "";
        endif;
    }
    
}

class WebServiceGeneral {

    public static function ciudades() {
        $dbo = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        $response = array();

        $SQLCiudades = "SELECT * FROM ciudades WHERE 1";
        $QRYCiudades = $dbo->query($SQLCiudades);  

        if(mysqli_num_rows($QRYCiudades) <= 0) {            
            $respuesta["message"] = "No hay ciudades actualmente";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

            return $respuesta;
        }
        
        $Mensaje = "Ciuades encontradas";

        while($datos = $QRYCiudades->fetch_array(MYSQLI_ASSOC)):
            $InfoResponse['id'] = $datos['id'];
            $InfoResponse['name'] = $datos['Ciudad'];
            $InfoResponse['lat'] = $datos['lat'];
            $InfoResponse['log'] = $datos['log'];

            array_push($response,$InfoResponse);            
        endwhile;


        $respuesta["message"] = $Mensaje;
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public static function humedadCiudad($id_ciudad) {
        $dbo = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
        $SQLCiudad = "SELECT * FROM ciudades WHERE id = $id_ciudad";
        $QRYCiudad = $dbo->query($SQLCiudad);  

        if(mysqli_num_rows($QRYCiudad) <= 0) {            
            $respuesta["message"] = "No existe la ciudad";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

            return $respuesta;
        }

        $data = $QRYCiudad->fetch_array(MYSQLI_ASSOC);

        $info = WebServiceGeneral::endPointHumedad($data['lat'], $data['log']);

        //GUARDAR HUMEDAD ACTUAL
        $time = date("Y-m-d H:i:s", $info['current']['dt']);
        $time_actual = date("Y-m-d H:i:s");
        $humidity = $info['current']['humidity'];
     
        $sql = "INSERT INTO historial (id_ciudad,time,humidity,time_actual) VALUES ('$id_ciudad', '$time', '$humidity', '$time_actual')";
        $insert = $dbo->query($sql);        

        $response['humidity'] = $humidity;
        $response['lat'] = $data['lat'];
        $response['lon'] = $data['log'];       
 
        $respuesta["message"] = "Humedad";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;
    }

    public static function endPointHumedad($lat, $lon) {
        
        $link = "https://api.openweathermap.org/data/3.0/onecall?lat=$lat&lon=$lon&appid=31a4ff255fa2449cb67eebdf6bea2e94";

        $response = file_get_contents($link);
        $data = json_decode($response, true);

        return $data;
    }     

    public static function historial($id_ciudad) {
        $dbo = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

        $SQLCiudades = "SELECT * FROM ciudades WHERE id = $id_ciudad";
        $QRYCiudades = $dbo->query($SQLCiudades);
        $ciudad = $QRYCiudades->fetch_object();

        $SQLCiudad = "SELECT * FROM historial WHERE id_ciudad = $id_ciudad ORDER BY time_actual DESC";
        $QRYCiudad = $dbo->query($SQLCiudad);  
        $response = array();
        if(mysqli_num_rows($QRYCiudad) <= 0) {            
            $respuesta["message"] = "No existe la ciudad";
            $respuesta["success"] = false;
            $respuesta["response"] = "";

            return $respuesta;
        }
        $data = $QRYCiudad->fetch_all(MYSQLI_ASSOC);

        foreach ($data as $key => $value) {
            $InfoResponse['humedad'] = $value['humidity'];
            $InfoResponse['tiempo'] = $value['time'];
            $InfoResponse['ciudad'] = $ciudad->Ciudad;
            $InfoResponse['tiempo_actual'] = $value['time_actual'];

            array_push($response, $InfoResponse);
        }

        $respuesta["message"] = "Historial";
        $respuesta["success"] = true;
        $respuesta["response"] = $response;

        return $respuesta;

    }

}
