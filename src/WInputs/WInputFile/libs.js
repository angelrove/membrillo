$(document).ready(function() {
    //----------------------------------------------------
    $("button.WInputFile_del").click(function() {
        event.preventDefault();

        var WInputFile_name = $(this).attr("param_input_name");

        // Mark deleted -----------
        $("#" + WInputFile_name + "_isDelete").val("1");

        // Elementos visuales ------
        // Hide: button "Delete"
        $(this).hide();

        // Hide: button "View file"
        if ($("#" + WInputFile_name + "_htmFilePrev").length) {
            $("#" + WInputFile_name + "_htmFilePrev").hide();
        }
        // Show: input file
        if ($("#" + WInputFile_name).length) {
            $("#" + WInputFile_name + "_obj_input").show();
        }
    });
    //----------------------------------------------------
});
