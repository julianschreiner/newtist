<?php
session_start();
$url = 'https://accounts.spotify.com/api/token';
$method = 'POST';
$spot_api_redirect = 'https://julianschreiner.de';

$credFile = fopen("creds.ini", "r") or die("Unable to open file!");
$creds = fread($credFile,filesize("creds.ini"));

$username = strtok($creds, ':');

$password = strtok('');
$password = preg_replace('/\v(?:[\v\h]+)/', '', $password);

fclose($credFile);

$link = new \PDO('mysql:host=rlated12.lima-db.de;dbname=db_363124_3;charset=utf8mb4',
	(string)$username,
	(string)$password,
	array(
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_PERSISTENT => false)
);


$handle = $link->prepare("select * from spotify_cred where id = ?");
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

//print_r($response['access_token']);
?>

<script type="text/javascript">
    var access_token = "<?php echo $response['access_token']; ?>";
    var session_id = "<?php echo (isset($_SESSION['id']) ? $_SESSION['id'] : '')  ?>";
</script>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <title>Newtist</title>
</head>
<body ng-app="myApp" ng-controller="myCtrl" ng-cloak>

<div class="container center">
    <div class="row">

        <div class="col s12 center">
			<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])) : ?>
                <!-- <h6 id="userLogged">User logged in:</h6> -->
            <br>
	        <?php if ($_SESSION['id'] == 'root') : ?>
            <a href="admin/mngUsers.php"><div class="chip">
                       <img src="icon/avatar.png" alt="Contact Person">
						<?php echo $_SESSION['id']; ?>
                    </div>
            </a>
            <?php else: ?>
                    <div class="chip">
                        <img src="icon/avatar.png" alt="Contact Person">
						<?php echo $_SESSION['id']; ?>
                    </div>
            <?php endif; ?>



			<?php elseif(isset($_GET['reg']) && !isset($_SESSION['id']) && $_GET['reg'] == 'logdout'): ?>
                <h6 id="userLogged">Successfully logged out!</h6>
			<?php endif; ?>

            <h2 class='text-center' id="headlineNewtist">Newtist</h2>
			<!-- NOTIFICATION --> 
            <?php if (isset($_SESSION['id']) || (isset($_GET['reg']) && $_GET['reg'] == 'success')) : ?>
                <ul class="collapsible" data-collapsible="accordion" ng-show="notificationBarLoaded">
              <li>
                <div class="collapsible-header">
                  <i class="material-icons">notification_important</i>
                  Notifications
                  <span class="new badge">{{ notificationCounter }}</span></div>
                  <div class="collapsible-body">

                      <!-- <div class="collapsible" ng-repeat="x in artistPool">
                          <img class="notificationImage card-image waves-effect waves-light hoverable" width="650" src="{{x.image}}">
                          <p class="nftext">{{ x.album }} 
                            by <strong>{{ x.artist }}</strong>
                          </p>
                      </div> -->
                      <div ng-repeat="x in artistPool">
                          <div class="chip diffChip">
                              <img src="{{ x.image }}" alt="Contact Person">
                              {{ x.album | cut:true:textLimit:' ...' }} by
                              <a href="" ng-click="goToUser(x.artist);">
                                  <strong>{{ x.artist}}</strong>
                              </a>
                          </div>
                      </div>




                      <p class="nftext" ng-if="notificationCounter == 0">
                          Nothing here.
                      </p>


                  </div>
              </li>
            </ul>
                <a href="auth/logout.php">
                    <p class="text-right grey-text lighten-3" id="loginRegister">Logout</p>
                </a>
			<?php else : ?>
                <a href="auth/login.php">
                    <p class="text-right grey-text lighten-3" id="loginRegister">Login</p>
                </a>
			<?php endif; ?>

            <!-- <p class="text-right grey-text lighten-3" id="loginRegister">Register</p> -->

            <!-- Switch -->
            <div class="switch">
                <label>
                    Whitemode
                    <input type="checkbox">
                    <span class="lever"></span>
                    Darkmode
                </label>
            </div>


            <!--      <h4 class='text-center grey-text lighten-3'>
					By <a href="https://twitter.com/rlated1337" target="_blank">@Rlated</a>
				  </h4> -->
            <form class="col s12">
                <div class="row input-field">
                    <p class="flow-text" id="txtSearchArtist">Search for an artist</p>
                    <input type="text" name="artist-search"
                           placeholder="Search Artist e.g Gucci Mane">
                    <button type="button" name="artist-submit" class="waves-effect waves-light btn" width="5">Search</button>
                    <button type="button" name="artist-back" class="waves-effect waves-light btn" ng-show="userSearched" width="5">Back</button>
                    <div class="row">
                    </div>
                    <div class="input-field col s12" ng-show="!userSearched && filterLoaded">
                        <select id="filter" class="ng-cloak" ng-if="init" ng-model="filter.selected" ng-change="filterApplied(filter.selected);">
                            <option value="" disabled selected>Choose a filter</option>
                            <option
                                    ng-repeat="y in filter" value="{{y}}">{{ y }}</option>
                        </select>
                        <label>Filter Genres</label>
                        <!-- <label>{{filter.selected}}</label> -->
                        <button type="button" name="filter-applied" class="waves-effect waves-light btn" width="5" ng-show="filter.selected != null" ng-click="getNewReleases();resFilter();">Reset Filter</button>
                    </div>
                    <div class="progress" ng-show="filterLoaded == false" id="loadingBar">
                        <div class="indeterminate"></div>
                    </div><!-- progress -->
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <section class="black">
            <div class="carousel carousel-slider" data-indicators="true">
                <!-- <div class="carousel-fixed-item ">
				 <div class="container">
				  <h1 class="white-text">Highlights</h1>
				  <a class="btn waves-effect white grey-text darken-text-2" href="https://codepen.io/collection/nbBqgY/" target="_blank">button</a>
				</div>
			  </div> -->
                <div class="carousel-item teal lighten-2 white-text" href="#one!">
                    <div class="container" id="carouselContainer">
                        <h1 class="white-text center" id="featuredTextCar">Featured</h1>
                    </div>
                </div>
                <div class="carousel-item red lighten-2 white-text" href="#one!">
                    <img src="{{ carouselItems[0].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ carouselItems[0].name }} by <a href="" ng-click="goToUser(carouselItems[0].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[0].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[0].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item amber darken-2 white-text" href="#two!">
                    <img src="{{ carouselItems[1].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ carouselItems[1].name }} by <a href="" ng-click="goToUser(carouselItems[1].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[1].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[1].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item green white-text" href="#three!">
                    <img src="{{ carouselItems[2].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ carouselItems[2].name }} by <a href="" ng-click="goToUser(carouselItems[2].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[2].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[2].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item blue white-text" href="#four!">
                    <img src="{{ carouselItems[3].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ carouselItems[3].name }} by <a href="" ng-click="goToUser(carouselItems[3].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[3].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[3].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
            </div>
        </section>
    </div> <!-- row -->

    <div class="row" id="mainRow">
        <h4 class='text-center grey-text lighten-3' ng-show="!userSearched" id="txtNewReleases">Newest Releases</h4>
        <h4 class='text-center grey-text lighten-3' ng-show="userSearched">Artist</h4>

        <div class="row" ng-show="!isLoading">
            <div class="col s12 m4 l3 myCards" ng-show="!userSearched" ng-repeat="x in new_releases track by $index">
                <!--
					   <div class="card blue-grey darken-1" style="min-height: 39em; max-height: 39em;">
						<div class="card-image">
							<img src="{{ x.images[0].url }}">
							<span class="card-title">{{ x.name }} by <strong>{{ x.artists[0].name}}</strong> </span>
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
						 -->

                <div class="card sticky-action">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ x.images[0].url }}">
                    </div>
                    <div class="card-content" class="newRelCardBody">
            <span class="card-title  grey-text text-darken-4">
                {{ x.name | cut:true:35:' ...' }} by
                <a href="" ng-click="goToUser(x.artists[0].name);">
                    <strong>{{ x.artists[0].name }}</strong>
                </a>
            </span>
                         <!-- <p>Artists:</p>
                        <ul>
                            <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
                        </ul> -->
                    </div>

                    <div class="card-action">
                        <a href="{{ x.artists[0].external_urls.spotify }}" target="_blank">Visit Artist</a><br>
                        <a href="{{ x.external_urls.spotify }}" target="_blank">Visit {{ x.album_type }}</a>
                    </div>

                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">{{ x.name }} by <strong>{{ x.artists[0].name}}</strong><i class="material-icons right">close</i></span>
                        <p>{{ x.artists[0].name }} released {{ x.name }} on {{ x.release_date }}</p>
                        <p>Artists:</p>
                        <ul>
                            <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- SUB MESSAGE -->
            <div class="row" id="alert_box" ng-show="userSearched">
                <div class="col s12 m12" id="colBox">
                    <div class="card green darken-1" id="alertCard">
                        <div class="row">
                            <div class="col s12 m10">
                                <div class="card-content white-text">
                                    <p id="alert_msg">{{ subMessage }}</p>
                                </div>
                            </div>
                            <div class="col s12 m2">
                                <i class="fa fa-times icon_style" id="alert_close" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- ARTIST CARD -->
            <div class="col s12 m4 l8 cards-container" ng-show="userSearched">
                <div class="card sticky-action" id="artistCard">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ artist_image  }}">
                    </div>
                    <div class="card-content">
                        <span class="card-title"><strong>{{ artist_name }}</strong></span>

                        <ul>
                            <li>Popularity: {{ artist_pop }}</li>
                            <li>Followers: {{ artist_followers }}</li>
                            <li ng-repeat="x in artist_genres">
                                {{ x | capitalize }}
                            </li>
                        </ul>
                    </div>

                    <div class="card-action" ng-init="userID='<?php echo (isset($_SESSION['id']) ? $_SESSION['id'] : "") ?>'">
						<?php if (isset($_SESSION['id'])): ?>
                            <a href="" ng-click="subscribe(artist_name);" onclick="scrollToMessageSub();" target="_blank" ng-if="!isSubd">Subscribe</a>
                            <a href="" ng-click="unsubscribe(artist_name);" onclick="scrollToMessageSub();" target="_blank" ng-if="isSubd">Unsubscribe</a>
						<?php endif; ?>
                        <a href="{{ artist_link }}" target="_blank">Visit Artist</a>
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
            <!-- <div class="card blue-grey darken-1">
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
		   -->

            <div class="card sticky-action" id="recentReleases">
                <div class="card-image waves-effect waves-block waves-light">
                    <img class="activator" src="{{ x.images[0].url }}">
                </div>
                <div class="card-content">
                    <span class="card-title  grey-text text-darken-4">{{ x.name }} by <a href="" ng-click="goToUser(x.artists[0].name);"><strong>{{ x.artists[0].name }}</strong></a> </span>

                    <!-- <p>Artists:</p> -->
                    <ul>
                        <li>Release Date: {{ x.release_date }}</li>
                        <li>Type: {{ x.type | capitalize }}</li>
                        <li ng-repeat="artists in x.artists">{{ artists.name | capitalize  }} </li>
                    </ul>
                </div>

                <div class="card-action">
                    <a href="{{ x.external_urls.spotify }}" target="_blank">Listen</a>
                </div>

                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4">{{ x.name }} by <strong>{{ x.artists[0].name}}</strong><i class="material-icons right">close</i></span>
                    <p>{{ x.artists[0].name }} released {{ x.name }} on {{ x.release_date }}</p>
                    <p>Artists:</p>
                    <ul>
                        <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
                    </ul>
                    <ul>
                        <li>Release Date: {{ x.release_date }}</li>
                        <li>Type: {{ x.type | capitalize }}</li>
                    </ul>
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
            <!--
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
				-->
            <div class="card sticky-action">
                <div class="card-image waves-effect waves-block waves-light">
                    <img class="activator" src="{{ track.album.images[0].url }}">
                </div>
                <div class="card-content" class="newRelCardBody">
            <span class="card-title  grey-text text-darken-4">
                {{ track.name | cut:true:35:' ...' }} by
                <a href="" ng-click="goToUser(x.artists[0].name);">
                    <strong>{{ track.artists[0].name }}</strong>
                </a>
            </span>

                   <!--  <p>Artists:</p>
                    <ul>
                        <li ng-repeat="fa in track.artists">{{ fa.name }} </li>
                    </ul> -->
                </div>

                <div class="card-action">
                    <a href="{{ track.external_urls.spotify }}" target="_blank">Listen</a>
                </div>

                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4">{{ track.name }} by <strong>{{ track.artists[0].name}}</strong><i class="material-icons right">close</i></span>
                    <p>{{ track.artists[0].name }} released {{ track.name }} on {{ track.release_date }}</p>
                    <p>Artists:</p>
                    <ul>
                        <li>Release Date: {{ track.album.release_date }}</li>
                        <li>Duration: {{ track.duration_ms | millSecondsToTimeString }}</li>
                        <li>Popularity: <b>{{ track.popularity }}</strong></li>
                        <li ng-repeat="artist in track.artists">{{ artist.name | capitalize  }} </li>
                    </ul>
                </div>
            </div>

        </div>
        <!-- CARD END -->

        <!-- FOOTER -->
        <footer class="page-footer" id="footer">
            <button type="button" name="load-more" class="waves-effect waves-light btn" ng-show="!userSearched">
                Load more
            </button>

        </footer>


        <div class="fixed-action-btn">
            <a class="btn-floating btn-large red" onclick="scrollToTop();">
                <i class="large material-icons" style="background-color: #26a69a;">navigation</i>
            </a>
        </div>



    </div>


</body>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="js/ui.js"></script>
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">

<script src="js/app.js"></script>

<link rel="stylesheet" href="css/main.scss">

<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



</html>
