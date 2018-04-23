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

   /* USER VARIABLES */
   $scope.userLocation = '';

   /* NEW RELEASES */
   $scope.new_releases = [];

   /* STATE VARIABLES */
   $scope.userSearched = false;

   //GET USER LOCATION
      $.get("https://ipinfo.io", function(response) {
        console.log(response.city, response.country);
        $scope.userLocation = response.country;
    }, "jsonp");

  //GET NEW RELEASES
  $.ajax({
          url: 'https://api.spotify.com/v1/browse/new-releases',
          type: 'GET',
          headers: {
              'Authorization' : 'Bearer ' + access_token
          },
          success: function(data){
              console.log(data);
              
              $scope.new_releases = data['albums']['items'];
              $scope.$apply();
              
          },
          error: function(err){
            alert("cannot get artist data");
            console.log(err);
          }
      }); //AJAX


   $("button[name = 'artist-submit']").click(function(e){
   var inp_search = $("input[name = 'artist-search']").val();
   var filtered_search =  inp_search.replace(' ', '%20');
   $scope.userSearched = true;


   /*SEARCH PROTOTYPE */
  $.ajax({
      url: 'https://api.spotify.com/v1/search?q='+filtered_search+'&type=artist',
      type: 'GET',
      headers: {
          'Authorization' : 'Bearer ' + access_token
      },
      success: function(data) {
          //console.log(JSON.stringify(data));
          $scope.artist_data = JSON.stringify(data);
          
          
          console.log(data);
          console.log(data['artists']['items'].length);
          

          for(var i = 0; i < data['artists']['items'].length; i++){
              if(data['artists']['items'][i]['name'] == inp_search){
                  $scope.artist_id = data['artists']['items'][i]['id'];
                  $scope.artist_name = data['artists']['items'][i]['name'];
                  $scope.artist_pop = data['artists']['items'][i]['popularity'];
                  $scope.artist_followers = data['artists']['items'][i]['followers']['total'];
                  $scope.artist_image = data['artists']['items'][i]['images'][0]['url'];
                  $scope.artist_link = data['artists']['items'][i]['external_urls']['spotify'];

                  $scope.$apply();
                  break;
              }
          }

          $scope.getSongData($scope.artist_id, access_token);

      },
      error: function(err){
        console.log(err);
        alert("cannot get artist id");
      }
  });  //AJAX 
});    //BTN

   $scope.getSongData = function(artist_id, token){
     if($scope.userLocation.length > 0){
      //RETRIEVE DATA
      $.ajax({
          url: 'https://api.spotify.com/v1/artists/' + artist_id + '/top-tracks?country=' + $scope.userLocation,
          type: 'GET',
          headers: {
              'Authorization' : 'Bearer ' + token
          },
          success: function(data){
              console.log(data);
          },
          error: function(err){
            alert("cannot get artist data");
            console.log(err);
          }
      }); //AJAX
      }
   }; //FUNC

});    //ANG APP



//TODO LOOK FOR NEWEST TRACKS WHERE NAME = ARTIST IN https://api.spotify.com/v1/browse/new-releases
//TODO FILTER GENRE
