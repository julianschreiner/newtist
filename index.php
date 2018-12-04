<?php
session_start();

require_once('./init/ConnWorker.php');


$connWorker = new ConnWorker();
$connWorker->initializeApp();

$response = $connWorker->getResponse();


/*
$route = new Route();
$route::parse('/change', function($ret, $matches){
    var_dump($ret);
    var_dump($matches);
});
 */

?>

<script type="text/javascript">
    var access_token = "<?php echo $response['access_token']; ?>";
    var session_id = "<?php echo (isset($_SESSION['id']) ? $_SESSION['id'] : '') ?>";
</script>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content=" <strong>Newtist</strong> is a plattform to explore <strong>new music releases</strong> from your favourite artists on <strong>spotify!</strong>">
    <meta name="keywords" content="New Music, New Releases, Artist, Music, Spotify, Newtist, Discover new Music, ">
    <meta name="author" content="Julian Schreiner">
    <meta name="robots" content="INDEX, FOLLOW" />
   
    <link rel="shortcut icon" href="icon/images/compact_disc.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.2/angular.min.js"></script>
    
    <title>Newtist - Explore new Music on Spotify every friday for free!</title>
</head>
<body ng-app="myApp" ng-controller="myCtrl" ng-cloak >

<div class="container center">
    <div class="row">
        <div class="col s12 center">
            <div ng-view></div>
			<?php if (isset($_SESSION['id']) && (isset($_GET['reg']) && $_GET['reg'] == 'success')) : ?>

                <!-- <h6 id="userLogged">User logged in:</h6> -->
                
            <br>
	        <?php if ($_SESSION['id'] == 'root') : ?>
            <a href="admin/mngUsers.php"><div class="chip">
                       <img src="icon/avatar.png" alt="Contact Person">
						<?php echo $_SESSION['id']; ?>
                    </div>
            </a>
            <?php else : ?>
                    <div class="chip">
                        <img src="icon/avatar.png" alt="Contact Person">
						<?php echo $_SESSION['id']; ?>
                    </div>
            <?php endif; ?>



			<?php elseif (isset($_GET['reg']) && !isset($_SESSION['id']) && $_GET['reg'] == 'logdout') : ?>
                <h6 id="userLogged">Successfully logged out!</h6>
			<?php endif; ?>

            <h2 class='text-center' id="headlineNewtist">Newtist</h2>
            
			<!-- NOTIFICATION --> 
            <?php if (isset($_SESSION['id']) && (isset($_GET['reg']) && $_GET['reg'] == 'success')) : ?>
            <ul class="collapsible" data-collapsible="accordion" ng-if="notificationBarLoaded">
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
            <div class="headerMenu">
                <a href="#"><p class="left-align grey-text lighten-3">News</p></a>
                <a href="#" class="nthItemMenu"><p class="left-align grey-text lighten-3">Collections</p></a>
              
                <a href="auth/logout.php" class="rightMenu">
                    <p class="text-right grey-text lighten-3" id="loginRegister">Logout</p>
                </a>
               
            
            <?php else : ?>
            <div class="headerMenu">
                <a href="#" ng-click="enableNews();"><p class="left-align grey-text lighten-3">News</p></a>
                <a href="#" ng-click="enableCollections();" class="nthItemMenu"><p class="left-align grey-text lighten-3">Collections</p></a>

                <a href="auth/login.php" class="rightMenu">
                    <p class="text-right grey-text lighten-3" id="loginRegister">Login</p>
                </a>
			<?php endif; ?>
           
            
            
            </div>

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
                    <div class="autocomplete">
                        <input type="text" name="artist-search" id="artist-search"
                           placeholder="Search Artist e.g Gucci Mane"
                           ng-keypress="getHint();" ng-model="inp_search">
                    </div>
                    <button type="button" name="artist-submit" class="waves-effect waves-light btn" width="5">Search</button>
                    <button type="button" name="artist-back" class="waves-effect waves-light btn" ng-show="userSearched || showNews" width="5">Back</button>
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
                        <button type="button" name="filter-applied" class="waves-effect waves-light btn" width="5" ng-if="filter.selected != null" ng-click="getNewReleases();resFilter();">Reset Filter</button>
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
                        <h2>{{ ::carouselItems[0].name }} by <a href="" ng-click="goToUser(carouselItems[0].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[0].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[0].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item amber darken-2 white-text" href="#two!">
                    <img src="{{ carouselItems[1].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ ::carouselItems[1].name }} by <a href="" ng-click="goToUser(carouselItems[1].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[1].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[1].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item green white-text" href="#three!">
                    <img src="{{ carouselItems[2].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ ::carouselItems[2].name }} by <a href="" ng-click="goToUser(carouselItems[2].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[2].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[2].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
                <div class="carousel-item blue white-text" href="#four!">
                    <img src="{{ carouselItems[3].images[0].url }}" class="carouselImage">
                    <div class="container" id="carouselContainer">
                        <h2>{{ ::carouselItems[3].name }} by <a href="" ng-click="goToUser(carouselItems[3].artists[0].name);" class="carouselLink"><strong>{{ carouselItems[3].artists[0].name}}</strong></a></h2>
                        <a class="btn waves-effect white grey-text darken-text-2" href="{{ carouselItems[3].external_urls.spotify }}" target="_blank">LISTEN</a>
                    </div>
                </div>
            </div>
        </section>
    </div> <!-- row -->

    <div class="row" id="mainRow">
        <h4 class='text-center grey-text lighten-3' ng-if="!userSearched && !showNews" id="txtNewReleases">Newest Releases</h4>
        <h4 class='text-center grey-text lighten-3' ng-if="userSearched">Artist</h4>

        <div class="row" ng-if="!isLoading">
            <div class="col s12 m4 l3 myCards" ng-if="!userSearched && !showNews" ng-repeat="x in new_releases track by $index">
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

                <div class="card sticky-action" id="artistCard">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ x.images[0].url }}">
                    </div>
                    <div class="card-content" class="newRelCardBody">
            <span class="card-title  grey-text text-darken-4">
                {{ ::x.name | cut:true:textLimit:' ...' }} by
                <a href="" ng-click="goToUser(x.artists[0].name);">
                    <strong>{{ ::x.artists[0].name | cut:true:artistTextLimit:' ...' }}</strong>
                </a>
            </span>
                         <!-- <p>Artists:</p>
                        <ul>
                            <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
                        </ul> -->
                    </div>

                    <div class="card-action">
                        <a href="{{ x.artists[0].external_urls.spotify }}" target="_blank">Visit Artist</a><br>
                        <a href="{{ x.external_urls.spotify }}" target="_blank">Visit {{ x.album_type | cut:true:7}}</a>

                         <a ng-click="favor($event, x.name, x.artists[0].name);">
                            <i class="material-icons favor" id="favor_{{ x.name }}">
                                favorite_border
                            </i>
                        </a>

                        
                    
                    </div>

                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">{{ x.name }} by 
                            <strong>{{ x.artists[0].name}}</strong>
                            <i class="material-icons right">close</i>
                        </span>
                        <p>{{ x.artists[0].name }} released {{ x.name }} on {{ x.release_date }}</p>
                        <p>Artists:</p>
                        <ul>
                            <li ng-repeat="fa in x.artists">{{ fa.name }} </li>
                        </ul>
                        <p>Tracklist:</p>
                        <ul class="tracklist_reveal">
                            <li ng-repeat="tl in x.tracklist">{{ tl.name }}</li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- SUB MESSAGE -->
            <div class="row" id="alert_box" ng-if="userSearched">
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
            <div class="col s12 m4 l8 cards-container" ng-if="userSearched">
                <div class="card sticky-action" id="artistCard_INC">
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
						<?php if (isset($_SESSION['id'])) : ?>
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
            <h4 class='text-center grey-text lighten-3' ng-if="userSearched && new_rel_artist.length > 0">
                Recent Releases
            </h4>
        </div>

        <!-- RECENT RELEASES CARD -->
        <div class="col s12 m4 13 cards-container" ng-if="userSearched"
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
        <h4 class='text-center grey-text lighten-3' ng-if="userSearched">
            Top Tracks
        </h4>
    </div>
    <div class="row">
        <!-- RECENT RELEASES CARD -->
        <div class="col s12 m4 13 cards-container" ng-if="userSearched"
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

        <div class="row" id="mainRow" ng-if="showNews">
            <h4 class='text-center grey-text lighten-3'  id="txtNewReleases">News</h4>
            <div class="row" ng-if="!isLoading">
                
            </div>
        </div>

        <!-- FOOTER -->
   
        <footer class="page-footer" id="footer">
            <button type="button" name="load-more" ng-click="loadMore();" class="waves-effect waves-light btn" ng-if="!userSearched && !filterUsed && !showNews">
                Load more
            </button>
            <br><br>

           <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <h5 class="white-text">New music every friday!</h5>
                <p class="grey-text text-lighten-4">Make sure to sign up to get weekly emails about newest releases!</p>
              </div>
              <div class="col l4 offset-l2 s12">
                <h5 class="white-text">Links</h5>
                <ul>
                <li><a class="grey-text text-lighten-3" href="https://julianschreiner.de" target="_blank">Home</a></li>
                  <li><a class="grey-text text-lighten-3" href="/auth/register.php">Sign Up</a></li>
                  <li><a class="grey-text text-lighten-3" target="_blank" href="https://github.com/rlated1337/newtist">Github</a></li>
                  <li><a class="grey-text text-lighten-3" href="/static/about.html">About</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2018 Julian Schreiner
            </div>
          </div>

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
<script src="js/route.js"></script>

<link rel="stylesheet" href="css/main.css">

<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



</html>
