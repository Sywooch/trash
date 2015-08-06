/**  * Created by a.tyutyunnikov on 30.09.14.  */
$(document).ready(function () {
    var dataTableWrapper = $('table.dataTable').data('DataTableWrapper');
    if (dataTableWrapper) {
        dataTableWrapper.editor.on('postSubmit', function (e, response, action) {
            if (action == 'error' && response.status !== undefined && (response.status == 403 || response.status == 401)) {
                $(location).attr('href', '/lk/auth');
            }
        });
    }
});

