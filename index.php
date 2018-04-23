<?php

	$url = 'https://accounts.spotify.com/api/token';
    $method = 'POST';
    $spot_api_redirect = 'https://julianschreiner.de';

    $client_id = 'bd6615f1fa0e40b3aa324b5ee6a25a20';
    $client_secret = 'f33aaaf8ad794d09a850ae37cb6098c0';

    $credentials = "{$client_id}:{$client_secret}";

    $headers = array(
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: runscope/0.1",
            "Authorization: Basic " . base64_encode($credentials));

    $data = 'grant_type=client_credentials';

   
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

       // print_r($response['access_token']);


?>
<script type="text/javascript">
   var access_token = "<?php echo $response['access_token']; ?>";
</script>

<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
	<title>Newtist</title>
</head>
<body ng-app="myApp" ng-controller="myCtrl">
	<h2>Newtist</h2>
    <div class="row">
    <form class="col s12">
      <div class="row">
        <div class="input-field col s6">
	       <p class="flow-text">Artist:</p>
	       <input type="text" name="artist-search">
	       <button type="button" name="artist-submit" class="waves-effect waves-light btn">GET</button>
        </div>
        </div>  
    </form>
</div>

    <div class="row" ng-show="artist_name.length > 0">
    <div class="col s12 m6">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
            <div class="card-image">
            <img src="{{ artist_image }}">
          <span class="card-title">{{artist_name}}</span>
            </div>
          <ul>
            <li>ID: {{ artist_id }}</li>
            <li>Popularity: {{ artist_pop }}</li>
            <li>Followers:{{ artist_followers }}</li>
          </ul>
        </div>
        <div class="card-action">
          <a href="#">SPOTIFY URI</a>
        </div>
      </div>
    </div>
  </div>

</body>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="app.js"></script>

</html>