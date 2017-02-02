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



//});

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
var full = 'Selectionné   <span class="glyphicon glyphicon-ok"></span>';// label en cas de fichier sélectionné
var empty = 'Ajouter une photo...<span class="glyphicon glyphicon-save"></span>';// label quand le input est vide
var i = 1;
var id_photo_complete = '';



while( i <= 3 ){
    var photo = '#photo' + id_photo_complete;
    var label = '#photo' + i;
    change_label(label, photo);
    i++;
    id_photo_complete = '_' + i;
}

function change_label(label, photo){
    var photo_sel = $(photo);
    var label_sel = $(label);
    if (photo_sel.val() !== "")
    {
        label_sel.html(full)
            .addClass("oqp");
        console.log(photo_sel.val());
    }
    photo_sel.on('change', function (){
//        console.log('changement ¤¤¤ ' + photo_sel.val() + ' ¤¤¤');

        if (photo_sel.val() == "")
        {
            label_sel.html(empty)
                    .removeClass("oqp");
        } else {
            label_sel.html(full)
                    .addClass("oqp");
            
        }
    });
    
};

/***** work on input-file (gestion salle) *****/
}); // fermeture document.ready