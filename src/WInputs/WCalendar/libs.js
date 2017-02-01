$(document).ready(function() {
  //------------------------------------
  $("div.dia").click(function() {
    if(showBtNew) {
       location.href = '?CONTROL='+control+'&EVENT=editNew&day='+$(this).attr('day');
    }
  });
  //------------------------------------
});
