$(".switch").find("input[type=checkbox]").on("change",function() {
      var status = $(this).prop('checked');
      
      if(status === true){
        //darkmode
        console.log("darkmode");
        $('body').css("background-color", "#263238");
        $('#footer').css("background-color", "#263238");

        /* TEXT */
        $('#headlineNewtist').css("color", "white");
        $('#txtSearchArtist').css("color", "white");
        $('#txtNewReleases').removeClass('grey-text');
        $('#txtNewReleases').css("color", "white");
        $('.select-dropdown').css("color", "white");

      } 
      else{
        //whitemode
        console.log("whitemode");
        $('body').css("background-color", "white");

        $('#headlineNewtist').css("color", "black");
        $('#txtSearchArtist').css("color", "black");
        $('#txtNewReleases').addClass('grey-text');
        $('.select-dropdown').css("color", "black");
        

      }
  });