$(".switch").find("input[type=checkbox]").on("change",function() {
      var status = $(this).prop('checked');

      if(status === true){
        //darkmode
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
        $('body').css("background-color", "white");

        $('#headlineNewtist').css("color", "black");
        $('#txtSearchArtist').css("color", "black");
        $('#txtNewReleases').addClass('grey-text');
        $('.select-dropdown').css("color", "black");


      }
  });


  // FLOATING button

  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.fixed-action-btn');
    var instances = M.FloatingActionButton.init(elems, {

    });
  });

  // Or with jQuery

  $(document).ready(function(){
    $('.fixed-action-btn').floatingActionButton();
  });
