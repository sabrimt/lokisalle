$(document).ready(function()
{
	console.log("ready");

	///---DATEPICKER

    // $( ".datepicker" ).datepicker({
    //   dateFormat: "yy-mm-dd"
    // });

    $('.datepicker').datetimepicker({
        dateFormat: "yy-mm-dd"
    });



});



/***** WORK ON INPUT-FILE (GESTION SALLE) *****/
var replace = 'Selectionné   <span class="glyphicon glyphicon-ok"></span>';// remplace le contenu du label
//var i = 1;
//var id_photo_complete = '';
//while( i <= 3 ){
    $('#photo').on('change', function (){
        $('#photo1').html(replace);
        // ++++ ajouter class au label : changer couleurs
    });
//    i++;
//    id_photo_complete = '_' + i;
//}
$('#photo_2').on('change', function (){
    $('#photo2').html(replace);
    // ++++ ajouter class au label : changer couleurs
});
$('#photo_3').on('change', function (){
    $('#photo3').html(replace);
    // ++++ ajouter class au label : changer couleurs
});
/***** work on input-file (gestion salle) *****/



/* *********** FICHE PRODUIT AFFICHAGE PHOTO ************* */
var th = document.getElementById('thumbnails');

th.addEventListener('click', function(e) {
  var t = e.target, new_src = t.parentNode.href, 
      large = document.getElementById('large'),
      cl = large.classList,
      lgwrap = document.getElementById('lg-wrap');
  lgwrap.style.backgroundImage = 'url(' +large.src + ')';
  if(cl) cl.add('hideme');
  window.setTimeout(function(){
    large.src = new_src;
    if(cl) cl.remove('hideme');
  }, 50);
  e.preventDefault();
}, false);


/* ****************  PLAN GOOGLE MAP ***************** */

		var map;
		function initMap(){
		var optionsCarte = {
		center: {lat:45.438257, lng: 4.391788},
		zoom: 16,
		zoomControl: true
		};
		var maCarte = new google.maps.Map(document.getElementById('map'), optionsCarte);
	
		var optionsMarqueur = {
			position: maCarte.getCenter(),
			map: maCarte,
                        icon: image,
                        title:"Hello World!"
		};
		var marqueur = new google.maps.Marker(optionsMarqueur);
		};
                var image = {
                    url: 'img/maison.png',
                    size: new google.maps.Size(20, 32),
                }
           