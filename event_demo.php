 <?php
date_default_timezone_set("Europe/Madrid");
header("Content-Type: text/event-stream\n\n");
header('Cache-Control: no-cache');

require_once '../../lib/KLogger.php';
$log = new KLogger ( "log_event.txt" , KLogger::DEBUG );

require_once '../../db/Medoo.php';
use Medoo\Medoo;

$_message = '{"time":"CURRENT_DATE", "event": EVENTOS }';
//FORMATO getMessage: {"ID":"27","FECHAHORA":"2019-02-27 12:05:17","MENSAJE":"XXXXXXXX"}
//FORMATO getTurnos: XXXXXXXXX
$timeTurnos = 120;
$timeSleep = 2;

//INICIAR TEMPORIZADOR para primera ejecucion
$counter = 1;
$log->Loginfo("INICIO");

while (1) {
  $log->Loginfo("WHILE");
  try{       
    /*
  	// get turnos every XX seconds
    $counter--;
  	if (!$counter) {    
      getTurnos();      
      $counter = $timeTurnos / $timeSleep;
  	}else{
      getMessage();               
    }	    	
  */
    getMessage();               
  }catch(Exception $e){
    $log->LogError("ERROR:::".$e->getMessage());
  }

  sleep($timeSleep);
}

function getMessage(){
    global $log;
    //$log->Loginfo("¿hay mensaje?:::");
    $message = file_get_contents('<URL_GET_MESSAGES>');    
    
    if (isset($message) && $message != ""){      
      send($message);      
      $log->LogInfo("HAY MENSAJE:::".$message);
    }else{
      //$log->LogError("NO");
    }
}

function getTurnos(){
    global $log;
    $log->LogInfo("Turnos init");
    $message = file_get_contents('<URL_GET_TURNOS>');    
    
    if (isset($message) && $message != ""){
      send($message);      
      $log->Loginfo("info:::".$message);
    }else{
      $log->LogError("---");
    }
}

function send($message){  
    global $log;
    $log->LogInfo("Enviado mensaje");
    echo 'data:'.$message."\n\n";    

    ob_flush();
    flush(); 
}
?>
