<?php
require_once('../webservice/connection/db.php');

$dbCon = new db;
$dbCon = $dbCon->getLink();


const URL = "https://www.tmz.com/category/music/rss.xml";

$xmlDocument = simplexml_load_file(URL);


$counter = 0;
foreach($xmlDocument->channel->item as $item){
    $title = $item->title;
    $description = preg_replace('/<[^>]*>/', '', $item->description);
    $description = str_replace('Permalink', '', $description);
    $description = preg_replace('/\[[^)]+\]/','',$description);
    $link = $item->link;

    $handle = $dbCon->prepare('SELECT * FROM news WHERE title =:title AND link =:link');
    $handle->bindValue(':title', $title);
    $handle->bindValue(':link', $link);
    $handle->execute();
    $result = $handle->fetchAll(\PDO::FETCH_COLUMN);

    if(empty($result) || !isset($result)){
        $handle = $dbCon->prepare(
            "INSERT INTO news (title, description, link, timestamp) VALUES (:title, :description, :link, :time)"
        );

        $handle->bindValue(":title", $title);
        $handle->bindValue(":description", $description);
        $handle->bindValue(":link", $link);
        $handle->bindValue(":time", date("Y-m-d H:i:s"));
    
        $handle->execute();

        $counter++;
    
    }
}


echo "Entries made: " . $counter . PHP_EOL;
