<?php

error_reporting(E_ALL);

$dbHostName = "sql107.epizy.com";
$dbUser = "epiz_26115994";
$dbPassword = "FJP0HtSnTXw";
$dbName = "epiz_26115994_awsDevAsoc";
$dbCon = NULL;
$response = array("success" => array(), "error" => array());

function initDBConnection(){
    global $dbHostName, $dbUser, $dbPassword, $dbName, $dbCon;
    for($i=0; $i<3; $i++){
        $dbCon = new mysqli($dbHostName,$dbUser,$dbPassword,$dbname);
        if($dbCon->connect_errno === 0){
            return TRUE;
        }
    }
    return FALSE;
}

function saveNewNote($note){
    global $dbCon, $response;
    $sql = "INSERT INTO epiz_26115994_awsDevAsoc.awsDevAsocNotes (note) VALUES (\"".$note."\")";
    if($dbCon->query($sql) === FALSE){
        array_push($response["error"], "SQL query error: ".$sql." ".$dbCon->error);
        return FALSE;
    }
    array_push($response["success"], "One row inserted");
    return TRUE;
}

function getRandomNote(){
    global $dbCon, $response;
    $sql = "SELECT MAX(id) AS 'count' FROM epiz_26115994_awsDevAsoc.awsDevAsocNotes";
    $result = $dbCon->query($sql);
    if($result === FALSE){
        array_push($response["error"], "SQL query error: ".$sql." ".$dbCon->error);
        return "error";
    }
    $row = $result->fetch_array();
    $count = (int)$row['count'];
    $id = rand(1, $count);
    $sql = "SELECT (note) FROM  epiz_26115994_awsDevAsoc.awsDevAsocNotes WHERE id = ".(string)$id;
    $result = $dbCon->query($sql);
    if($result === FALSE){
        array_push($response["error"], "SQL query error: ".$sql." ".$dbCon->error);
        return "error";
    }
    $row = $result->fetch_array();
    return $row["note"];
}

function getAllNotes(){
    global $dbCon, $response;

    $sql = "SELECT * FROM epiz_26115994_awsDevAsoc.awsDevAsocNotes ORDER BY id";
    $result = $dbCon->query($sql);
    if($result === FALSE){
        array_push($response["error"], "SQL query error: ".$sql." ".$dbCon->error);
        return "error";
    }
    if ($result->num_rows == 0){
        return "0 results";
    }
    $data = array();
    while($row = $result->fetch_assoc()) {
        array_push($data, ["id" => $row["id"], "note" => $row["note"]]);
    }
    return $data;
}

if(initDBConnection()){
    switch($_POST['action']){
        case "saveNewNote": saveNewNote($_POST["newNoteContent"]);
                            break;
        case "getRandomNote": array_push($response["success"], getRandomNote());
                              break;
        case "getAllNotes": array_push($response["success"], getAllNotes());
                            break;
        default: array_push($response["error"], "Invalid Action");
    }
    $dbCon->close();
}
else{
    array_push($response["error"], "Database connection error: ".$dbCon->connect_error);
}

echo json_encode($response);

?>