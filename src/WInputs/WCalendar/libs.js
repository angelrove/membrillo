
$(document).ready(function() {
  //------------------------------------
  $("div.dia").click(function() {
    if(showBtNew) {
       location.href = './'+control+'/?EVENT=editNew&day='+$(this).attr('day');
    }
  });
  //------------------------------------
});
