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

//var th = document.getElementById('thumbnails');
//
//th.addEventListener('click', function(e) {
//  var t = e.target, new_src = t.parentNode.href, 
//      large = document.getElementById('large'),
//      cl = large.classList,
//      lgwrap = document.getElementById('lg-wrap');
//  lgwrap.style.backgroundImage = 'url(' +large.src + ')';
//  if(cl) cl.add('hideme');
//  window.setTimeout(function(){
//    large.src = new_src;
//    if(cl) cl.remove('hideme');
//  }, 50);
//  e.preventDefault();
//}, false);


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