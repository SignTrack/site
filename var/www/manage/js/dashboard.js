/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id=0;
var edit_item='';
var edit_verb='';
var edit_type="add";
var inventory_type='';
$(function () {
    $("#input-notify,#edit-quantity").on("keypress", function (evt) {
    if (evt.which < 48 || evt.which > 57)
        {
            evt.preventDefault();
        }
    });
    $("#edit-price").on("keypress", function (evt) {
        console.log(evt.which);
    if ((evt.which < 48 && evt.which!==46) || evt.which > 57 || (evt.which==46 && $("#edit-price").val().indexOf('.')!==-1))
        {
            evt.preventDefault();
        }
    });
    if(MATERIAL_COUNT==0 && SIGN_COUNT==0){
        $('#help-dashboard div').html("Welcome! You'll want to add materials first (e.g. ReBars) and then your sign types. Click \"ADD MORE\" to get started. Please Note: once you add Materials and Sign Types, you cannot delete them. For information on using this admin system, download the User Guide (in the top bar of this page).");
        $('#help-dashboard').slideDown('fast');
    }else if(MATERIAL_COUNT>0 && SIGN_COUNT==0){
        $('#help-dashboard div').html("Once you're done adding Materials it's time to add some Sign Types.");
        $('#help-dashboard').slideDown('fast');
    }else if(SIGN_LOCATIONS==0 && NUM_USERS==1){
        $('#help-dashboard div').html("When you're done adding signs and materials, you'll want to <a href='user'>Add Team Members</a>. You can skip this step and <a href='sign'>Add Sign Locations</a>, but you won't be able to assign them to anyone.");
        $('#help-dashboard').slideDown('fast');
    }else if(SIGN_LOCATIONS>0 && NUM_USERS==1){
        //$('#help-dashboard div').html("You have signs, but no team members. <a href='user'>Add Team Members</a> when ready.");
        //$('#help-dashboard').slideDown('fast');
    }else if(SIGN_LOCATIONS==0 && NUM_USERS>1){
        //$('#help-dashboard div').html("You have volunteers, but no sign locations. <a href='sign'>Add Sign Locations</a> when ready.");
        //$('#help-dashboard').slideDown('fast');
    }
});
function add(type){
    inventory_type=type;
    edit_id=0;
    edit_type="add";
    if(type=='Material'){
        $('#editModal').height('250px');
        $('#modal-sm-name').html('(e.g. ReBar)');
        $('#modal-options-signs').hide();
    }else if(type=='Sign'){
        $('#modal-sm-name').html('(e.g. XL Sign)');
        $('#modal-options-signs').show();
        $('#editModal').height('280px');
    }
    
    $('#myModalLabel').html('Add Inventory');
    $('#edit-name').val('');
    $('#edit-quantity').val('');
    $('#edit-price').val('');
    $('#edit-num').val('0');
    $('#edit-material').val('0');
    $('#editModal').modal('show');
  }
  function edit(id){
     edit_type="edit";
     $('#myModalLabel').html('Edit Inventory');
     edit_id=id;
     var result = $.grep(_INVENTORY, function(e){return e.inventory_id == id;});
     console.log(result[0]);
     //console.log(result[0]);
     $('#edit-name').val(result[0].name);
     $('#edit-quantity').val(result[0].num_total);
     $('#edit-price').val(result[0].price);
     inventory_type=result[0].inventory_type;
     if(result[0].inventory_type=='Material'){
        $('#editModal').height('250px');
        $('#modal-sm-name').html('(e.g. ReBar)');
        $('#modal-options-signs').hide();
        
    }else if(result[0].inventory_type=='Sign'){
        $('#modal-sm-name').html('(e.g. XL Sign)');
        $('#modal-options-signs').show();
        $('#editModal').height('280px');
        $('#edit-material').val(result[0].material_id);
        $('#edit-num').val(result[0].num_material);
    }
     $('#editModal').modal('show');
  }
  function editSave(){
      loader('show');
     $.ajax(
        {
            url: APPLICATION_URL+"/campaign/handler/format/html",
            data: {id: edit_id,inventory_type:inventory_type, type:edit_type+"Inventory",material_id:$('#edit-material').val(), name:$('#edit-name').val(), num_total:$('#edit-quantity').val(),price:$('#edit-price').val(),num_material:$('#edit-num').val()},
            type: "POST",
            success: function(response){ 
                
                $('#editModal').modal('hide');
                if(isNumber(response)){
                    if(response>0){
                        location.reload();
                    }else if(response==-1){
                        alert('Sorry, there was an error.')
                    }
                }else{
                    //window.location.replace(APPLICATION_URL+'/auth/logout');
                }
            }
        });
  }
 
function drop(id){
    
     edit_id=id;
    var result = $.grep(items, function(e){return e.sign_id == id;});
    edit_item=result[0].address;
    edit_verb="deleted";
    $('#msg-user-delete').html('Are you sure you want to delete sign: <strong><span id="delete-user">'+edit_item+'</span></strong>?');
    $('#deleteModal').modal('show');
}
function editDashboard(field,mode){
    if(mode=='edit'){
        if(field=='election'){
            $('#tr-edit-election').show();
            $('#tr-view-election').hide();
            setDatepicker($('#view-election').html(),'#input-election')
        }else if(field=='notify'){
          toggleEditNotify('edit');
        }
    }else if(mode=='save'){
        msg='';
        if(field=='election'){
            $('#tr-edit-election').hide();
            $('#tr-view-election').show();
            $('#view-election').html($('#input-election').val());
            
        }else if(field=='notify'){
           
           if(!isValidEmailAddress($('#input-email').val())){
                msg='Please enter a valid email address before saving';
            }else{
                $('#view-notify').html($('#input-notify').val()*1);
                $('#view-email').html($('#input-email').val());
                toggleEditNotify('close');
            }
           
        }
        
        if(msg==''){
            var  notifications=0;
        if($('#toggle-notifications div:first-of-type').hasClass('toggle-active')){
            notifications=1;
        }
        $.ajax(
        {
            url: APPLICATION_URL+"/campaign/handler/format/html",
            data: {email: $('#view-email').html(), type:'editCampaign',notifications:notifications,date_election:formatMysqlDate($('#view-election').html()),num_notify:$('#view-notify').html()*1,email:$('#view-email').html()},
            type: "POST",
            success: function(response){ 
                if(isNumber(response)){
                    if(response>0){
                        if(field=='election'){
                            if(field=='election'){
                                var election = formatMysqlDate($('#view-election').html());
                                var today = new Date();
                                var dd = today.getDate();
                                var mm = today.getMonth()+1;
                                var yyyy = today.getFullYear();
                                if(dd<10){
                                    dd='0'+dd
                                } 
                                if(mm<10){
                                    mm='0'+mm
                                } 
                                today = yyyy+'-'+mm+'-'+dd;
                                if(election>today){
                                    $('#display-recover').hide();
                                    $('#display-normal').show();
                                }else{
                                    $('#display-recover').show();
                                    $('#display-normal').hide();
                                }
                            }
                        }
                    }
                }else{
                    //window.location.replace(APPLICATION_URL+'/auth/logout');
                }
            }
        });
    }else{
        alert(msg);
    }
    }else if(mode=='cancel'){
        toggleEditNotify('close');
    }
    
}
function toggleEditNotify(type){
    if(type=='edit'){
        $('#tr-view-notify').hide();
        $('#tr-view-email').hide();
           $('#tr-view-options').hide();
           $('#tr-input-notify').show();
           $('#tr-input-email').show();
           $('#tr-edit-options').show();
    }else{
         $('#tr-view-notify').show();
        $('#tr-view-email').show();
           $('#tr-view-options').show();
           $('#tr-input-notify').hide();
           $('#tr-input-email').hide();
           $('#tr-edit-options').hide();
           $('#tr-edit-election').hide();
            $('#tr-view-election').show();
    }
}
function confirmDelete(){
    loader('show');
    $.ajax(
        {
            url: APPLICATION_URL+"/sign/handler/format/html",
            data: {id: edit_id, type:'delete'},
            type: "POST",
            success: function(response){ 
                $('#deleteModal').modal('hide');
                if(isNumber(response)){
                    if(response>0){
                        get_users();
                        show_msg(1,'');
                    }else if(response==-1){
                        alert('You can not delete yourself.')
                    }
                }else{
                    //window.location.replace(APPLICATION_URL+'/auth/logout');
                }
            }
        });
}

function toggleNotifications(){
    
      if($('#toggle-notifications div:first-of-type').hasClass('toggle-active')){
          $('#toggle-notifications div:first-of-type').removeClass('toggle-active');
          $('#toggle-notifications div:nth-of-type(2)').addClass('toggle-active');
          
      }else{
          $('#toggle-notifications div:first-of-type').addClass('toggle-active');
          $('#toggle-notifications div:nth-of-type(2)').removeClass('toggle-active');
          
      }
      editDashboard('','save');
  }
  
  function formatMysqlDate(fdate){

    return fdate.substr(6,4)+'-'+fdate.substr(0,2)+'-'+fdate.substr(3,2);
}
function resetAllData(){
    $.ajax(
        {
            url: APPLICATION_URL+"/campaign/handler/format/html",
            data: {type:'resetAll'},
            type: "POST",
            success: function(response){ 
               
                location.reload();
            }
        });
}
//set date on pop-out calendar
function setDatepicker(val,dpicker){
    var pubdate = $( dpicker).datepicker('setValue',val).on('changeDate', function() {
        pubdate.hide();
    }).data('datepicker');
    ;
    $( dpicker ).datepicker('place','input');
    
}