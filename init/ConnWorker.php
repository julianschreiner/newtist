<?php

include('./webservice/connection/db.php');


class ConnWorker {

    const SPOTIFY_API_URL = 'https://accounts.spotify.com/api/token';
    const RES_METHOD = 'POST';
    const API_REDIRECT = 'https://julianschreiner.de';


    private $response;
    private $db_connection;



    public function __construct(){
        $dbInstance = new db();

        $this->db_connection = $dbInstance->getLink();
    }

    public function initializeApp(){
        $handle = $this->db_connection->prepare("select * from spotify_cred where id = ?");
        $handle->bindValue(1, 1, PDO::PARAM_INT);
        $handle->execute();
        $result = $handle->fetchAll(\PDO::FETCH_OBJ);

        $client_id = $result[0]->cl_id;
        $client_secret = $result[0]->cl_sec;

        $credentials = "{$client_id}:{$client_secret}";

        $headers = array(
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: runscope/0.1",
            "Authorization: Basic " . base64_encode($credentials)
        );

        $data = 'grant_type=client_credentials';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::SPOTIFY_API_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
       
        curl_close($ch);

        $this->setResponse($response);
    }

    private function setResponse(array $respon){
        $this->response = $respon;
    }

    public function getResponse(){
        return $this->response;
    }
};