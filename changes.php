<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 13.08.2018
 * Time: 14:48
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = 'https://api.github.com/repos/rlated1337/newtist/branches';

$headers = array(
	"Accept: */*",
	"Content-Type: application/x-www-form-urlencoded",
	"User-Agent: runscope/0.1"
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$sha = $response[0]['commit']['sha'];
$url = 'https://api.github.com/repos/rlated1337/newtist/commits?per_page=100&sha=##PLACEHOLDER##';
$url = str_replace('##PLACEHOLDER##', $sha, $url);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

//echo "<pre>" . print_r($response, true) . "</pre>";

$ret = [];

foreach($response as $key => $item){
    $ret[$key]['sha'] = $item['sha'];
    $ret[$key]['message'] = $item['commit']['message'];
    $ret[$key]['committer'] = $item['commit']['committer'];
    $ret[$key]['html_url'] = $item['html_url'];
}

//echo "<pre>" . print_r($ret, true) . "</pre>";


?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Newtist</title>
</head>
<body>
<h3 class="flow-text">Last Commits</h3>
<a href="./"><i class="material-icons">backspace</i></a>
<table>
    <thead>
    <tr>
        <th>SHA</th>
        <th>Message</th>
        <th>COMMITTER</th>
        <th>Timestamp</th>
        <th>HTML_URL</th>

    </tr>
    </thead>
    <tbody>
	<?php foreach($ret as $results): ?>
        <tr>
            <td><?php echo $results['sha'] ?></td>
            <td><?php echo $results['message'] ?></td>
            <td><?php echo $results['committer']['name'] ?></td>
            <td><?php echo $results['committer']['date'] ?></td>
            <td><a href="<?php echo $results['html_url'] ?>" target="_blank">URL</a></td>
        </tr>
	<?php endforeach; ?>

    </tbody>
</table>
</body>
</html>