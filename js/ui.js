/* STORAGE COLORMODE */
let colorMode = window.localStorage.getItem('colorMode');
let mode = '';

(colorMode == 'dark' ? mode = 'dark' : mode = 'white');

switchColor(mode);

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
        $('#userLogged').css("color", 'white');

        /* NOTIFICATION */
        $('.collapsible-header').css('background-color', '#455a64');
        $('.collapsible-header').css('border-bottom', '1 px solid #455a64 !important');

        $('.collapsible').css('border-top', '1 px solid #455a64 !important');
        $('.collapsible').css('border-right', '1 px solid #455a64 !important');
        $('.collapsible').css('border-left', '1 px solid #455a64 !important');
        $('.collapsible').css('color', 'white');


          $( '.chip' ).each(function () {
              this.style.setProperty( 'background-color', 'white', 'important' );
          });


        window.localStorage.setItem('colorMode', 'dark');

      }
      else{
        //whitemode
        $('body').css("background-color", "white");

        $('#headlineNewtist').css("color", "black");
        $('#txtSearchArtist').css("color", "black");
        $('#userLogged').css("color", 'black');
        $('#txtNewReleases').addClass('grey-text');
        $('.select-dropdown').css("color", "black");


        /* NOTIFICATION */
        $('.collapsible-header').css('background-color', '#fff');
        $('.collapsible-header').css('border-bottom', '1 px solid #ddd !important');

        $('.collapsible').css('border-top', '1 px solid #ddd !important');
        $('.collapsible').css('border-right', '1 px solid #ddd !important');
        $('.collapsible').css('border-left', '1 px solid #ddd !important');
        $('.collapsible').css('color', 'black');



          $( '.chip' ).each(function () {
              this.style.setProperty( 'background-color', '#2bbbad', 'important' );
          });

        window.localStorage.setItem('colorMode', 'white');
      }
  });

  function switchColor(color){
    if(color === "dark"){
      //darkmode
      setTimeout(function(){
        $(".switch").find("input[type=checkbox]").prop('checked', true);
      }, 1000);

      $('body').css("background-color", "#263238");
      $('#footer').css("background-color", "#263238");
      
      /* TEXT */
      $('#headlineNewtist').css("color", "white");
      $('#txtSearchArtist').css("color", "white");
      $('#txtNewReleases').removeClass('grey-text');
      $('#txtNewReleases').css("color", "white");
      $('#userLogged').css("color", 'white');
      $('.select-dropdown').css("color", "white");

        $( '.chip' ).each(function () {
            this.style.setProperty( 'background-color', 'white', 'important' );
        });

      setTimeout(function(){
        /* NOTIFICATION */
        $('.collapsible-header').css('background-color', '#455a64');
        $('.collapsible-header').css('border-bottom', '1 px solid #455a64 !important');

        $('.collapsible').css('border-top', '1 px solid #455a64 !important');
        $('.collapsible').css('border-right', '1 px solid #455a64 !important');
        $('.collapsible').css('border-left', '1 px solid #455a64 !important');
        $('.collapsible').css('color', 'white');
    }, 2000);

    
      window.localStorage.setItem('colorMode', 'dark');

    }
    else{
      //whitemode
      $('body').css("background-color", "white");

      $('#headlineNewtist').css("color", "black");
      $('#txtSearchArtist').css("color", "black");
      $('#txtNewReleases').addClass('grey-text');
      $('.select-dropdown').css("color", "black");
      $('#userLogged').css("color", 'black');

        $( '.chip' ).each(function () {
            this.style.setProperty( 'background-color', '#2bbbad', 'important' );
        });

      setTimeout(function(){
        /* NOTIFICATION */
        $('.collapsible-header').css('background-color', '#fff');
        $('.collapsible-header').css('border-bottom', '1 px solid #ddd !important');

        $('.collapsible').css('border-top', '1 px solid #ddd !important');
        $('.collapsible').css('border-right', '1 px solid #ddd !important');
        $('.collapsible').css('border-left', '1 px solid #ddd !important');
        $('.collapsible').css('color', 'black');
    }, 1500);
      
      window.localStorage.setItem('colorMode', 'white');
    }
  };
  /*
  // FLOATING button
  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.fixed-action-btn');
    var instances = M.FloatingActionButton.init(elems, {
      hoverEnabled: false
    });
  });
  */
  // Or with jQuery
  $(document).ready(function(){
    $('.fixed-action-btn').floatingActionButton();
    $('.collapsible').collapsible();
    $('.dropdown-trigger').dropdown();
  });

  function scrollToTop(){
      $('html, body').animate({ scrollTop: ($('#txtSearchArtist').offset().top)}, 'slow');
  };

  /* ALERT BOX */
  $('#alert_close').click(function(){
      $( "#alert_box" ).fadeOut( "slow", function() {
    });
   });


function scrollToMessageSub(){
    console.log("scrolled");
    setTimeout(function(){
        $('html, body').animate({ scrollTop: ($('#alert_box').offset().top)}, 'slow');
    }, 1000);

};

$("button[name = 'artist-submit']").click(function(e){
  setTimeout(function(){
        $('html, body').animate({ scrollTop: ($('#artistCard_INC').offset().top)}, 'slow');
    }, 1000);
}); //BUTTON




