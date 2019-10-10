<?php
require_once 'ctrl_parent/list_controller.inc';
require_once 'ctrl_detail/list_controller.inc';
?>

<?php WPage::get()?>
  <table width="100%"><tr>
      <td valign="top" style="width:350px">
        <?php require_once 'ctrl_parent/list_view.inc'; ?>
      </td>
      <td> &raquo; </td>
      <td valign="top">
        <?php require_once 'ctrl_detail/list_view.inc'; ?>
      </td>
  </tr></table>
<?php WPage::get_end()?>
