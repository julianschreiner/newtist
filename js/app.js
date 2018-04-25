console.log(access_token);

var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope) {
   /* API VARIABLES */
   $scope.artist_data = {};
   $scope.artist_id = '';
   $scope.artist_name = '';
   $scope.artist_pop = '';
   $scope.artist_followers = '';
   $scope.artist_image = '';
   $scope.artist_link = '';
   $scope.artist_genre = [];
   $scope.artist_top_tracks = [];

   /* USER VARIABLES */
   $scope.userLocation = '';

   /* USER INPUT VARIABLES */
   $scope.inp_search = '';
   $scope.filtered_search = '';


   /* NEW RELEASES */
   $scope.new_releases = [];

   /* NEW RELEASES ARTIST */
   $scope.new_rel_artist = [];


   /* STATE VARIABLES */
   $scope.userSearched = false;
   $scope.isLoading = true;
   $scope.searchReqLimit = 20;
   $scope.searchReqOffset = 0;


   //GET USER LOCATION
      $.get("https://ipinfo.io", function(response) {
        //console.log(response.city, response.country);
        $scope.userLocation = response.country;
    }, "jsonp");

  //GET NEW RELEASES
  $.ajax({
          url: 'https://api.spotify.com/v1/browse/new-releases?limit=' + $scope.searchReqLimit + '&offset=' + $scope.searchReqOffset,
          type: 'GET',
          headers: {
              'Authorization' : 'Bearer ' + access_token
          },
          success: function(data){
              //console.log(data);
              
              $scope.new_releases = data['albums']['items'];

              window.setTimeout(function(){
                  $scope.isLoading = false;
                  $scope.$apply();
              }, 500);

              $scope.$apply();
              
          },
          error: function(err){
            alert("cannot get newest releases");
            console.log(err);
          }
      }); //AJAX


   $("button[name = 'artist-submit']").click(function(e){
   $scope.inp_search = $("input[name = 'artist-search']").val();
   $scope.filtered_search =  $scope.inp_search.replace(' ', '%20');
   $scope.userSearched = true;
   $scope.$apply();


   /*SEARCH PROTOTYPE */
  $.ajax({
      url: 'https://api.spotify.com/v1/search?q='+$scope.filtered_search+'&type=artist&market=' + $scope.userLocation,
      type: 'GET',
      headers: {
          'Authorization' : 'Bearer ' + access_token
      },
      success: function(data) {
          //console.log(JSON.stringify(data));
          $scope.artist_data = JSON.stringify(data);
          
         // console.log(data);
        //  console.log(data['artists']['items'].length);

          for(var i = 0; i < data['artists']['items'].length; i++){
              if(data['artists']['items'][i]['name'] == $scope.inp_search){
                  $scope.artist_id = data['artists']['items'][i]['id'];
                  $scope.artist_name = data['artists']['items'][i]['name'];
                  $scope.artist_pop = data['artists']['items'][i]['popularity'];
                  $scope.artist_followers = data['artists']['items'][i]['followers']['total'];
                  $scope.artist_image = data['artists']['items'][i]['images'][0]['url'];
                  $scope.artist_link = data['artists']['items'][i]['external_urls']['spotify'];
                  $scope.artist_genre = data['artists']['items'][i]['genres'];

                  $scope.$apply();
                  break;
              }
          }

          $scope.getTopSongsData($scope.artist_id, access_token);

      },
      error: function(err){
        console.log(err);
        alert("cannot get artist id");
      }
  });  //AJAX 

  /*LOOK FOR NEWEST RELEASES OF THAT ARTIST*/
  angular.forEach($scope.new_releases, function(value, key){
    angular.forEach(value['artists'], function(value_2, key_2){
      if(value_2['name'] == $scope.inp_search){
          $scope.new_rel_artist.push($scope.new_releases[key]);
      } //IF
    }); //INNER FOREACH
  }); //OUTTER FOREACH
});    //BTN

   $scope.getTopSongsData = function(artist_id, token){
     if($scope.userLocation.length > 0){
      //RETRIEVE DATA
      $.ajax({
          url: 'https://api.spotify.com/v1/artists/' + artist_id + '/top-tracks?country=' + $scope.userLocation,
          type: 'GET',
          headers: {
              'Authorization' : 'Bearer ' + token
          },
          success: function(data){
            // TODO SHOW TOP TRACKS
              $scope.artist_top_tracks = data['tracks'];
              console.log($scope.artist_top_tracks);
              $scope.$apply();
              
          },
          error: function(err){
            alert("cannot get artist data");
            console.log(err);
          }
      }); //AJAX
      } //IF
   }; //FUNC

   //LOAD-MORE 
   $("button[name = 'load-more']").click(function(e){
      $scope.searchReqOffset += 20;
      $scope.isLoading = true;

      //GET NEW RELEASES
  $.ajax({
          url: 'https://api.spotify.com/v1/browse/new-releases?limit=' + $scope.searchReqLimit + '&offset=' + $scope.searchReqOffset,
          type: 'GET',
          headers: {
              'Authorization' : 'Bearer ' + access_token
          },
          success: function(data){
              //console.log(data);
              
              $scope.new_releases = data['albums']['items'];

              window.setTimeout(function(){
                  $scope.isLoading = false;
                  $scope.$apply();
              }, 500);

              $scope.$apply();
              
          },
          error: function(err){
            alert("cannot get newest releases");
            console.log(err);
          }
      }); //AJAX


   });

});    //ANG APP

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});

app.filter('millSecondsToTimeString', function() {
  return function(millseconds) {
    var oneSecond = 1000;
    var oneMinute = oneSecond * 60;
    var oneHour = oneMinute * 60;
    var oneDay = oneHour * 24;

    var seconds = Math.floor((millseconds % oneMinute) / oneSecond);
    var minutes = Math.floor((millseconds % oneHour) / oneMinute);
    var hours = Math.floor((millseconds % oneDay) / oneHour);
    var days = Math.floor(millseconds / oneDay);

    var timeString = '';
    if (days !== 0) {
        timeString += (days !== 1) ? (days + ' days ') : (days + ' day ');
    }
    if (hours !== 0) {
        timeString += (hours !== 1) ? (hours + ' hours ') : (hours + ' hour ');
    }
    if (minutes !== 0) {
        timeString += (minutes !== 1) ? (minutes + ' minutes ') : (minutes + ' minute ');
    }
    if (seconds !== 0 || millseconds < 1000) {
        timeString += (seconds !== 1) ? (seconds + ' seconds ') : (seconds + ' second ');
    }

    return timeString;
};
});


//TODO LOOK FOR NEWEST TRACKS WHERE NAME = ARTIST IN https://api.spotify.com/v1/browse/new-releases
//TODO FILTER GENRE
