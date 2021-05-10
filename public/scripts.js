  // Script pour faire la différence des dates//
  let dates = document.querySelectorAll('.date');

  console.log('toto')

  var i;
  for (i = 0; i < dates.length; i++) {

      const date1 = new Date(dates[i].dataset.date); // récupération de date dans le span pour chaque entrainement
      var today = new Date();
      let date2 = new Date('0' + (today.getMonth() + 1) + '/' + (today.getDate()) + '/' + today.getFullYear());

      const diffTime = Math.abs(date1 - date2);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      let nbClass = i + 1;
      document.querySelector('.date-' + nbClass + '-diff').innerHTML = diffDays;
  }