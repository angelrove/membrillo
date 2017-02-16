
var editores = new Object;

//-------------------------------------------------
function WInputCode_activate(theEditor)
{
  setTimeout(function() {
    theEditor.refresh();
    theEditor.focus();
  },1);
}
//-------------------------------------------------
