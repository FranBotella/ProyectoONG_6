<?php

try {
    $user = new Usuarios();

    $userGet = $user->getUser($_SESSION["user"]);
    $emailGet = $user->getEmail($_SESSION["user"]);
   
} catch (PDOException $e) {
    error_log($e->getMessage() . "##Código: " . $e->getCode() . "  " . microtime() . PHP_EOL, 3, "../logBD.txt");
    // save errors
    $errorsGuide['NoGuide'] = "Ha habido un error <br>";
}
if(isset( $_POST [ 'submit' ])){ 
           // $fname  =  $_POST [ 'fname' ];
           $fname= $userGet; 
            // $lname  =  $_POST [ 'nombre' ]; 
           // $email  =  $_POST [ 'correo_electrónico' ]; 
         $email=$emailGet;
            if(!empty( $email ) && ! filter_var ( $email ,  FILTER_VALIDATE_EMAIL ) ===  false ){ 
                // Credenciales API de MailChimp 
                $apiKey  =  '7f6e2fb371a9997d142b7211173fd513-us21' ; 
                $listaID  = '5436226c85' ; 
               
               
                // URL de la API de MailChimp 
                $memberID  =  md5 ( strtolower ( $email )); 
                $dataCenter  =  substr ( $apiKey , strpos ( $apiKey , '-' )+ 1 ); 
                $url  =  'https://'  . $dataCenter  . '.api.mailchimp.com/3.0/lists/'  . $listaID  . '/members/'  . $memberID ; 
                
                // información del miembro 
                $json  =  json_encode ([
                    'email_address'  =>  $email , 
                    'status'         =>  'subscribed' ,
                     'merge_fields'   => [ 'FNAME'      =>  $fname 
                    //   'LNAME'      =>  $lname 
                      ]      
                 ]); 
                     $ch  =  curl_init ( $url );
                      curl_setopt ( $ch ,  CURLOPT_USERPWD ,  'user:'  .  $apiKey );
                       curl_setopt ( $ch ,  CURLOPT_HTTPHEADER , ['Content-Type:application/json' ]); 
                curl_setopt ( $ch ,  CURLOPT_RETURNTRANSFER ,  true ); 
                curl_setopt ( $ch ,  CURLOPT_TIMEOUT ,  10 ); 
                curl_setopt ( $ch ,  CURLOPT_CUSTOMREQUEST ,  'PUT' ); 
                curl_setopt ( $ch ,  CURLOPT_SSL_VERIFYPEER ,  false ); 
                curl_setopt ( $ch ,  CURLOPT_POSTFIELDS ,  $json ); 
                $resultado  = curl_exec ( $ch ); 
                $httpCode  =  curl_getinfo ( $ch ,  CURLINFO_HTTP_CODE ); 
                curl_close ( $ch ); 
            
                // almacena el mensaje de estado basado en el código de respuesta 
                if ( $httpCode  ==  200 ) { 
                 
                    // $_SESSION [ 'msg' ] =  '<p style="color: #34A853">Se ha suscrito correctamente a GUP.</p> ' ; 
                    $_SESSION["alta"]=true;
                    header("location:index.php?ctl=suscribirse");
                } else { 
                 
                    switch ( $httpCode ) { 
                        case  214 : 
                            // $msg  =  'Ya estás suscrito.'; 
                          
                        //     romper; 
                        break;
                        // predeterminado:
                        case 401 : 
                            // $msg  =  'Ocurrió algún problema, intente nuevamente.' ; 
                            // romper; 
                            break;
                    } 
                    $_SESSION [ 'msg' ] =  '<p style="color: #EA4335">' . $msg . '</p>' ; 
                    header("location:index.php?ctl=suscribirse");
                } 
            }else{ 
                // $_SESSION [ 'msg' ] =  '<p style="color: #EA4335">Ingrese una dirección de correo electrónico válida.</p>' ; 
                $_SESSION["correoSuscribirse"]=true;
                header("location:index.php?ctl=suscribirse");
            } 
        }
       


       

        ?>