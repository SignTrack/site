/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id=0;
var edit_item='';
var edit_verb='';
var edit_type="add";
var fadeout=0;
  $(function () {
      
        $( "#btn-user-add" )
            .click(function() {
                edit_type="add";
                $( "#form-add" ).dialog( "open" );
            });
        get_accounts();

  })
 
  

  function get_accounts(){
      $('#grid-users').load(APPLICATION_URL+'/account/list/format/html');
  }
  
  function show_add(){
    edit_type="add";
    edit_verb="added";
    $('#input-name').val('');
    $('#btn-update').html('Add Account');
    $('#addModal').modal('show');
    $('#myModalLabel').html('Add Account');
    $('#addModal').css('height','240px');
    $('#addModal').css('margin-top','-120px');
      
  }
  
  function edit_account(id,name,has_airline){
      $('#myModalLabel').html('Edit Account');
     edit_type="edit";
     edit_id=id;
        $("#input-name").val(name);
        $("#input-airline").val(has_airline);
        $('#btn-update').html('Save Changes');
        $('#addModal').css('height','240px');
    $('#addModal').css('margin-top','-120px');
        $('#addModal').modal('show');
  }
  function add(){
      var msg = '';
                    if($('#input-name').val()==""){
                        msg = 'Please enter an Account Name.';
                    }
                    if(msg==''){
                        if(edit_type=="edit"){
                            edit_verb="updated";
                        }else{
                            edit_verb="added";
                        }
                        var data = "account_id="+edit_id+"&name="+$('#input-name').val()+"&type="+edit_type+"&has_airline="+$('#input-airline').val();
                        console.log(data);
                        $.ajax(
                        {

                            url: APPLICATION_URL+"/account/handler/format/html",
                            data: data,
                            type: "POST",
                            success: function(response){ 
                                edit_item=$('#input-name').val();
                                if(isNumber(response)){
                                    if(response>=0){
                                        get_accounts();
                                        show_msg(1,'');
                                        $('#addModal').modal('hide');
                                    }else if(response==-1){
                                        alert("The Account Name you are trying to add is already in the system.");
                                    }else{
                                        show_msg(0,'');
                                    }
                                }else{
                                    //window.location.replace(APPLICATION_URL+'/auth/logout');
                                }
                            }
                        });
                    }else{
                        alert(msg);
                    }
  }
  function delete_account(id){
      $('#msg-update').css('opacity','0.0');
      edit_id=id;
    var result = $.grep(accounts, function(e){return e.account_id == id;});
    edit_item=result[0].name;
    edit_verb="deleted";
    $('#msg-user-delete').html('Are you sure you want to delete account <strong><span id="delete-user">'+edit_item+'</span></strong>?');
    $('#deleteModal').modal('show');
}

function confirm_delete(){
    $.ajax(
        {
            url: APPLICATION_URL+"/account/handler/format/html",
            data: {account_id: edit_id, type:'delete'},
            type: "POST",
            success: function(response){ 
                $('#deleteModal').modal('hide');
                if(isNumber(response)){
                    if(response>0){
                        get_accounts();
                        show_msg(1,'');
                    }
                }else{
                    window.location.replace(APPLICATION_URL+'/auth/logout');
                }
            }
        });
}

function show_msg(type,msg){
    $('#msg-update').css('opacity','1.0');
    if(type>0){
        $('#msg-update').html('<div class="alert alert-success">The account, <strong>'+edit_item+'</strong>,  was successfully '+edit_verb+'.</div>'); 
    }else{
        $('#msg-update').html('<div class="alert alert-error">The account, <strong>'+edit_item+'</strong>, could not be '+edit_verb+' due to an error. Please try again later. '+msg+'</div>'); 
    }
    clearTimeout(fadeout);
    fadeout = setTimeout(function() { 
    $('.alert-success,.alert-error').fadeOut(1000);
},  5000);
}