 <?php

    $post = [
        'evento_id' => 47,  //el event id es igual al numero
        'date' => $_POST['date'], //el dia es igual al post dat
    ];

    $ch = curl_init('https://eventos.mediaorange.com.ar/index.php/api/inscriptos/addnew'); //Inicia una nueva sesión
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//Configura una opción para una transferencia cURL
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//Configura una opción para una transferencia cURL
    $response = curl_exec($ch); //Establece una sesión cURL
    curl_close($ch); //va a mostrar lo que esta en la variable data
    echo  $response;  //Muestra una o más cadenas


 ?>