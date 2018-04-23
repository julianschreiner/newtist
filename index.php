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
   
<div class="container center">
  <div class="row">
    <div class="col s12 center">
      <h2 class='text-center'>Newtist</h2>
      <h4 class='text-center grey-text lighten-3'>
        By <a href="https://twitter.com/rlated1337">@Julian</a>
      </h4>
      <form class="col s12">
      <div class="row input-field">
           <p class="flow-text">Artist:</p>
           <input type="text" name="artist-search" 
           placeholder="Search Artist e.g Gucci Mane">
           <button type="button" name="artist-submit" class="waves-effect waves-light btn">Search</button>
        
      </div>  
    </form>
    </div>
  </div>
  <div class="row">
    <h4 class='text-center grey-text lighten-3'>Newest Releases</h4>
    <div class="col s12 m6 l3 cards-container center" ng-show="!userSearched">
       <div class="card blue-grey darken-1" ng-repeat="x in new_releases">
        <div class="card-image">
            <img src="{{ x.images[0].url }}">
            <span class="card-title">{{ x.name }} by {{ x.artists[0].name}} </span>
        </div>
         <div class="card-content white-text">
           <p>{{ x.artists[0].name }} released {{ x.name }} on {{ x.release_date }}</p>
         </div>
         <div class="card-action">
           <a href="{{ x.artists[0].external_urls.spotify }}" target="_blank">Visit Artist</a><br>
           <a href="{{ x.external_urls.spotify }}" target="_blank">Visit {{ x.album_type }}</a>
         </div>
        </div>
        <button type="button" name="artist-submit" 
        class="waves-effect waves-light btn">Load more</button>
    </div>

    <!-- CARD -->
    <div class="col s12 m4 l8 cards-container" ng-show="userSearched">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
            <div class="card-image">
            <img src="{{ artist_image }}">
          <span class="card-title"><strong>{{ artist_name }}</strong></span>
            </div>
          <ul>
            <li>ID: {{ artist_id }}</li>
            <li>Popularity: {{ artist_pop }}</li>
            <li>Followers: {{ artist_followers }}</li>
          </ul>
        </div>
        <div class="card-action">
          <a href="{{ artist_link }}" target="_blank">SPOTIFY URI</a>
        </div>
      </div>
  </div>
  <!-- CARD END -->
  </div>
</div>


</body>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <link rel="stylesheet" href="main.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="app.js"></script>

</html>