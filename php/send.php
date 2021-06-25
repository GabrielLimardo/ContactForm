<?php
$field_name = $_POST['name'].' '.$_POST['surname'];

$field_email = $_POST['email'];

$field_message = $_POST['message'];
$field_telefono = $_POST['telefono'];
$field_empresa = $_POST['empresa'];
$field_cargo = $_POST['cargo'];


$mail_to = 'Fortihealthcare Web <webmaster@fortihealthcare.com>';
$field_from = 'marga@mediaorange.com.ar,jesica@mediaorange.com.ar,hugomarcelo@gmail.com';

$subject = 'Message from a site visitor '.$field_first_name;

$body_message = 'From: '.$field_first_name."\n";

$body_message .= 'Nombre: '.$field_name."\n";
$body_message .= 'E-mail: '.$field_email."\n";
$body_message .= 'Telefono: '.$field_telefono."\n";
$body_message .= 'Empresa: '.$field_empresa."\n";
$body_message .= 'Puesto: '.$field_puesto."\n";
$body_message .= 'cargo: '.$field_cargo."\n";
$body_message .= '--------------------------------------------------------';
$body_message .= 'Fecha : '.$reserva_date." --- ".$hora_selected."\n";
$body_message .= '--------------------------------------------------------';
$body_message .= 'Message: '.$field_message;

$headers = 'From: '.$mail_to."\r\n";

//$headers .= 'Reply-To: '.$field_email."\r\n";

$mail_status = mail($field_from, $subject, $body_message, $headers);
write_visita();
$inscripto_id = Cargar_base();
print_r($inscripto_id);
if ($mail_status) { ?>
	<script language="javascript" type="text/javascript">
		//alert('Thank you for the message. We will contact you shortly.');
		window.location = 'index.php';
	</script>
<?php
}
else { ?>
	<script language="javascript" type="text/javascript">
		//alert('Message failed. Please, send an email to gordon@template-help.com');
		window.location = 'index.php';
	</script>
<?php
}


function Cargar_base(){
    // set post fields
    $post = [
        'evento_id' => 47,
        'nombre' => $_POST['name'].' '.$_POST['surname'],
        'email' => $_POST['email'],
        'telefono' =>$_POST['telefono'],
        'empresa' => $_POST['empresa'],
        'cargo' => $_POST['cargo'],
        'mensages' =>' ',
        'servicios' =>1
    ];

    $ch = curl_init('https://eventos.mediaorange.com.ar/index.php/api/inscriptos/addnew');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $response = curl_exec($ch);
    $data = json_decode($response);
    curl_close($ch);
    return $data;

}

//Llamamos a la función, y ella hace todo :)
//función que escribe la IP del cliente en un archivo de texto    
function write_visita (){
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    //Indicar ruta de archivo válida
    $archivo="inscripto.txt";

    //Si que quiere ignorar la propia IP escribirla aquí, esto se podría automatizar
    $ip="mi.ip.";
    $new_ip=get_client_ip();

    if ($new_ip!==$ip){
        $now = new DateTime();

   //Distinguir el tipo de petición, 
   // tiene importancia en mi contexto pero no es obligatorio

    $data = ip_info($new_ip);
    $txt =  str_pad($new_ip,10). ";".
            str_pad($_POST['names'],10).";".
            str_pad($now->format('Y-m-d H:i:s'),10).";".
            str_pad($data['continent'],10).";".
            str_pad($data['country'],10).";".
            str_pad($data['state'],10).";".
            str_pad($data['city'],10).";".
            str_pad($_POST['email'],10).";".
            str_pad($_POST['empresa'],10).";".
            str_pad($_POST['puesto'],10).";".
            str_pad($_POST['solucion'],10);
           
         

    $myfile = file_put_contents($archivo, $txt.PHP_EOL , FILE_APPEND);
    }
}


//Obtiene la IP del cliente
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


//Obtiene la info de la IP del cliente desde geoplugin

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

?>