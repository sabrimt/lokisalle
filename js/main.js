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


/***** WORK ON INPUT-FILE (GESTION SALLE) *****/
    var full = 'Selectionné   <span class="glyphicon glyphicon-ok"></span>';// label en cas de fichier sélectionné
    var empty = 'Ajouter une photo...<span class="glyphicon glyphicon-save"></span>';// label quand le input est vide
    var i = 1;
    var id_complete = '';
    //var affiche_image = $()



    while( i <= 3 ){
        var photo = '#photo' + id_complete;
        var label = '#photo_lab' + id_complete;

        change_label(label, photo);

        i++;
        id_complete = '_' + i;
    }

    function change_label(label, photo){
        var sel = $(photo);
        var label_sel = $(label);

        if (sel.val() !== "")
        {
            label_sel.html(full)
                .addClass("oqp");
        }
        sel.on('change', function (){
    //        console.log('changement ¤¤¤ ' + sel.val() + ' ¤¤¤');

            if (sel.val() == "")
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
/***** FILTRE RECHERCHE ACCUEIL *****/
$spanCapacite=$('#capacite-filtre');
    $spanPrix=$('#prix-filtre');

    $('#prix').on('input change', function(){
        $thisValue=$(this).val();
        $spanPrix.html($thisValue);
    });

    $('#capacite').on('input change', function(){
        $thisValue=$(this).val();
        if($thisValue > 0)
        {
            $spanCapacite.html($thisValue);
        }
        else
        {
            $spanCapacite.html("Indifférent");
        }
    });
/***** filtre recherche accueil *****/
}); // fermeture document.ready


