$(document).ready(function () {

    let taskListingParent = $('.taskListingParent');
    let loadingImage = $('.loadingImage');
    let noToDoAlert = $('.noToDoAlert');


    //Loading to-do data after page is fully loaded in background
    loadToDoItem();

    //Handle to do item load
    function loadToDoItem() {
        //Get to-do request from api using ajax request
        $.ajax({
            type: 'GET',
            url: endPoint,
            async: true,
            contentType: false,
            processData: false,
            complete: function () {
                loadingImage.hide();
            },
            success: function (response) {

                let status = Number(response.status);

                if (status === 1) {

                    let dataObject = response.data;
                    let dataKeys = Object.keys(dataObject);
                    let totalToDo = dataKeys.length;

                    console.log(totalToDo);

                    //Check if to-do list is empty
                    if (totalToDo < 1) {
                        //shows no to-do message if to-do is not found
                        noToDoAlert.show();
                    } else {
                        let markUp = renderToDoListTable(dataObject);
                        taskListingParent.html(markUp);
                    }

                }
            }
        });
    }

    //Handle to-do list table render
    function renderToDoListTable(dataObject) {
        let markUp = '';
        //Process to-do list if to-do is found2022-05-05
        $.each(dataObject, function (key, valueObj) {

            let taskStatus = Number(valueObj.status);
            let statusBtn;

            if (taskStatus === 1) {
                statusBtn = '<button type="button" class="btn btn-success mb-1 ms-1 updateStatusBtn" data-id="' + valueObj.id + '" data-status="0">Completed</button>\n';
            } else {
                statusBtn = '<button type="button" class="btn btn-warning mb-1 ms-1 updateStatusBtn" data-id="' + valueObj.id + '" data-status="1">Pending</button>\n';
            }

            markUp += '<tr data-id="' + valueObj.id + '">\n' +
                '<td class="align-middle">' + valueObj.task + '</td>\n' +
                '<td class="align-middle text-center"><span class="badge bg-secondary">' + valueObj.date + '</span></td>\n' +
                '<td class="align-middle text-end">\n' +
                '<button type="button" class="btn btn-primary mb-1 updateBtn" data-id="' + valueObj.id + '" aria-hidden="true"><i class="bi bi-pencil-square"></i></button>\n' +
                '<button type="button" class="btn btn-danger mb-1 deleteBtn" data-id="' + valueObj.id + '" aria-hidden="true"><i class="bi bi-x-circle"></i></button>\n' +
                statusBtn +
                '</td>\n' +
                '</tr>';

        });
        return markUp;
    }

    //Handle to-do item add\\
    let addSubmitBtn = $('.addSubmitBtn');

    $(document).on('submit', '.addForm', function (event) {

        event.preventDefault();

        noToDoAlert.hide();

        let object = this;
        let method = object.method;

        console.log(method);

        let formData = new FormData(object);

        $.ajax({
            type: method,
            url: endPoint,
            async: true,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                addSubmitBtn.text('Adding...');
            },
            complete: function () {
                addSubmitBtn.text('Add');
            },
            success: function (data) {

                let status = Number(data.status);

                if (status === 1) {

                    let markUp = '<tr data-id="' + data.id + '">\n' +
                        '<td class="align-middle">' + $('.task').val() + '</td>\n' +
                        '<td class="align-middle text-center"><span class="badge bg-secondary">' + $('.date').val() + '</span></td>\n' +
                        '<td class="align-middle text-end">\n' +
                        '<button type="button" class="btn btn-primary mb-1 updateBtn" data-id="' + data.id + '"><i class="bi bi-pencil-square"></i></button>\n' +
                        '<button type="button" class="btn btn-danger mb-1 deleteBtn" data-id="' + data.id + '"><i class="bi bi-x-circle"></i></button>\n' +
                        '<button type="button" class="btn btn-warning mb-1 ms-1 updateStatusBtn" data-id="' + data.id + '" data-status="1">Pending</button>\n' +
                        '</td>\n' +
                        '</tr>';

                    taskListingParent.append(markUp);
                    $(object)[0].reset();

                }
            }
        });

    });
    //End handling to-do item add\\

    //Start handling to-do item delete\\
    $(document).on('click', '.deleteBtn', function (event) {
        let btnObject = this;
        let itemID = $(btnObject).data('id');

        $.ajax({
            type: 'DELETE',
            url: endPoint,
            async: true,
            data: JSON.stringify({
                "id": itemID
            }),
            contentType: false,
            processData: false,

            success: function (response) {

                let status = Number(response.status);

                if (status === 1) {

                    let dataObject = response.updated_data;
                    let dataKeys = Object.keys(dataObject);
                    let totalToDo = dataKeys.length;

                    console.log(totalToDo);

                    //Check if to-do list is empty
                    if (totalToDo > -1) {
                        let markUp = renderToDoListTable(dataObject);
                        taskListingParent.html(markUp);
                    }

                    //shows no to-do message if to-do is not found
                    if(totalToDo == 0){
                        noToDoAlert.show();
                    }
                }
            }
        });

    });
    //End handling to-do item delete\\

    //Start handling to-do item status update\\
    $(document).on('click', '.updateStatusBtn', function (event) {

        let btnObject = this;
        let itemStatus = Number($(btnObject).data('status'));

        $.ajax({
            type: 'PUT',
            url: endPoint,
            async: true,
            data: JSON.stringify({
                "id": Number($(btnObject).data('id')),
                "status": itemStatus
            }),
            contentType: false,
            processData: false,

            success: function (response) {

                let status = Number(response.status);

                if (status === 1) {
                    if (itemStatus === 1) {
                        //Update Status button for completed task
                        $(btnObject).removeClass('btn-warning');
                        $(btnObject).addClass('btn-success');
                        $(btnObject).text('Completed');
                        $(btnObject).data('status', 0);
                    } else {
                        //Update Status button for pending task
                        $(btnObject).addClass('btn-warning');
                        $(btnObject).removeClass('btn-success');
                        $(btnObject).text('Pending');
                        $(btnObject).data('status', 1);
                    }
                }
            }
        });

    });
    //End handling to-do item status update\\

    //Start handling to-do item update
    let updateModal = $("#todoEditModal");
    let editSubmitBtn = $('.editSubmitBtn');
    let btnObject;
    let updateRow;
    let taskEditInput = $('.taskEditInput');
    let dateEditInput = $('.dateEditInput');
    let idEditInput = $('.idEditInput');

    $(document).on('click', '.updateBtn', function (event) {
        btnObject = this;

        //get to-do data
        updateRow = $(btnObject).closest('tr');
        let task = updateRow.find('td:nth-child(1)').text();
        let date = updateRow.find('td:nth-child(2) > span').text();

        //set to-do data on edit form
        taskEditInput.val(task);
        dateEditInput.val(date);
        idEditInput.val($(btnObject).data('id'));

        //shows update modal
        updateModal.modal('show');
    });

    $(document).on('submit', '.editForm', function (event) {

        event.preventDefault();

        noToDoAlert.hide();

        let object = this;

        let formData = new FormData(object);
        let jsonData = JSON.stringify(Object.fromEntries(formData));

        console.log(jsonData);

        $.ajax({
            type: 'PUT',
            url: endPoint,
            async: true,
            data: jsonData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                editSubmitBtn.text('Saving...');
            },
            complete: function () {
                editSubmitBtn.text('Save');
            },
            success: function (data) {

                let status = Number(data.status);

                if (status === 1) {
                    //update data in the table
                    updateRow.find('td:nth-child(1)').text(taskEditInput.val());
                    updateRow.find('td:nth-child(2) > span').text(dateEditInput.val());
                    updateModal.modal('hide');
                }
            }
        });

    });
    //End handling to-do item update
});