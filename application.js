var lat = 0;
var lng = 0;
var max_timestamp = Math.round(+new Date()/1000);

$(document).ready(function(){
  var loading = "";

  alert(options.distance);
  $('h1 #distance').text(options.distance);
  
  function startLoading(){
    loading = setInterval(function(){$('#loading').fadeOut(500,function(){$(this).fadeIn(500);});}, 1000);
    $('#loading').bind('inview', function (event, visible) {
      if (visible == true) {
        $('#loading').unbind();
        startAPICall();
      }
    });
  }
  
  startLoading();
  $('#loading').unbind();
  startAPICall();
  
  function startAPICall(){
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(success, error);
    } else {
      $('#loading').empty().append('<b>Fehler:</b> Aktuelle Geolocation konnte nicht ermittelt werden :(');
    }
    
    function success(position) {
      
      lat = position.coords.latitude;
      lng = position.coords.longitude;
      
      var url = "https://api.instagram.com/v1/media/search?client_id=" + options.client_id + "&lat="+lat+"&lng="+lng+"&distance=" + options.distance + "&max_timestamp="+max_timestamp;
      
      $.ajax({
        type: "GET",
        dataType: "jsonp",
        cache: false,
        url: url,
        statusCode: {
          200: function(data){
            window.clearInterval(loading);
            $('#loading').empty().append('Weitere Bilder laden …');
            
            startLoading();
            
            for (var i = 0; i < 10; i++) {
              $("#pics").append("<a target='_blank' href='" + data.data[i].link + "'><img src='" + data.data[i].images[options.image_type].url + "'></img></a><br>");
              
              max_timestamp = data.data[i].created_time;
            }
          },
            404: function() {
              window.clearInterval(loading);
              $('#loading').empty().append('<b>Fehler:</b> Serverfehler :(');
            },
            500: function() {
              window.clearInterval(loading);
              $('#loading').empty().append('<b>Fehler:</b> Serverfehler :(');
            }
        }
       });
    }
    
    function error(msg) {
      window.clearInterval(loading);
      $('#loading').empty().append('<b>Fehler:</b> Aktuelle Geolocation konnte nicht ermittelt werden :(');
      
    }
  }
  
});