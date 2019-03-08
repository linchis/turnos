<?php

header('Content-Type: text/html; charset=utf-8');
$host = $_SERVER['HTTP_HOST'];
//NOMBRE DEL SERVICIO
$host_name = "BASIC SERVICE REST";
setlocale(LC_TIME, "es_ES.utf8");
date_default_timezone_set('Europe/Madrid');
$version = "2.0";
//URL: <generate APIKEY>
$appkey = "a41becca1be8c4a9fe737d8a9a6c6f28";

//DEFAULT URL: http://proyectosxs.xyz/webtmp/services/rest/XXX

/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';
require '../../../../db/Medoo.php';
use Medoo\Medoo;
//require 'db_config.php';          //cada BBDD diferente
require_once '../../../../lib/KLogger.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Install LOG aplication
 */
$log = new KLogger ( "log.txt" , KLogger::DEBUG );

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */


//Ruta base para acceder a los servicios de "token"
$ws_base_path = "http://proyectosxs.xyz/webapp/services/token";
//$ws_base_path = $_SERVER['DOCUMENT_ROOT']."/webapp/services/token";
//default_token: 99999999999999999999999999999999

//ADMINISTRACION y CONSUMO
//Mediante este sevicio podemos saber si la app está ACTIVA/INACTIVA y en caso de estar ACTIVA
//obtener un token para poder acceder a los servicios
$app->get('/obtenertoken/:apikey','obtencion');
//Mediante este servicio podemos comprobar si el token que tenemos es válido para acceder a los
//servicios y obtener uno nuevo en caso de expiracion.
$app->get('/validartoken/:token','validacion');

//GENERICAS WEBSERVICE
$app->get('/','home');
$app->post('/login','login');
$app->post('/loginMD5','loginMD5');

//Service ENABLED
$app->get('/status/:serv', 'isEnabled');

//FUNCIONALES
//TURNOS
$app->get('/:token', 'getTurnos');
$app->post('/consume/:agente/:token', 'consumeTurno');
//$app->get('/consume/:agente/:token', 'consumeTurno');
$app->post('/nuevo/:usuario/:token', 'nuevoTurno');
//COLAS
$app->delete('/colas/:token', 'borrarColas');


//USUARIOS
$app->get('/usuarios/:token', 'getUsuarios');
$app->get('/usuario/:id/:token', 'getUsuario');
$app->post('/usuario/:id/:token', 'addUsuario');    //INSERT OR UPDATE
$app->delete('/usuario/:id/:token', 'deleteUsuario');
//PUESTOS
$app->get('/puestos/:token', 'getPuestos');
$app->get('/puesto/:id/:token', 'getPuesto');
$app->post('/puesto/:id/:token', 'addPuesto');    //INSERT OR UPDATE
$app->delete('/puesto/:id/:token', 'deletePuesto');

//MENSAJES
$app->get('/mensaje/:token', 'getMensaje');


//TODO: bool function_exists ( string $function_name ) Verificar si una funcion existe!!!!

$app->run();

/**
 * Funciones
 */

function home() {
    global $host_name;
    $template = <<<EOT
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>$host_name</title>
            <style>
                html,body,div,span,object,iframe,
                h1,h2,h3,h4,h5,h6,p,blockquote,pre,
                abbr,address,cite,code,
                del,dfn,em,img,ins,kbd,q,samp,
                small,strong,sub,sup,var,
                b,i,
                dl,dt,dd,ol,ul,li,
                fieldset,form,label,legend,
                table,caption,tbody,tfoot,thead,tr,th,td,
                article,aside,canvas,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section,summary,
                time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;}
                body{line-height:1;}
                article,aside,details,figcaption,figure,
                footer,header,hgroup,menu,nav,section{display:block;}
                nav ul{list-style:none;}
                blockquote,q{quotes:none;}
                blockquote:before,blockquote:after,
                q:before,q:after{content:'';content:none;}
                a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent;}
                ins{background-color:#ff9;color:#000;text-decoration:none;}
                mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold;}
                del{text-decoration:line-through;}
                abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help;}
                table{border-collapse:collapse;border-spacing:0;}
                hr{display:block;height:1px;border:0;border-top:1px solid #cccccc;margin:1em 0;padding:0;}
                input,select{vertical-align:middle;}
                html{ background: #EDEDED; height: 100%; }
                body{background:#FFF;margin:0 auto;min-height:100%;padding:0 30px;width:550px;color:#666;font:14px/23px Arial,Verdana,sans-serif;}
                h1,h2,h3,p,ul,ol,form,section{margin:0 0 20px 0;}
                h1{color:#333;font-size:20px;}
                h2,h3{color:#333;font-size:14px;}
                h3{margin:0;font-size:12px;font-weight:bold;}
                ul,ol{list-style-position:inside;color:#999;}
                ul{list-style-type:square;}
                code,kbd{background:#EEE;border:1px solid #DDD;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:0 4px;color:#666;font-size:12px;}
                pre{background:#EEE;border:1px solid #DDD;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;padding:5px 10px;color:#666;font-size:12px;}
                pre code{background:transparent;border:none;padding:0;}
                a{color:#70a23e;}
                header{padding: 30px 0;text-align:center;}
            </style>
        </head>
        <body>
            <header>
                ProyectosXs
            </header>
           
            <h1 style="text-align:center;">TURNOS API REST!</h1>
           
            <section style="display:none;">
                <h2>Get Started</h2>
                <ol>
                    <li>The application code is in <code>index.php</code></li>
                    <li>Read the <a href="http://docs.slimframework.com/" target="_blank">online documentation</a></li>
                    <li>Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter</li>
                </ol>
            </section>
            <section style="display:none;">
                <h2>Slim Framework Community</h2>

                <h3>Support Forum and Knowledge Base</h3>
                <p>
                    Visit the <a href="http://help.slimframework.com" target="_blank">Slim support forum and knowledge base</a>
                    to read announcements, chat with fellow Slim users, ask questions, help others, or show off your cool
                    Slim Framework apps.
                </p>

                <h3>Twitter</h3>
                <p>
                    Follow <a href="http://www.twitter.com/slimphp" target="_blank">@slimphp</a> on Twitter to receive the very latest news
                    and updates about the framework.
                </p>
            </section>
            
            <section style="display:none;" style="padding-bottom: 20px">
                <h2>Slim Framework Extras</h2>
                <p>
                    Custom View classes for Smarty, Twig, Mustache, and other template
                    frameworks are available online in a separate repository.
                </p>
                <p><a href="https://github.com/codeguy/Slim-Extras" target="_blank">Browse the Extras Repository</a></p>
            </section>

            <section style="padding-bottom: 20px">          
                %%datetime_modify%%
            </section>  

            <section style="padding-bottom: 20px">
                <!--
                <h2>LOGIN</h2>
                <form method="POST" action="loginMD5" style="margin-left:50px;">
                    <input type="text" name="email" placeholder="Email" />
                    <input type="password" name="pwd" placeholder="Password"/>
                    <input type="submit" value="ENVIAR"/>
                </form>
                -->     
                <h2>TURNOS</h2>
                <h4>Gestion de colas, puestos y usaurios</h4>
                <ul>
                    <li>[get] /:token </li>
                    <li>[post] /consume/:agente/:token</li>
                    <li>[post] /nuevo/:usuario/:token</li>
                    <li>[delete] /colas/:token</li>
                    <li>[get] /usuarios/:token</li>
                    <li>[get] /usuario/:id/:token</li>
                    <li>[post] /usuario/:id/:token</li>
                    <li>[delete] /usuario/:id/:token</li>
                    <li>[get] /puestos/:token</li>
                    <li>[get] /puesto/:id/:token</li>
                    <li>[post] /puesto/:id/:token</li>
                    <li>[delete] /puesto/:id/:token</li>
                    <!--<li> <strike>[DELETE] /score/scoreid/token </strike></li>-->
                </ul>
            </section>
            
        </body>
    </html>
EOT;
    echo str_replace("%%datetime_modify%%", "Ultima modificacion: ".date("F d Y H:i:s.",filemtime("index.php")),$template);
}

/**
 * Funcion para obtener un TOKEN a partid de un APIKEY, el TOKEN podra tener validez limitada o ilimitada, debiendo obtener uno nuevo si el anterior caducó
 * @param $apikey
 */
function obtencion($apikey) {
    global $log;
    global $ws_base_path;

    try {
        //echo "Informacion recibida:::".$token;
        $resp = file_get_contents($ws_base_path."/service.php?key=".$apikey."&action=GETTOKEN");
        //$resp = file_get_contents($ws_base_path."/service.php?key=".$apikey."&action=GETTOKEN",false,$context);
        //$resp = file_get_contents(__DIR__."/service.php?key=".$apikey."&action=GETTOKEN",false,$context);
        echo $resp;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        $log->LogError("Validar apikey ERROR: ".$e->getMessage());
    }
}

/**
 * Funcion para validacion de TOKEN
 */
function validacion($token) {
    global $log;

    try {
        //echo "Informacion recibida:::".$token;
        $resp = token('ISVALIDTOKEN',$token);
        echo $resp;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        $log->LogError("Validar token: ".$token." ERROR: ".$e->getMessage());
    }
}
 

function login() {

    global $log;
    $request = \Slim\Slim::getInstance()->request();
    //Obtener los parametros*********************************

    $sql = "SELECT NOMBRE,APELLIDOS,TOKEN 
                FROM USUARIO 
                WHERE EMAIL = '".$request->post('email')."' AND 
                    PASSWORD = '".$request->post('pwd')."' AND 
                    ACTIVO = 1 LIMIT 1;";
    $log->LogInfo("LOGIN:Query:::".$sql);

    try {
        $db = getConnection();
        $user = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        $db = null;

        if(isset($user) && count($user)==1){
            echo json_encode($user[0]);     //TODO generar token de usuario
            $log->LogInfo("User login: ".$request->post('email')." ok");
        }else{
            echo null;
            $log->LogInfo("User login: ".$request->post('email')." intento de login fallido");
        }


    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        $log->LogError("User login: ".$request->post('email')." ERROR: ".$e->getMessage());
    }
}


function loginMD5() {
    global $log;
    $request = \Slim\Slim::getInstance()->request();
    //Obtener los parametros*********************************
    $sql = "SELECT NOMBRE,APELLIDOS,TOKEN,ACTIVO,ROL
            FROM USUARIO 
            WHERE EMAIL = '".$request->post('email')."' 
                AND PASSWORD = '".md5($request->post('pwd'))."' LIMIT 1;";
    $log->LogInfo("LOGIN_MD5:Query:::".$sql);
    try {
        $db = getConnection();
        $user = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        $db = null;

        if(isset($user) && count($user)==1){
            echo json_encode($user[0]);     //TODO generar token de usuario
            $log->LogInfo("User login_MD5: ".$request->post('email')." ok");

        }else{
            echo null;
            $log->LogInfo("User login_MD5: ".$request->post('email')." intento de login fallido");
        }

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        $log->LogError("User login_MD5: ".$request->post('email')." ERROR: ".$e->getMessage());

    }
}


//Funcion COMUN***********************************************************

function isEnabled($service) {
    global $log;
    $respuesta = array(
        'activo'    =>true
    );
    echo json_encode($respuesta);
}


//TURNOS************************************************
/**
 * Funcion para obtener todos los TURNOS (OK)
 * @param $token
 */
function getTurnos($token) {
    global $log;
    if (esTokenValido($token)){
        echo json_encode(_getTurnos($log)); ;
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }    
}


function _getTurnos(){
    global $log;
    $respuesta = null;
    $turnosPendientes = "";
    $ultimoTurnoEmitido = "";        
    $ultimoTurnoConsumido = "";

    $sql1 = "select * from pendientes where ESTADO = 0 ORDER BY TURNO ASC;";
    $sql2 = "SELECT * FROM pendientes order by TURNO DESC limit 1;"; 
    $sql3 = "select c.*,p.* from pendientes p,consumidos c where c.IDTURNO = p.ID order by c.ID DESC limit 1;";        

    try {

        $log->LogInfo("TURNOS PENDIENTES:Query:::".$sql1);
        $db = getConnection();                    
        $result1 = $db->query($sql1)->fetchAll(PDO::FETCH_CLASS);
        if(isset($result1)){
            $turnosPendientes = $result1;                                
        }

        $log->LogInfo("GET ULTIMO TURNO EMITIDO:Query:::".$sql2);
        $db = getConnection();
        $result2 = $db->query($sql2)->fetchAll(PDO::FETCH_CLASS);
        $db = null;
        if(isset($result2)){
            $ultimoTurnoEmitido = $result2;                                
        }
       
        $log->LogInfo("ULTIMO TURNO CONSUMIDO:Query:::".$sql3);
        $db = getConnection();                    
        $result3 = $db->query($sql3)->fetchAll(PDO::FETCH_CLASS);
        if(isset($result3)){
            $ultimoTurnoConsumido = $result3;                                
        }

        $respuesta = array(
            "turnos_pendientes" => $turnosPendientes!=null?$turnosPendientes:0,
            "ultimo_turno_emitido" => $ultimoTurnoEmitido!=null?$ultimoTurnoEmitido:0,    
            "ultimo_turno_consumido" => $ultimoTurnoConsumido!=null?$ultimoTurnoConsumido:0
        );          

    } catch(PDOException $e) {
        $log->LogError("Ver turnos ERROR:::". $e->getMessage());
        $error = array(
            "text" => $e->getMessage()
        );
        $respuesta = array(
            "error" => $error            
        );        
    }
    return $respuesta;
}


/**
 * Funcion para consumir TURNO por un AGENTE/PUESTO (OK)
 * @param $token
 */
function consumeTurno($agente,$token) {
    global $log;
    if (esTokenValido($token)){
        try {
            $log->LogInfo("CONSUME TURNO:::".$agente.", ".$token);  
          
        //OPERACION DE BLOQUE (ATOMICA) SINCRONIZADA    
            /*
            class My extends Thread {
                public function run() {
                    $this->synchronized(function($thread){
                        if (!$thread->done)
                            $thread->wait();
                    }, $this);
                }
            }
            $my = new My();
            $my->start();
            $my->synchronized(function($thread){
                $thread->done = true;
                $thread->notify();
            }, $my);
            var_dump($my->join());
            */


            $database = getConnection();    
            $database->pdo->beginTransaction();
 
            //$database->action(function($db) {
            //Minimo turno no atendido
            $sql1 = "select * from pendientes where ESTADO=0 ORDER BY TURNO ASC Limit 1;";
            $data = $database->query($sql1)->fetchAll(PDO::FETCH_CLASS);
            $log->LogInfo("MIN TURNO:::".json_encode($data));    

                                    
            //});
            
                

            //Add turnos consumido           
            $result = $database->insert("consumidos",[
                    "IDTURNO" => $data[0]->ID,
                    "PUESTO" => $agente
                ]);
            $log->LogInfo("ADD TURNO CONSUMIDO:::".$result->rowCount());                
            
            //update turno pendiente                
            $result = $database->update("pendientes",[
                    "ESTADO" => 1
                ],[
                    "ID" => $data[0]->ID
                ]);
            $log->LogInfo("UPDATE_ TURNO PENDIENTE:::".$result->rowCount());   
            
            //add mensaje
            $mensaje = json_encode(_getTurnos($token));
            $result = $database->insert("mensajes",[
                    "MENSAJE" => $mensaje  
                ]);
            $lastID = $result->rowCount();
            $log->LogInfo("ADD MENSAJE TURNO CONSUMIDO:::ID:".$lastID."MSG:::".$mensaje);

            if ($lastID > 0){

                $database->pdo->commit();                    
                $respuesta = array(
                    'action'    =>'consumeturno-'.$agente,
                    'turno_consumido' => $data
                );
                echo json_encode($respuesta);     

            }else{
                
                $database->pdo->rollBack();    
                //return false;
            }   
//            });

            /*
            $sql1 = "select * from pendientes where ESTADO=0 ORDER BY TURNO ASC Limit 1;";
            //Minimo turno no atendido
            $database = getConnection();    
            $data = $db->query($sql1)->fetchAll(PDO::FETCH_CLASS);
            $log->LogInfo("MIN TURNO:::".json_encode($data));              
            $db = null;    
                         
            //Add turnos consumido
            $db = getConnection();    
            $result = $db->insert("consumidos",[
                    "IDTURNO" => $data[0]->ID,
                    "PUESTO" => $agente
                ]);
            $log->LogInfo("ADD TURNO CONSUMIDO:::".$result->rowCount());                
            $db = null;    
            
            //update turno pendiente
            $db = getConnection();    
            $result = $db->update("pendientes",[
                    "ESTADO" => 1
                ],[
                    "ID" => $data[0]->ID
                ]);
            $log->LogInfo("UPDATE_ TURNO PENDIENTE:::".$result->rowCount());                
            $db = null;    
            

            $respuesta = array(
                'action'    =>'consumeturno-'.$agente,
                'turno_consumido' => $data
            );
            echo json_encode($respuesta);     
            */

         } catch(PDOException $e) {
            $log->LogError("Consume turno ERROR:::". $e->getMessage());
            echo '{"error":{"text":'. $e->getMessage() .'}}';     
        }
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }       
}

/**
 * Funcion para generar un nuevo TURNO por un USUARIO (OK)
 * @param $token
 */
function nuevoTurno($usuario,$token) {
    global $log;
    if (esTokenValido($token)){
        try {
            $sql = "SELECT * FROM pendientes order by TURNO DESC limit 1;"; 
            $log->LogInfo("GET ULTIMO TURNO EMITIDO:Query:::".$sql);
            $db = getConnection();
            $result = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            $db = null;
            if(isset($result)){
                $ultimoTurnoEmitido = $result;                                
                if ($ultimoTurnoEmitido!=null){         
                    $turno = $ultimoTurnoEmitido[0]->TURNO + 1;
                }else{          
                    $turno = 1;
                }
            }   

            $db = getConnection();    
            $result = $db->insert("pendientes",[
                "TURNO" => $turno,
                "IDUSUARIO" => $usuario,
                "ESTADO" => 0
                ]);
            $log->LogInfo("ADD TURNO RESULT:::".$result->rowCount());               
            $db = null;    

            $respuesta = array(
                'action'    =>'nuevoturno',
                'nuevo_turno' => $turno
            );
            echo json_encode($respuesta);

        } catch(PDOException $e) {
            $log->LogError("Nuevo turno ERROR:::". $e->getMessage());
            echo '{"error":{"text":'. $e->getMessage() .'}}';     
        }
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


/**
 * Funcion para limpiar las lista de turnos y los usuarios registrados
 * @param $token
 */
function borrarColas($token) {
    global $log;
    if (esTokenValido($token)){
        $log->LogInfo("Borrar tablas...");
        try {
            $db = getConnection();
            $result = $db->delete("pendientes",[]);
            $log->LogInfo("borrada tabla: pendientes");
            $db = null;

            $db = getConnection();
            $result = $db->delete("consumidos",[]);
            $log->LogInfo("borrada tabla: consumidos");
            $db = null;

            $db = getConnection();
            $result = $db->delete("usuario",[]);
            $log->LogInfo("borrada tabla: usuario");
            $db = null;

            $respuesta = array(
                'action'    =>'borrarcolas',
                'result'    =>'OK'
            );
            echo json_encode($respuesta);

        } catch(PDOException $e) {
            $log->LogError("Borrar tablas ERROR:::". $e->getMessage());
            echo '{"error":{"text":'. $e->getMessage() .'}}';     
        }
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}



//FUNCIONES COMPLEMENTARIAS*****************
//USUARIOS
function getUsuarios($token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function getUsuario($idusuario,$token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function addUsuario($idusuario,$token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function deleteUsuario($idusuario,$token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


//PUESTOS
function getPuestos($token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function getPuesto($idpuesto,$token) {
    global $log;
    if (esTokenValido($token)){
        try {
            
            //DATOS DEL PUESTO            
            $db = getConnection();
            $puesto = $db->select("puesto","*",[
                'ID' => $idpuesto
            ]);
            $log->LogInfo("Datos del puesto: ".$idpuesto);
            $db = null;

            //TURNOS ATENDIDOS
            $db = getConnection();
            $atendidos = $db->select("consumidos","*",[
                'PUESTO' => $idpuesto
            ]);
            $log->LogInfo("Turnos consumidos en el puesto: ".$idpuesto);
            $db = null;
            
            //TIEMPOS DE ATENCION
            $db = getConnection();
            $sql = "SELECT * FROM consumidos c,pendientes p where c.PUESTO = 1 && p.ID = c.IDTURNO";
            $tiempos = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            $log->LogInfo("Turnos consumidos en el puesto: ".$idpuesto);
            $db = null;
            
            $result = array(
                'puesto'    => $puesto,
                'atendidos' => $atendidos,
                'tiempos'   => $tiempos
            );

            $respuesta = array(
                'action'    => 'infopuesto',
                'response'  => $result
            );
            echo json_encode($respuesta);

        } catch(PDOException $e) {
            $log->LogError("Borrar tablas ERROR:::". $e->getMessage());
            echo '{"error":{"text":'. $e->getMessage() .'}}';     
        }
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function addPuesto($idpuesto,$token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


function deletePuesto($idpuesto,$token) {
    global $log;
    if (esTokenValido($token)){
        $respuesta = array(
            'action'    =>'borrarcolas'
        );
        echo json_encode($respuesta);
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}

//Obtiene el ultimo mensaje de la base de datos y lo borra
function getMensaje($token){
    global $log;
    if (esTokenValido($token)){
        try {
            
            //DATOS DEL PUESTO            
            $sql = "select * from mensajes order by ID desc limit 1;";
            $db = getConnection();
            $msg = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            $log->LogInfo("Mensaje: ".json_encode($msg));            
            $db = null;

            if (isset($msg) && sizeof($msg)>0){

                //BORRAR MENSAJE
                $db = getConnection();
                $result = $db->delete("mensajes",[
                    'ID' => $msg[0]->ID
                ]);
                $log->LogInfo("consumir mensaje".$msg[0]->ID);
                $db = null;
                
                echo str_replace("\\", "", json_encode($msg[0]));
            }else{
                echo null;
            }

        } catch(PDOException $e) {
            $log->LogError("Get mensaje ERROR:::". $e->getMessage());
            echo '{"error":{"text":'. $e->getMessage() .'}}';     
        }
    }else{
        echo '{"error":{"text":"Token required"}}';    
    }
}


//EXAMPLE FUNCTIONS***************************************************************************
/**
 * Funcion para obtener todos los items almacenados en BBDD
 * @param $token
 */
function getAllItems($token) {
    global $log;

    if (esTokenValido($token)){

        $sql = "SELECT i.* 
                FROM DIRECTORIO_ITEM i 
                ORDER BY i.NOMBRE asc;";

        $log->LogInfo("GET_ALL_ITEMS:Query:::".$sql);
        try {
            $db = getConnection();
            $items = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            $db = null;
            if(isset($items)){
                echo json_encode($items);
                $log->LogInfo("Ver items");
            }else{
                echo '';
            }
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
            $log->LogError("Ver items ERROR:::". $e->getMessage());
        }
    }else{
        echo '{"error":{"text":"Token required"}}';
    }
}

/**
 * Funcion para obtener el items (id) almacenados en BBDD
 * @param $id, $token
 */
function getItem($id,$token) {
    global $log;

    if (esTokenValido($token)){

        $sql = "SELECT i.* 
                FROM DIRECTORIO_ITEM i 
                WHERE i.id = $id
                ORDER BY i.NOMBRE asc;";

        $log->LogInfo("GET_ITEM:Query:::".$sql);
        try {
            $db = getConnection();
            $items = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
            $db = null;
            if(isset($items)){
                echo json_encode($items);
                $log->LogInfo("Ver item");
            }else{
                echo '';
            }
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
            $log->LogError("Ver item ERROR:::". $e->getMessage());
        }
    }else{
        echo '{"error":{"text":"Token required"}}';
    }
}


/**
 * Funcion para añadir un item en BBDD (POST)
 * @param $id, $token
 */
function addItem($token){
    global $log;
    $log->LogInfo("Peticion: Insertar Item:");

    if (esTokenValido($token)){
       $request = \Slim\Slim::getInstance()->request();
        //JSON
        $parametros = procesar($request->getBody());
        $item = myunserialize($parametros);

        ob_start();
        var_dump($item);
        $result = ob_get_clean();
                
        try {
            $db = getConnection();
            $item = $db->insert(
                "DIRECTORIO_ITEM",
                array(
                    'NOMBRE' => $item["contenido_nombre"],
                    'TIPO' => $item["contenido_tipo"],
                    'DESCRIPCION' => $item["contenido_descripcion"],
                    'ENLACE' => $item["contenido_enlace"],                    
                    'TELEFONO' => $item["contenido_telefono"],                    
                    'POSICION' => $item["contenido_posicion"],
                    'ACTIVO' => $item["contenido_activo"]                    
                )
            );
            $log->LogInfo("Insertando item: ".$db->id());
            $db = null;
            $estado = "OK";
            $mensaje = "Item insertado!";
        } catch(PDOException $e) {
            $log->LogError("Insertar item: ".$id." ERROR:::". $e->getMessage());
            $estado = "FALLO";
            $mensaje = "No se ha insertado item: " . $e;
        }
        $respuesta = array(
            "estado" => $estado,
            "mensaje" => $mensaje
        );
        echo json_encode($respuesta);
        $log->LogInfo("FIN Insert item");
        
    }else{
        echo '{"error":{"text":"Token required"}}';
    }
}

/**
 * Funcion para actualizar un item (id) almacenado en BBDD (PUT)
 * @param $id, $token
 */
function updateItem($id,$token){
    global $log;
    $log->LogInfo("Peticion: Actualizar Item:" . $id);

    if (esTokenValido($token)){
       $request = \Slim\Slim::getInstance()->request();
        //JSON
        $parametros = procesar($request->getBody());
        $item = myunserialize($parametros);

        ob_start();
        var_dump($item);
        $result = ob_get_clean();
                
        try {
            $db = getConnection();
            $item = $db->update(
                "DIRECTORIO_ITEM",
                array(
                    'NOMBRE' => $item["contenido_nombre"],
                    'TIPO' => $item["contenido_tipo"],
                    'DESCRIPCION' => $item["contenido_descripcion"],
                    'ENLACE' => $item["contenido_enlace"],                    
                    'TELEFONO' => $item["contenido_telefono"],                    
                    'POSICION' => $item["contenido_posicion"],
                    'ACTIVO' => $item["contenido_activo"]                    
                ),
                array(
                    'ID' => $id
                )
            );
            $log->LogInfo("Actualizado item: ".$db->id());
            $db = null;
            $estado = "OK";
            $mensaje = "Item actualizado!";
        } catch(PDOException $e) {
            $log->LogError("Actualizar item: ".$id." ERROR:::". $e->getMessage());
            $estado = "FALLO";
            $mensaje = "No se ha actualizado item: " . $e;
        }
        $respuesta = array(
            "estado" => $estado,
            "mensaje" => $mensaje
        );
        echo json_encode($respuesta);
        $log->LogInfo("FIN Update item");
        
    }else{
        echo '{"error":{"text":"Token required"}}';
    }
}

/**
 * Funcion para borrar un item (id) almacenado en BBDD (PUT)
 * @param $id, $token
 */
function deleteItem($id,$token){
    global $log;
    $log->LogInfo("Peticion: borrar Item:" . $id);

    if (esTokenValido($token)){
                       
        try {
            $db = getConnection();
            $item = $db->delete(
                "DIRECTORIO_ITEM",                
                array(
                    'ID' => $id
                )
            );
            $log->LogInfo("borrado item: ".$db->id());
            $db = null;
            $estado = "OK";
            $mensaje = "Item borrado!";
        } catch(PDOException $e) {
            $log->LogError("Borrar item: ".$id." ERROR:::". $e->getMessage());
            $estado = "FALLO";
            $mensaje = "No se ha borrado item: " . $e;
        }
        $respuesta = array(
            "estado" => $estado,
            "mensaje" => $mensaje
        );
        echo json_encode($respuesta);
        $log->LogInfo("FIN delete item");
        
    }else{
        echo '{"error":{"text":"Token required"}}';
    }
}

//**********************************************************************************************


//*************************************************************************************************************
//FUNCIONES GENERALES
//*************************************************************************************************************
function getConnection() {
    $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'u525703232_turno',
            'server' => 'mysql.hostinger.es',
            'username' => 'u525703232_turno',
            'password' => 'pruebaturnos2019'
        ]);         
    return $database;
}

function changeDateFormat($date){
    $originalDate = "2010-03-21";
    $originalDate = $date;
    $newDate = date("Y-m-d", strtotime($originalDate));
    return $newDate;
}

function peticion($tipo, $destino, $params){

    $url = $destino;
    //$data = array('field1' => 'value', 'field2' => 'value');

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => $tipo,
            'content' => http_build_query($params),
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;

    /*  //CURL

        // abrimos la sesión cURL
        $ch = curl_init();

        // definimos la URL a la que hacemos la petición
        curl_setopt($ch, CURLOPT_URL,$destino);
        if ($tipo == 'POST'){
            // indicamos el tipo de petición: POST
            curl_setopt($ch, CURLOPT_POST, TRUE);
            // definimos cada uno de los parámetros
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);//"postvar1=value1&postvar2=value2&postvar3=value3"
        }
        // recibimos la respuesta y la guardamos en una variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_server_output = curl_exec ($ch);
        // cerramos la sesión cURL
        curl_close ($ch);
        // hacemos lo que queramos con los datos recibidos
        // por ejemplo, los mostramos
        return $remote_server_output;
    */
}


//REVISAR!!!!! 2018-02-20
function token($accion, $key){
    global $version;
    $db = getConnection();
    $mensaje = '';
    $resultado = '';

    if ($accion == 'GETTOKEN' && $key != ""){

        $fecha = new DateTime();
        $timestamp1 = $fecha->getTimestamp();
        //echo "Ahora: ".$timestamp1;
        $timestamp2 = $timestamp1 + (60 * 10);

        //genera el token y guardarlo en bbdd
        $token = md5($timestamp1);

        //Existe en BBDD???
        $data = $db->select("_TOKEN", ["enable_to"], ["token" => $token]);
        if (count($data)==1){
            //ya existe: UPDATE
            $db->update("_TOKEN", ["enable_to"=>$timestamp2],["token"=>$token]);
        }else{
            //INSERT
            $posicion = $db->insert("_TOKEN", [
                "token" => $token,
                "type" => "TEST",
                "date" => $fecha->format('Y-m-d H:i:s'),
                "enable_to" => $timestamp2
            ]);
        }
        $mensaje = 'La validez del token es de 10 min.';
        $resultado = $token;

    }else if ($accion == 'ISVALIDTOKEN'){
        $resultado = esTokenValido($key);
        $mensaje = 'OK';
    }

    //RESPUESTA //////////////////////////////////////////////////////////////////////////////
    if ($accion != ''){
        $respuesta = array(
            'action'    =>$accion,
            'result'    =>$resultado,
            'message'   => utf8_encode($mensaje)
        );
        echo json_encode($respuesta);
    }else{
        echo 'Servicio TOKEN version '.$version;
    }
}

/**
 * Validar token:
 *-> Consultar a la bbdd si el token indicado existe
 *-> obtener la fecha de validez
 *-> comparar que sea mayor a la fecha actual
 * @param $token
 * @return bool
 */
function esTokenValido($token) {
    global $log;
    global $ws_base_path;

    try {
        //echo "Informacion recibida:::".$token;
        $resp = file_get_contents($ws_base_path."/service.php?value=".$token."&action=ISVALIDTOKEN");
        //$resp = file_get_contents($ws_base_path."/service.php?value=".$token."&action=ISVALIDTOKEN",false,$context);
        return json_decode($resp,true)['result'];
    } catch(PDOException $e) {
        $log->LogError("Validar token: ".$token." ERROR: ".$e->getMessage());
        return false;
    }
}

function myunserialize($str){
    global $log;
    $str = urldecode($str);     //decodificar parametros de URL a STRING
    $log->LogInfo("unserialize...:" . $str);
    $object = array();
    try {        
        $vars = explode("&", $str);
        $log->LogInfo("unserialize..." . sizeof($vars). " params");
        if ($vars != null) {
            for ($i = 0; $i < sizeof($vars); $i++) {
                $log->LogInfo("Pair:" . $vars[$i]);
                $pair = explode("=", $vars[$i]);
                $log->LogInfo("Pairs:" . sizeof($pair));
                if ($pair != null && sizeof($pair) > 1) {
                    $object[$pair[0]] = $pair[1];
                    $log->LogInfo($pair[0] . "/" . $object[$pair[0]]);
                }else{
                    $object = json_decode($vars[$i]);
                }
            }
        }

    }catch(Exception $e){
        $log->LogError("Unserialize params ERROR: ".$e->getMessage());
        return null;
    }
    return $object;
}

function procesar($cadena){
    return $cadena . "";
}

function formatDate($strfecha){
    global $log;
    $log->LogInfo("UNFORMAT DATE: " . $strfecha);
    $str = urldecode($strfecha);
    $log->LogInfo("FORMATED DATE: " . $str);

    $date = str_replace('/', '-', $str);
    $formated = date('Y-m-d', strtotime($date));
    $log->LogInfo("DATE WELL FORMED: " . $formated);
    return $formated;
}


function getMyID($token){
    global $log;
    global $ws_base_path;
   try {
        //echo "Informacion recibida:::".$token;
        $resp = file_get_contents($ws_base_path."/service.php?value=".$token."&action=GETUID");
        //$resp = file_get_contents($ws_base_path."/service.php?value=".$token."&action=ISVALIDTOKEN",false,$context);
        return json_decode($resp,true)['result'];
    } catch(PDOException $e) {
        $log->LogError("Obtener UID: ".$token." ERROR: ".$e->getMessage());
        return -1;
    }
}

?>