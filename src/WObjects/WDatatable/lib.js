
$(document).ready(function() {

    // Datatable-------------
    var dataTable = $('#'+id_component).DataTable( {
        ajax: '/index_ajax.php?service=datatable-read',
        select: true,
        dom: 'Bfrtip',
        buttons: [
            'print', 'csvHtml5', 'copyHtml5'
        ],

        aoColumns: dt_cols,

        columnDefs: [
            {
                'render': function ( data, type, row ) {
                   var d = new Date(data);
                   return d.toLocaleString();
                },
                'targets': colsRender_datetime
            },
            {
                'render': function ( data, type, row ) {
                    if(data == true) {
                       return '<span style="color:green"><i class="fas fa-check"></i></span>';
                    } else {
                       return '';
                    }
                },
                'targets': colsRender_bool
            }
        ]

    });

    // Option: Edit New -----
    if(href_new) {
        $(".dt-buttons").append(
            '<a href="" onclick="location.href=href_new;return false;" class="btn btn-success"><i class="fa fa-plus"></i> New</a>'
        );
    }

    // Options events -------
    dataTable.on( 'click', 'button', function () {
        var event = $(this).attr('param_event');
        var data = dataTable.row( $(this).parents('tr') ).data();
        var id = data.id;

        list_onEvent(id_component, id, event, event, '');
    } );

 });


//----------------------------------------------------------------
function WDatatable_onUpdate(id_component, id) {
    list_onEvent(id_component, id, CRUD_EDIT_UPDATE, '', '');
}

function WDatatable_onDelete(id_component, id) {
    list_onEvent(id_component, id, '', CRUD_OPER_DELETE, '');
}

function list_onEvent(id_component, id, event, oper, txConfirm)
{
  var action = $('#'+id_component).attr('param_action');

  var str_id   = (id)  ? '/'+id        : '';
  var str_oper = (oper)? '?OPER='+oper : '';

  var href_event = action + event + str_id + str_oper;
  // console.log(href_event); return;

  // Confirm
  if(txConfirm == '') {
     location.href = href_event;
  }
  else if(confirm(txConfirm)) {
     location.href = href_event;
  }
}
//----------------------------------------------------------------
