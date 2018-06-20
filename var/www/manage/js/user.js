/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id = 0;
var edit_item = '';
var edit_verb = '';
var edit_type = "add";
var fadeout = 0;
$(function () {

    $("#btn-user-add")
            .click(function () {
                edit_type = "add";
                $("#form-add").dialog("open");
            });
    get_users();
    $('#input-area, #input-phone1, #input-phone2').autotab_magic().autotab_filter('numeric');
    $('#grid-users').width($('#tabs-1').width() - 30);


})



function get_users() {
    $('#userGrid').load(APPLICATION_URL + '/user/list/format/html');
}

function show_add() {
    edit_type = "add";
    edit_verb = "added";
    $('#input-username,#input-area,#input-phone1,#input-phone2,#input-fname,#input-lname').val('');
    $('#input-role').val('Admin');
    $('#addModal').css('height', '220px');
    $('#addModal').css('margin-top', '-120px');
    $('#myModalLabel').html('Add Team Member');
    $('#addModal').modal('show');
}


function edit_user(id) {
    $('#myModalLabel').html('Edit Team Member');
    $('#btn-update').html('Save Changes');
    edit_type = "edit";
    edit_id = id;
    var result = $.grep(users, function (e) {
        return e.user_id == id;
    });

    $("#input-username").val(result[0].username);
    $("#input-fname").val(result[0].fname);
    $("#input-lname").val(result[0].lname);
    $("#input-role").val(result[0].role);
    $("#input-area").val(result[0].phone.substr(1, 3));
    $("#input-phone1").val(result[0].phone.substr(6, 3));
    $("#input-phone2").val(result[0].phone.substr(10, 4));
    $('#addModal').css('height', '220px');
    $('#addModal').css('margin-top', '-120px');
    $('#addModal').modal('show');


}

function addUser() {
    var msg = '';
    if ($('#input-fname').val() == "" || $('#input-lname').val() == "") {
        msg = 'Please enter the full name.';
    } else if ($('#input-username').val() == "") {
        msg = 'Please enter an email address.';
    } else if (!isValidEmailAddress($('#input-username').val())) {
        msg = 'Please enter a valid email address.';
    }

    if (msg == '') {
        var phone = '';
        if ($('#input-area').val().length == 3 && $('#input-phone1').val().length == 3 && $('#input-phone2').val().length == 4) {
            phone = "(" + $('#input-area').val() + ") " + $('#input-phone1').val() + "-" + $('#input-phone2').val();
        }
        if (edit_type == "edit") {
            edit_verb = "updated";
        } else {
            edit_verb = "added";
        }
        var data = "user_id=" + edit_id + "&username=" + $('#input-username').val() + "&password=nothing&type=" + edit_type + "&fname=" + $('#input-fname').val() + "&lname=" + $('#input-lname').val() + "&phone=" + phone;
        loader('show');
        $.ajax(
                {
                    url: APPLICATION_URL + "/user/handler/format/html",
                    data: data,
                    type: "POST",
                    success: function (response) {
                        edit_item = $('#input-fname').val() + ' ' + $('#input-lname').val();
                        if (isNumber(response)) {
                            if (response >= 0) {
                                get_users();
                                show_msg(1, '');
                            } else {
                                if (response == -1) {
                                    alert('The user you are trying to add is already a member of this campaign.');
                                } else {
                                    show_msg(0, '');
                                }
                            }
                            $('#addModal').modal('hide');
                        } else {
                            alert('Sorry, there was an error.');
                        }
                    }
                });
    } else {

        alert(msg);

    }
}
function delete_user(user_id) {
    $('#msg-update').css('opacity', '0.0');
    edit_id = user_id;
    var result = $.grep(users, function (e) {
        return e.user_id == user_id;
    });
    edit_item = result[0].fname + " " + result[0].lname;
    edit_verb = "deleted";
    $('#msg-user-delete').html('Are you sure you want to delete user <strong><span id="delete-user">' + edit_item + '</span></strong>?');
    $('#deleteModal').modal('show');

}

function confirmDelete() {
    loader('show');
    $.ajax(
            {
                url: APPLICATION_URL + "/user/handler/format/html",
                data: {user_id: edit_id, type: 'delete'},
                type: "POST",
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    if (isNumber(response)) {
                        if (response > 0) {
                            var result = $.grep(users, function (e) {
                                return e.user_id != edit_id;
                            });
                            users = result;
                            dataView.beginUpdate();
                            dataView.setItems(users, 'user_id');
                            dataView.endUpdate();
                            grid.invalidate();
                            grid.render();
                        } else if (response == -1) {
                            alert('Sorry, there was an issue deleting')
                        } else if (response == -2) {
                            alert('You cannot delete yourself.');
                        }
                    } else {
                        alert('Sorry, there was an error.');
                    }
                }
            });
}

function show_msg(type, msg) {
    $('#msg-update').css('opacity', '1.0');
    if (type > 0) {
        $('#msg-update').html('<div class="alert alert-success">The user, <strong>' + edit_item + '</strong>,  was successfully ' + edit_verb + '.</div>');
    } else {
        $('#msg-update').html('<div class="alert alert-error">The user, <strong>' + edit_item + '</strong>, could not be ' + edit_verb + ' due to an error. Please try again later. ' + msg + '</div>');
    }
    clearTimeout(fadeout);
    fadeout = setTimeout(function () {
        $('.alert-success,.alert-error').fadeOut(1000);
    }, 5000);
}