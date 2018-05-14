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
        By <a href="https://twitter.com/rlated1337" target="_blank">@Rlated</a>
      </h4>
      <form class="col s12">
      <div class="row input-field">
           <p class="flow-text">Search for an artist:</p>
           <input type="text" name="artist-search"
           placeholder="Search Artist e.g Gucci Mane">
           <button type="button" name="artist-submit" class="waves-effect waves-light btn" width="5">Search</button>
            <button type="button" name="artist-back" class="waves-effect waves-light btn" ng-show="userSearched" width="5">Back</button>
            <div class="input-field col s12" ng-show="!userSearched">
              <select class="ng-cloak" ng-if="init" ng-model="filter.selected" ng-change="filterApplied(filter.selected);">
                <option value="" disabled selected>Choose a filter</option>
                <option
                ng-repeat="y in filter" value="{{y}}">{{ y }}</option>
              </select>
              <label>Filter Genres</label>
						 <!-- <label>{{filter.selected}}</label> -->
            </div>
      </div>
    </form>
    </div>
  </div>
  <div class="row">
    <h4 class='text-center grey-text lighten-3' ng-show="!userSearched">Newest Releases</h4>
    <h4 class='text-center grey-text lighten-3' ng-show="userSearched">Artist</h4>

  <div class="row" ng-show="!isLoading">
    <div class="col s12 m4 l3" ng-show="!userSearched" ng-repeat="x in new_releases">
       <div class="card blue-grey darken-1" style="min-height: 39em; max-height: 39em;">
        <div class="card-image">
            <img src="{{ x.images[0].url }}">
            <span class="card-title">{{ x.name }} by {{ x.artists[0].name}} </span>
        </div>
         <div class="card-content white-text">
           <p>{{ x.artists[0].name }} released {{ x.name }} on {{ x.release_date }}</p>
           <br>
           <p>Artists:</p>
           <ul>
              <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
           </ul>
         </div>
         <div class="card-action">
           <a href="{{ x.artists[0].external_urls.spotify }}" target="_blank">Visit Artist</a><br>
           <a href="{{ x.external_urls.spotify }}" target="_blank">Visit {{ x.album_type }}</a>
         </div>
        </div>
    </div>
    <button type="button" name="load-more"
        class="waves-effect waves-light btn" ng-show="!userSearched">Load more</button>


    <!-- ARTIST CARD -->
    <div class="col s12 m4 l8 cards-container" ng-show="userSearched">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
            <div class="card-image">
            <img src="{{ artist_image }}">
          <span class="card-title"><strong>{{ artist_name }}</strong></span>
            </div>
          <ul>
            <li>Popularity: {{ artist_pop }}</li>
            <li>Followers: {{ artist_followers }}</li>
            <li ng-repeat="x in artist_genre">{{ x | capitalize  }} </li>
          </ul>
        </div>
        <div class="card-action">
          <a href="{{ artist_link }}" target="_blank">SPOTIFY URI</a>
        </div>
      </div>
    </div>
  <!-- CARD END -->
</div>
  <div class="row">
      <h4 class='text-center grey-text lighten-3' ng-show="userSearched && new_rel_artist.length > 0">
        Recent Releases
    </h4>
  </div>

    <!-- RECENT RELEASES CARD -->
    <div class="col s12 m4 13 cards-container" ng-show="userSearched"
     ng-repeat="x in new_rel_artist">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
            <div class="card-image">
            <img src="{{ x.images[0].url }}">
          <span class="card-title"><strong>{{ x.name }}</strong></span>
            </div>
          <ul>
            <li>Release Date: {{ x.release_date }}</li>
            <li>Type: {{ x.type | capitalize }}</li>
            <li ng-repeat="artists in x.artists">{{ artists.name | capitalize  }} </li>
          </ul>
        </div>
        <div class="card-action">
          <a href="{{ x.external_urls.spotify }}" target="_blank">SPOTIFY URL</a>
        </div>
      </div>
    </div>
  <!-- CARD END -->
  </div>
  <div class="row">
      <h4 class='text-center grey-text lighten-3' ng-show="userSearched">
       Top Tracks
    </h4>
 </div>
 <div class="row">
  <!-- RECENT RELEASES CARD -->
    <div class="col s12 m4 13 cards-container" ng-show="userSearched"
     ng-repeat="track in artist_top_tracks">
      <div class="card blue-grey darken-1" style="min-height: 42em; max-height:42em;">
        <div class="card-content white-text">
            <div class="card-image">
            <img src="{{ track.album.images[0].url }}">
          <span class="card-title"><strong>{{ track.name }}</strong></span>
            </div>
          <ul>
            <li>Release Date: {{ track.album.release_date }}</li>
            <li>Duration: {{ track.duration_ms | millSecondsToTimeString }}</li>
            <li>Popularity: <b>{{ track.popularity }}</strong></li>
            <li ng-repeat="artist in track.artists">{{ artist.name | capitalize  }} </li>
          </ul>
        </div>
        <div class="card-action">
          <a href="{{ track.external_urls.spotify }}" target="_blank">SPOTIFY URL</a>
        </div>
      </div>
    </div>
  <!-- CARD END -->

</div>


</body>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
    <link rel="stylesheet" href="css/main.css">

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>



</html>
