
$(document).ready(function() {
  //------------------------------------
  $("div.dia").click(function() {
    if(showBtNew) {
       location.href = './'+control+'/?'+EVENT=CRUD_EDIT_NEW+'&day='+$(this).attr('day');
    }
  });
  //------------------------------------
});
