  // Script pour faire la différence des dates//
  // let dates = document.querySelectorAll('.date');

  // var i;
  // for (i = 0; i < dates.length; i++) {

  //     const date1 = new Date(dates[i].dataset.date); // récupération de date dans le span pour chaque entrainement
  //     var today = new Date();
  //     let date2 = new Date('0' + (today.getMonth() + 1) + '/' + (today.getDate()) + '/' + today.getFullYear());

  //     const diffTime = Math.abs(date1 - date2);
  //     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  //     let nbClass = i + 1;
  //     document.querySelector('.date-' + nbClass + '-diff').innerHTML = diffDays;
  // }


  // *** Script pour afficher le form dans la page mon compte

  if (document.querySelector('.edit-my-info')) {
    $('.edit-my-info').on('click', function () {
      $('form#form-edit-my-info').css('display', 'block');
      $('.clean-form').css('display', 'none');
    })
    $('.form-group').addClass('col-12 col-lg-3')
  }

  if (document.querySelector('.form-edit-user-info')) {
    $('.form-group').addClass('col-12 col-lg-3')
  }

  // *** Script pour changer le status d'une session *** //
  $('#session_form_status[type="checkbox"]').change(function () {
    updateStatus()
  });


  //Fonction pour update le statut de la session
  function updateStatus() {
    
    if ($('#session_form_status[type="checkbox"]').is(':visible')) {
      
      if ($('#session_form_status[type="checkbox"]').is(':checked')) {
        $('.edit-status-session').text("La session est ouverte au public");
        $('.edit-status-session').addClass('text-success');
        $('.edit-status-session').removeClass('text-danger');
      } else {
        $('.edit-status-session').text("La session est fermée au public");
        $('.edit-status-session').addClass('text-danger');
        $('.edit-status-session').removeClass('text-success');
      }
    }
  }
  
  //On execute la fonction au chargement de la page
  updateStatus()
  
  // *** Script pour changer le background au refresh
  
  var num = Math.floor(Math.random() * 10);
  document.body.style.background = "url('http://localhost/motocross-auribail/public/img/image-" + num + ".jpg')"
  document.body.style.backgroundSize = "cover";
  document.body.style.backgroundRepeat = "no-repeat";
  document.body.style.backgroundAttachment = "fixed";

  //** Script de recherche */


  // $(document).ready(function(){
  //   $("#search").on("keyup", function() {
  //     var value = $(this).val().toLowerCase();
  //     $("#table tr").filter(function() {
  //       $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  //     });
  //   });
  // });
