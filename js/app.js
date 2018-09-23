    var app = angular.module('myApp', []);


    app.controller('myCtrl', function($scope, $timeout, $http, $httpParamSerializer, $window) {
        /* API VARIABLES */
        /* ARTIST     */
        $scope.artist_data = {};
        $scope.artist_id = '';
        $scope.artist_name = '';
        $scope.artist_pop = '';
        $scope.artist_followers = '';
        $scope.artist_image = '';
        $scope.artist_link = '';
        $scope.artist_genre = [];
        $scope.artist_top_tracks = [];
        $scope.artist_genres = [];

        /* USER VARIABLES */
        $scope.userLocation = '';

        /* USER INPUT VARIABLES */
        $scope.inp_search = '';
        $scope.filtered_search = '';

        /* NEW RELEASES */
        $scope.new_releases = [];

        /* NEW RELEASES ARTIST */
        $scope.new_rel_artist = [];

        /*FILTER ARTIST */
        $scope.filterArtistName = [];
        $scope.filterUsed = false;

        /* STATE VARIABLES */
        $scope.userSearched = false;
        $scope.isLoading = true;
        $scope.searchReqLimit = 50;
        $scope.searchReqOffset = 0;
        $scope.init = false;

        /* FILTER */
        $scope.filter = [];
        $scope.filterLoaded = false;

        /* CAROUSEL */
        $scope.carouselItems = [];
        $scope.carouselLoaded = false;

        $scope.fetchedData = [];

        /* FILTER REWORK TESTING */
        $scope.categories = [];

        /* SUBSCRIPTION */
        $scope.isSubd = false;
        $scope.subMessage = '';
        $scope.notificationBarLoaded = false;
        $scope.notificationCounter = 0;

        /* SUB BAR */
        $scope.textLimit = 0;      // 15 mobile - 35 Desktop

        /* SUGGESTIONS */
        $scope.hintUsed = false;

        /* ONLOAD FUNCTION TO FIX CSS ERROR BECAUSE TEXT LENGTHS */
        angular.element($window).on('load', function () {
            if(parseInt($window.innerWidth) < 1300 && parseInt($window.innerWidth) > 996){
                $scope.textLimit = 20;
                $scope.$apply();
            }
        });

        angular.element($window).on('resize', function () {
            //console.log(parseInt($window.innerWidth));
            if(parseInt($window.innerWidth) < 1300 && parseInt($window.innerWidth) > 996){
                $scope.textLimit = 20;
                $scope.$apply();
            }
        });

        if (navigator.userAgent.match(/Tablet|iPad/i))
        {
            console.log("tablet!");
            $scope.textLimit = 25;
        } else if(navigator.userAgent.match(/Mobile|Windows Phone|Lumia|Android|webOS|iPhone|iPod|Blackberry|PlayBook|BB10|Opera Mini|\bCrMo\/|Opera Mobi/i) )
        {
         console.log("mobile!");
         $scope.textLimit = 15;
     } else {
        $scope.textLimit = 30;
        console.log("desktop!");
    }

    console.log($scope.textLimit);

    /* USER */
    $scope.userID = session_id;

        /*
         * * * * * * * * * * * * * *
         * * * * * * * * * * * * * *
         * * * * * * * * * * * * * *
         * * * * METHODS START * * *
         * * * * * * * * * * * * * *
         * * * * * * * * * * * * * *
         */

         var json = '{"frouter": {"apikey": "1234", "method": "genre"	} }';
         obj = JSON.parse(json);

         $http({
            url: './webservice/frouter.php?f=route',
            headers: { 'Content-Type': 'application/json;charset=utf-8' },
            method: "POST",
            data: { obj }
        })
         .then(function(response) {
                    // success
                    $scope.categories = response.data;
                    //console.log($scope.categories);
                },
                function(response) { // optional
                    // failed
                    //console.log(response);
                });

         $scope.getArtistGenre = function(artist_name){
            angular.forEach($scope.categories, function(element, index) {
                // statements
                if(element.name == artist_name){
                    let sepeprate = element.genre.split(',');
                    $scope.artist_genres = sepeprate;
                }
            });
        };

        $scope.getReleasesInc = function(artistName = null, quLimit = 50, quOffset = 0){
            //GET NEW RELEASES helper function for filtering
            $.ajax({
                url: 'https://api.spotify.com/v1/browse/new-releases?limit=' + quLimit + '&offset=' + quOffset,
                type: 'GET',
                headers: {
                    'Authorization' : 'Bearer ' + access_token
                },
                success: function(data){
                    //  console.log(data);
                    if(artistName == null || artistName.length == 0){
                        $scope.fetchedData.push(data['albums']['items']);
                        //    angular.forEach(data['albums']['items'], function(key, value){
                        //          $scope.fetchedData.push(key);
                        //      });
                        // $scope.getCarouselItems($scope.new_releases);
                    }
                },
                error: function(err){
                    alert("cannot get newest releases in inc func");
                    //console.log(err);
                }
            }); //AJAX
        };  //FUNC

        var i;
        var j = 50;

        for(i = 0; i < 3; i++){
            $scope.getReleasesInc(null, 50, j);
            j+=50;
        }

        setTimeout(function(){
            //console.log($scope.fetchedData);
        }, 3000);


        $scope.getFilter = function(){
            $.ajax({
                url: 'https://api.spotify.com/v1/recommendations/available-genre-seeds',
                type: 'GET',
                headers: {
                    'Authorization' : 'Bearer ' + access_token
                },
                success: function(data){
                    angular.forEach(data.genres, function(key, value){
                        //console.log(key);
                        $scope.filter.push(key);

                    });
                }
            });  //AJAX
            $scope.init = true;

            /*SELECT*/
            setTimeout(function(){
                $('select').formSelect();
                $scope.filterLoaded = true;
                $scope.$apply();
            },1000);
        };  // FUNC

        $scope.filterApplied = function(selectedFilter){
            $scope.filterUsed = true;
            $scope.filterArtistName = [];

            if(selectedFilter != null){
                selectedFilter = selectedFilter.replace('-', ' ');

                //console.log("Selected Filter: ", selectedFilter);

                //push name where name == filter to $scope.filterArtistName
                //maybe change .includes method and explode key.genre in array and then look into that array

                angular.forEach($scope.categories, function(key, value){
                    if(key.genre.includes(selectedFilter)){
                        $scope.filterArtistName.push(key.name);
                    }
                }); // FOREACH

                if($scope.filterArtistName.length != 0){
                    $scope.getNewReleases($scope.filterArtistName, true);
                }
                else{
                    /* TODO DISPLAY MESSAGE THAT NOTHING COULD BE FOUND */ 
                    $scope.getNewReleases();
                }

            } //FILTER != NULL
        }; // FUNC


        $scope.getFilter();



        /* CAROUSEL ITEMS */
        $scope.getCarouselItems = function(releases, limit = 4){
            $scope.carouselItems = releases.splice(0, limit);
            //console.log($scope.carouselItems);
            $scope.carouselLoaded = true;
            $scope.$apply();
        };

        
            //GET USER LOCATION
            $.get("https://ipinfo.io", function(response) {
                //console.log(response.city, response.country);
                $scope.userLocation = response.country;
            }, "jsonp");   
    
    

        $scope.getNewReleases = function(artistName = null, endless = false){
            //GET NEW RELEASES
            $scope.isLoading = true;
            $scope.new_releases = [];
            var foundsmth = false;

            // TODO: WHEN FILTERING FOR USERS MAKE IT SHOW ALL ENTRIES AND NOT ONLY 20 (SERACHREQLIMIT)

            $.ajax({
                url: 'https://api.spotify.com/v1/browse/new-releases?limit=' + $scope.searchReqLimit + '&offset=' + $scope.searchReqOffset,
                type: 'GET',
                headers: {
                    'Authorization' : 'Bearer ' + access_token
                },
                success: function(data){
                    //  console.log(data);

                    if(artistName == null || artistName.length == 0){
                        $scope.new_releases = data['albums']['items'];
                        $scope.getCarouselItems($scope.new_releases);
                    }
                    else{
                        //ONLY SAVE ALBUMS WHERE GIVEN ARTIST NAME IS IN THERE
                        angular.forEach(data['albums']['items'], function(key, value){
                            angular.forEach(key.artists, function(key2, value){
                                if(artistName.indexOf(key2.name) > -1){
                                    $scope.new_releases.push(key);
                                    foundsmth = true;
                                }
                            });  //FOREACH
                        });  //FOREACH

                        if(foundsmth){
                            // TODO: SHOW EVERYTHING
                            // $scope.searchReqOffset += 50;
                            //console.log($scope.new_releases);
                            //$scope.getNewReleases(artistName);
                        }  //IF
                    } // IF / ELSE


                    window.setTimeout(function(){
                        $scope.isLoading = false;
                        $scope.getNotification();
                        $scope.getTracklist();
                        $scope.$apply();
                    }, 500);
                },
                error: function(err){
                    alert("cannot get newest releases");
                    //console.log(err);
                }
            }); //AJAX
        };  //FUNC

        $scope.getNewReleases();

        $scope.getTracklist = function(){
            $scope.isLoading = true;
            angular.forEach($scope.new_releases, function(key, value){
                let albumID = key.id;
                $.ajax({
                    url: 'https://api.spotify.com/v1/albums/'+albumID+'/tracks',
                    type: 'GET',
                    headers: {
                        'Authorization' : 'Bearer ' + access_token
                    },
                    success: function(data){
                        //console.log(data);
                        key.tracklist = data.items;
                    }
                });  //AJAX
                //console.log(key.id);
            }); // FOREACH

            window.setTimeout(function(){
                    $scope.isLoading = false;
                    //console.log($scope.new_releases);
                    $scope.$apply();
                    }, 500);
        };

        $scope.getNotification = function(){
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

            /* CALL TO BACKEND FOR NOTIFICATION UPDATES */
            var json = '{"frouter": {"apikey": "1234", "method": "notificationHandle", "userID":  "' + $scope.userID + '" } }';
            obj = JSON.parse(json);

            $http({
                url: './webservice/frouter.php?f=route',
                headers: { 'Content-Type': 'application/json;charset=utf-8' },
                method: "POST",
                data: { obj }
            })
            .then(function(response) {
                        // success
                        var res = response;
                        $scope.artistPool = [];
                        $scope.allItemsComb = $scope.carouselItems.concat($scope.new_releases);

                        angular.forEach(res.data, function(value2, key2){
                            angular.forEach($scope.allItemsComb, function(value, key){
                                if(value.artists[0].name.indexOf(value2) > -1){
                                    var notificationInfo = {
                                        artist: value.artists[0].name,
                                        album: value.name,
                                        image: value.images[0].url
                                    };
                                    $scope.artistPool.push(notificationInfo);
                                        // TODO FINISH FRONTEND
                                        // TODO FINISH NOTIFICATION COUNTER
                                    }
                                });
                            }); //inner foreach
                        //console.log($scope.artistPool);
                        $scope.notificationBarLoaded = true;
                        $scope.notificationCounter = $scope.artistPool.length;
                    },
                    function(response) { // optional
                        // failed
                        //console.log(response);
                    });
        };

        $("button[name = 'artist-back']").click(function(e){
            $scope.getNewReleases();
            $scope.userSearched = false;
            $scope.resetURL();
        });


        $("button[name = 'artist-submit']").click(function(e){
            $scope.new_rel_artist = [];

            $scope.inp_search = $("input[name = 'artist-search']").val();

            if($scope.inp_search.includes('%20')){
                $scope.filtered_search =  $scope.inp_search.replace(' ', '%20');
            }
            else{
                $scope.filtered_search = $scope.inp_search;
            }

            $scope.buildURL($scope.filtered_search);

            $scope.userSearched = true;

            //$scope.$apply();


            /* SEARCH PROTOTYPE */
            $.ajax({
                url: 'https://api.spotify.com/v1/search?q='+$scope.filtered_search+'&type=artist&market=' + $scope.userLocation,
                type: 'GET',
                headers: {
                    'Authorization' : 'Bearer ' + access_token
                },
                success: function(data) {
                    $scope.isSub($scope.inp_search);
                    //console.log(JSON.stringify(data));
                    $scope.artist_data = JSON.stringify(data);

                    //   console.log(data);
                    //  console.log(data['artists']['items'].length);

                    for(var i = 0; i < data['artists']['items'].length; i++){
                        let artistName = data['artists']['items'][i]['name'];
                        if(artistName.toUpperCase().includes($scope.inp_search.toUpperCase())){
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
                    //console.log($scope.artist_id);
                    $scope.getArtistGenre($scope.artist_name);
                    $scope.getTopSongsData($scope.artist_id, access_token);

                },
                error: function(err){
                    console.log(err);
                    alert("cannot get artist id");
                }
            });  //AJAX

            /*LOOK FOR NEWEST RELEASES OF THAT ARTIST*/
            //BUG FOUND HERE
            $scope.allItemsComb = $scope.carouselItems.concat($scope.new_releases);

            angular.forEach($scope.allItemsComb, function(value, key){
                angular.forEach(value['artists'], function(value_2, key_2){
                    if(value_2['name'] == $scope.inp_search){
                        $scope.new_rel_artist.push($scope.allItemsComb[key]);
                    } //IF
                }); //INNER FOREACH
            }); //OUTTER FOREACH
        });    //BTN

        $scope.getHint = function(){
            var inp = $("input[name = 'artist-search']").val();
            if(inp.length > 0){
                $scope.hintUsed = true;
            }
            else{
                $scope.hintUsed = false;
            }
          
            console.log($scope.inp_search);
            var artistNames = [];

            if($scope.inp_search.includes('%20')){
                $scope.filtered_search =  $scope.inp_search.replace(' ', '%20');
            }
            else{
                $scope.filtered_search = $scope.inp_search;
            }

            if($scope.inp_search.length > 0){
             /* SEARCH PROTOTYPE */
             $.ajax({
                url: 'https://api.spotify.com/v1/search?q='+$scope.filtered_search+'&type=artist&market=' + $scope.userLocation,
                type: 'GET',
                headers: {
                    'Authorization' : 'Bearer ' + access_token
                },
                success: function(data) {
                    $scope.isSub($scope.inp_search);
                    //console.log(JSON.stringify(data));
                    $scope.artist_data = JSON.stringify(data);

                    //   console.log(data);
                    //  console.log(data['artists']['items'].length);

                    for(var i = 0; i < data['artists']['items'].length; i++){
                        artistNames.push(data['artists']['items'][i]['name']);
                    }
                
                   // console.log(artistNames);
                    autocomplete(document.getElementById('artist-search'), artistNames);
                    $scope.$apply();

                },
                error: function(err){
                    console.log(err);
                }
            });  //AJAX
        }



        }; // getHint

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
                        //console.log($scope.artist_top_tracks);
                        $scope.$apply();

                    },
                    error: function(err){
                        alert("cannot get artist data");
                        //console.log(err);
                    }
                }); //AJAX
            } //IF
        }; //FUNC

        //LOAD-MORE
        $("button[name = 'load-more']").click(function(e){
            $scope.searchReqOffset += 50;
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
                    //console.log(err);
                }
            }); //AJAX

        }); //BUTTON


        $scope.resFilter = function(){
            $('#filter :nth-child(0 )').prop('selected', true); // To select via index
        };

        /* CAROUSEL */
        $('.carousel.carousel-slider').carousel({
            fullWidth: true,
            indicators: true,
            duration: 250,
            interval: 6000,
            dist: 0
        });

        /*CAROUSEL AUTOPLAY */
        setTimeout(function(){autoplay();}, 2500);
        function autoplay() {
            $('.carousel.carousel-slider').carousel('next');
            setTimeout(autoplay, 4500);
        }

        $scope.goToUser = function(artistName){
            if(artistName.length != 0){
                $("input[name = 'artist-search']").val(artistName);
                $scope.buildURL(artistName);

                if($scope.userLocation.length <= 0){
                //GET USER LOCATION
                $.get("https://ipinfo.io", function(response) {
                    //console.log(response.city, response.country);
                    $scope.userLocation = response.country;
                    $("button[name = 'artist-submit']").click();
                }, "jsonp");   
                }
                else{
                     $("button[name = 'artist-submit']").click();
                }
                
            }
        };

        $scope.subscribe = function(artistName){
            /* IN PROGRESS */
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

            var json = '{"frouter": {"apikey": "1234", "method": "subscribe", "artistName": "' + artistName + '", "userID": "' + $scope.userID + '"  } }';
            var obj = JSON.parse(json);

            $http({
                url: './webservice/frouter.php?f=route',
                headers: { 'Content-Type': 'application/json;charset=utf-8' },
                method: "POST",
                data: { obj }
            })
            .then(function(response) {
                        // success
                        var data = response.data;
                        var subSuccess = data[0];
                        //console.log(subSuccess);

                        if(subSuccess === true){
                            $('#alert_box').css('display', 'block');
                            $scope.isSubd = true;
                            $scope.subMessage = "Successfully subscribed! You'll be informed when the artist releases something new.";
                        }

                    },
                    function(response) {
                        // failed
                        //console.log(response);
                    });

        };  //subscribe method

        $scope.unsubscribe = function (artistName) {
            /* IN PROGRESS */
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

            var json = '{"frouter": {"apikey": "1234", "method": "unsubscribe", "artistName": "' + artistName + '", "userID": "' + $scope.userID + '"  } }';
            var obj = JSON.parse(json);

            $http({
                url: './webservice/frouter.php?f=route',
                headers: {'Content-Type': 'application/json;charset=utf-8'},
                method: "POST",
                data: { obj }
            })
            .then(function (response) {
                        // success
                        var data = response.data;
                        var unsubSuccess = data[0];
                        //console.log(unsubSuccess);

                        if (unsubSuccess === true) {
                            $('#alert_box').css('display', 'block');
                            $scope.isSubd = false;
                            $scope.subMessage = 'Successfully unsubscribed!';
                        }

                    },
                    function (response) {
                        // failed
                        //console.log(response);
                    });

        };  //unsubscribe method

        $scope.isSub = function(artistName){
            var json = '{"frouter": {"apikey": "1234", "method": "isSub", "artistName": "' + artistName + '", "userID": "' + $scope.userID + '"  } }';
            var obj = JSON.parse(json);
            //console.log(obj);

            $http({
                url: './webservice/frouter.php?f=route',
                headers: { 'Content-Type': 'application/json;charset=utf-8' },
                method: "POST",
                data: { obj }
            })
            .then(function(response) {
                        // success
                        var data = response.data;
                        var isSub = data[0];
                        //console.log(isSub);

                        if(isSub === true){
                            $scope.isSubd = true;
                        }
                        else{
                            $scope.isSubd = false;
                        }

                    },
                    function(response) {
                        // failed
                        //console.log(response);
                    }); //HTTP END


        };  //isSsub method

        /* URL PARSING */
        $scope.buildURL = function(input) {
            //console.log("called!!");
            history.pushState(null, '', '?at=' + input);
        };

        var rootURL = window.location.search;
        //console.log(rootURL);
        if(!rootURL.includes('reg')){
            // IDEA IS TO NOT DO ANYTHING IF REG IS IN LOCATION.SEARCH
            rootURL = rootURL.slice(4, rootURL.length);
            rootURL = rootURL.replace('%20', ' ');

            if(typeof(rootURL) != null || typeof(rootURL) != undefined){
                //console.log(rootURL);
                $scope.goToUser(rootURL);
            }
        }
        

        $scope.resetURL = function(){
            //console.log("reset url");
            history.replaceState(null, '', 'index.php');
        };

        $scope.favor = function(e, track, artist){
            let buttonID = e.target.id;
            let betweenTag =  $("i[id='"+buttonID+"']").text().trim(); 
    
            if(betweenTag === 'favorite_border'){
                 $("i[id='"+buttonID+"']").text('favorite');
            }
            else{
                $("i[id='"+buttonID+"']").text('favorite_border');
            }
            //console.log(track, artist);

            /* CURRENT FAVORED */
            var obj = new Object();
            obj.track = track;
            obj.artist = artist;

           // var json = JSON.stringify(obj);
            
            var arr = [];
            arr.push(obj);
            // GET EXISTING ENTRIES IN LOCALSTORAGE

            if(window.localStorage.getItem('favor') != null){
                // THERES SOMETHING IN THERE ALREADY
                let currentStorage = window.localStorage.getItem('favor');
                let storageOBJ = JSON.parse(currentStorage);
                
                var arr = Object.keys(storageOBJ).map(function(k) { return storageOBJ[k] })  
                arr.push(obj);
                window.localStorage.setItem('favor', JSON.stringify(arr));
            }
            else{
                window.localStorage.setItem('favor', JSON.stringify(arr));
            }

        };

    

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

    app.filter('cut', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace !== -1) {
                    //Also remove . and , so its gives a cleaner result.
                    if (value.charAt(lastspace-1) === '.' || value.charAt(lastspace-1) === ',') {
                        lastspace = lastspace - 1;
                    }
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' …');
        };
    });

    function autocomplete(inp, arr) {
        /*the autocomplete function takes two arguments,
        the text field element and an array of possible autocompleted values:*/
        var currentFocus;
        /*execute a function when someone writes in the text field:*/
        inp.addEventListener("input", function(e) {
            var a, b, i, val = this.value;
            /*close any already open lists of autocompleted values*/
            closeAllLists();
            if (!val) { return false;}
            currentFocus = -1;
            /*create a DIV element that will contain the items (values):*/
            a = document.createElement("DIV");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            /*append the DIV element as a child of the autocomplete container:*/
            this.parentNode.appendChild(a);
            /*for each item in the array...*/
            for (i = 0; i < arr.length; i++) {
              /*check if the item starts with the same letters as the text field value:*/
              if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                    b.addEventListener("click", function(e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
              }
            }
        });
        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
              /*If the arrow DOWN key is pressed,
              increase the currentFocus variable:*/
              currentFocus++;
              /*and and make the current item more visible:*/
              addActive(x);
            } else if (e.keyCode == 38) { //up
              /*If the arrow UP key is pressed,
              decrease the currentFocus variable:*/
              currentFocus--;
              /*and and make the current item more visible:*/
              addActive(x);
            } else if (e.keyCode == 13) {
              /*If the ENTER key is pressed, prevent the form from being submitted,*/
              e.preventDefault();
              if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
              }
            }
        });
        function addActive(x) {
          /*a function to classify an item as "active":*/
          if (!x) return false;
          /*start by removing the "active" class on all items:*/
          removeActive(x);
          if (currentFocus >= x.length) currentFocus = 0;
          if (currentFocus < 0) currentFocus = (x.length - 1);
          /*add class "autocomplete-active":*/
          x[currentFocus].classList.add("autocomplete-active");
        }
        function removeActive(x) {
          /*a function to remove the "active" class from all autocomplete items:*/
          for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
          }
        }
        function closeAllLists(elmnt) {
          /*close all autocomplete lists in the document,
          except the one passed as an argument:*/
          var x = document.getElementsByClassName("autocomplete-items");
          for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }
      /*execute a function when someone clicks in the document:*/
      document.addEventListener("click", function (e) {
          closeAllLists(e.target);
      });
    } 
