<?php
define('EVENTO_ID',47);
$inscripto_id = Cargar_base();

if($inscripto_id){ //mientras haya inscriptops

    if(send_mail()){ //si se envia el mail
        echo "";

        echo "<fieldset>";
        echo "<div id='success_page'>";
        echo "<h3>E-mail Enviado con exito.</h3>";
        echo "<p>Muchas gracias por su inscripción - Nos contactamos a la brevedad.</p>";
        echo "</div>";
        echo "</fieldset>";
    }
    
}


/*

if(send_mail()){
    echo "";

    echo "<fieldset>";
    echo "<div id='success_page'>";
    echo "<h3>E-mail Enviado con excitó.</h3>";
    echo "<p>Gracias por su consulta. Nos comunicaremos a la brevedad</p>";
    echo "</div>";
    echo "</fieldset>";
}

*/
function send_mail(){ // funcion que manda email 


    $mail_to = 'Tier1 Web <webmaster@moeventos.com.ar>'; // el mail va a salir de este mail
    $field_from = 'marga@mediaorange.com.ar,jesica@mediaorange.com.ar,yaninasisniega@gmail.com'; // el mail se va a enviar a estos mails
    $subject = 'Tier1' . $_POST{'Cata'}; // Asunto del mail

/*  $body_message = '<pre>: ';
    $body_message .=     print_r($_POST,true);
    $body_message .= '<pre/>';*/

    foreach ($_POST as $key => $value) {  //El foreach te está creando un body_message por cada $_POST.
        $body_message.= $key." = ". $value."\r\n"; //Hace una concatenación con la $key y el $value y con saltos de línea.
    }


    $headers = 'From: '.$mail_to."\r\n";
    $mail_status = mail($field_from, $subject, $body_message, $headers); //Debe quedar tipo texto. Todo para pasarle los parámetros a la función mail()
    
    if($mail_status){ //mientras mailstatus exista
        return true; //devolvera verdadero
    }

}


function Cargar_base(){
    // set post fields
    $post = [
        'evento_id' => EVENTO_ID,  // carga el eventID
        'nombre' => $_POST['name'].' '.$_POST['apellido'], // carga nombre y apellido
        'email' => $_POST['email'], // carga el mail
        'telefono' =>$_POST['tel'], // carga telefono 
        'servicios' => '', // servicio que no se usa
        'empresa' => $_POST['empresa'], // carga la empresa
        'cargo' => $_POST['cargo'], // carga el cargo
        'mensages' =>print_r($_POST,true), // carga el mensaje
    ];

    $ch = curl_init('https://eventos.mediaorange.com.ar/index.php/api/inscriptos/addnew'); //Inicia una nueva sesión
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//Configura una opción para una transferencia cURL
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//Configura una opción para una transferencia cURL
    $response = curl_exec($ch);  //Establece una sesión cURL
    $data = json_decode($response); //Decodifica un string de JSON
    return  $data->data->Inscriptos->id; //Cierra una sesión cURL
    curl_close($ch);  //va a mostrar lo que esta en la variable data
}

function cargar_reserva($inscripto_id,$reserva_id){
    // set post fields
    $post = [ //va a postear todo lo siguiente
        'evento_id' => EVENTO_ID, //el id de evento que se puso arriba
        'user_id'=>$inscripto_id, //va a tomar el userid segun el id que de de inscripto
        'reserva_id'=>$reserva_id //va a tomar el reverva id segun el id que de de la reserva
    ];

    $ch = curl_init('https://eventos.mediaorange.com.ar/index.php/api/reservas/addReserva'); //Inicia una nueva sesión
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Configura una opción para una transferencia cURL
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //Configura una opción para una transferencia cURL
    $response = curl_exec($ch);  //Establece una sesión cURL
    $data = json_decode($response); //Decodifica un string de JSON
    curl_close($ch); //Cierra una sesión cURL
    return  $data;  //va a mostrar lo que esta en la variable data

}



?>