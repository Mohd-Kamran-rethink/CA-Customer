const manageModal = (id) => {
    $("#deleteInput").val(id);
    $(`#modal-default`).modal("show");
};

$(function () {
    $("#reservation").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
    });
});
