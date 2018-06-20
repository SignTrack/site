/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id = 0;
var edit_item = '';
var edit_verb = '';
var edit_type = "add";
var fadeout = 0;
var currMarkers = [];
var markerId;
var assignId = 0;
var newMarkers = [];
var markerCluster;
var assignMarkers = [];
var map;
var filteredItems = [];
var trafficLayer;
$(function () {
    $('#editModal').on('hidden.bs.modal', function () {
        loader('hide');
    })
    filteredItems = items;
    getMap();

    $('#select-status,#select-volunteer,#select-size').change(function () {
        filterItems();
    });
    $('.modal').on('hidden.bs.modal', function () {
        loader('hide');
    })
    $('.modal .btn').click(function () {
        if($(this).attr('id')!='btn-upload-file' && $(this).attr('id')!='btn-upload-back'){
            loader('show');
        }
    });
    $('#upload-status').on('change',function(e){
      
       if($(this).val()=='OK'){
           $("#upload-materials").css('visibility','visible');
       }else{
           $("#upload-materials").css('visibility','hidden');
           $('#inv-num-material-upload').val(0);
       }
    });
})


function getList() {
    dataView = new Slick.Data.DataView();
    initGrid();
}
function getMap() {
    $('#signtable').css('opacity', 1);
    currMarkers = [];
    var lat = CAMPAIGN.lat;
    var lng = CAMPAIGN.lng;
    var styles = [
        {
        }, {
            "featureType": "poi",
            "stylers": [
                {"visibility": "off"}
            ]
        }, {
            "featureType": "transit",
            "stylers": [
                {"visibility": "off"}
            ]
        }
    ];
    var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});
    var mapOptions = {};
    map = new google.maps.Map(document.getElementById("map"),
            mapOptions);
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
    var latlngbounds = new google.maps.LatLngBounds();
    latlngbounds.extend(new google.maps.LatLng(CAMPAIGN.nelat, CAMPAIGN.nelng));
    latlngbounds.extend(new google.maps.LatLng(CAMPAIGN.swlat, CAMPAIGN.swlng));
    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
        console.log('test');
        map.setZoom(map.getZoom() + 1);
      });
    map.fitBounds(latlngbounds);
    trafficLayer = new google.maps.TrafficLayer();
    trafficLayer.setMap(map);
    setMarkers();
}
function setMarkers() {
    currMarkers = [];
    $('#num-records').html(filteredItems.length + ' Signs');
    for (var i = 0; i < filteredItems.length; i++) {



        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(filteredItems[i].lat, filteredItems[i].lng),
            map: map,
            icon: APPLICATION_URL + '/images/markers/' + filteredItems[i].status + '.svg',
            id: filteredItems[i].sign_id
        });

        var infowindow = new google.maps.InfoWindow();
        var sign_id = filteredItems[i].sign_id;
        google.maps.event.addListener(marker, 'click', (function (marker, sign_id) {
            var id_dashes = formatId(filteredItems[i].sign_id);
            if (!filteredItems[i].name || filteredItems[i].user_id==0) {
                filteredItems[i].name = 'Unassigned';
            }
            var contentString = '<div id="info' + filteredItems[i].sign_id + '" style="background-color:#fff; height:65px; width:180px;">' +
                    '<div class="info-id">Sign ID: ' + id_dashes + '</div><div class="info-address">' + filteredItems[i].name + '</div><div><a href="javascript:edit(' + filteredItems[i].sign_id + ',' + i + ')">Edit</a> | <a id href="javascript:drop(' + filteredItems[i].sign_id + ',' + i + ')">Delete</a></div>' +
                    '</div>';
            return function () {
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            }
        })(marker, sign_id));

        currMarkers.push(marker);

    }
    markerCluster = new MarkerClusterer(map, currMarkers, {maxZoom: 13});
}
function addMarkers() {
    newMarkers = [];

    google.maps.event.addListener(map, 'click', function (event) {
        placeMarker(event.latLng);

    });


}

function placeMarker(location) {
    var trying = items.length + newMarkers.length;
    if (trying < SIGNLIMIT * 1) {
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            icon: APPLICATION_URL + '/images/markers/Add.svg'
        });
        marker.metadata = {
            type: "add"
        };
        newMarkers.push(marker);
    } else {
        alert('Sorry, you have reached your limit of sign locations.');
    }

}

function saveMarkers() {
    var addSigns = [];
    for (var i = 0; i < newMarkers.length; i++) {
        addSigns.push({
            lat: newMarkers[i].getPosition().lat(),
            lng: newMarkers[i].getPosition().lng(),
            user_id: $('#input-user').val(),
            inventory_id: $('#input-inventory').val()
        })
    }
    $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    type: 'add',
                    signs: addSigns
                },
                type: "POST",
                success: function (response) {

                    showSignStep(0);
                    items = jQuery.parseJSON(response);
                    filteredItems = items;
                    getMap();
                }
            });
}
function showSignStep(num) {
    if (!$('#toggleMapList div:first-of-type').hasClass('toggle-active')) {
        toggleMapList();
    }
    $('#content-options').hide();
    $('.filters').hide();
    if (num > 0) {


        if (num == 3) {
            //validate items entered
            var msg = '';
            if ($('#input-inventory').val() * 1 < 1) {
                msg = 'Please enter a sign type before continuing.';
            }
            if (msg == '') {
                $('#sign-step' + num).show();
                var id = $('#input-inventory').val();
                var result = $.grep(inventory, function (e) {
                    return e.inventory_id == id;
                });
                num_left = result[0]['num_total'] - result[0]['num_used'];
                addMarkers();
            } else {
                $('#sign-step2').show();
                alert(msg);
            }

        } else if (num == 4) {
            google.maps.event.clearListeners(map, 'click');
            if (newMarkers.length > 0) {
                $('#signtable').css('opacity', .4).css('pointer-events', 'none');
                $('.filters').hide();
                $('#layout-header').append('<div id="div-load"><img src="' + APPLICATION_URL + '/images/ajax-loader.gif" style="width:25px;margin:20px 15px;"><div style="color:#888;display:inline-block">Saving...</div></div>');
                saveMarkers();
            } else {
                showSignStep(0);
            }
        } else if (num == 5) {
            $('#btn-address-done').html('Cancel');
            $('#sign-step' + num).show();
        }else {
            $('#sign-step' + num).show();
        }
    } else {
        $('#div-load').remove();
        $('#signtable').css('opacity', 1).css('pointer-events', 'all');
        google.maps.event.clearListeners(map, 'click');
        $('#content-options').show();
        $('#sign-filters').show();
        for (i = 0; i < newMarkers.length; i++) {
            newMarkers[i].setMap(null);
        }
    }
}
function uploadBack(){
    $('#upload-step2').hide();
    $('#upload-step1').show();
    $('#file-select').val(null);
    $('.btn-upload1').show();
    $('.btn-upload2').hide();
}
function uploadList() {
    uploadBack();
    $('#uploadModal').modal('show');
    var form = document.getElementById('file-form');
    var fileSelect = document.getElementById('file-select');
    $('#btn-upload-file').unbind().on('click',function(e){
        $('#file-form').trigger('submit');
    });
    form.onsubmit = function (event) {
        event.preventDefault();
        
        // Update button text.


        var files = fileSelect.files;
        var formData = new FormData();

        if(files.length>0){
            loader('show');
        var file = files[0];
        // Check the file type.
        // Add the file to the request.
        formData.append('file', file, file.name);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', APPLICATION_URL + '/php/list.php', true);

        xhr.onload = function () {
            if (xhr.response === '1') {
                $.ajax(
                        {
                            url: APPLICATION_URL + "/sign/handler/format/html",
                            data: {
                                type: 'listupload',
                                inventory_id: $('#upload-sign').val(),
                                user_id: $('#upload-user').val(),
                                status: $('#upload-status').val(),
                                material_id:  $('#inv-material-upload').val(),
                                num_materials:  $('#inv-num-material-upload').val()
                            },
                            type: "POST",
                            success: function (response) {
                                loader('hide');
                                $('.btn-upload2').show();
                                $('.btn-upload1').hide();
                                $('#upload-step1').hide();
                                $('#upload-step2').show();
                                  
                                  response = jQuery.parseJSON(response);
                                  $('#msg-error').html('');
                                  if(response.added.length>0){
                                      $('#msg-success').html(response.added.length+' Addresses added successfully.');
                                      
                                      for(i=0;i<response.added.length;i++){
                                          items.push(response.added[i]);
                                        }
                                      resetSearch();
                                  }
                                  var html = '';
                                  html+='<table style="border-spacing: 10px;border-collapse: separate;">';
                                  if(response.notadded.length>0){
                                      $('#msg-error').html(response.notadded.length+' Addresses had errors.');
                                      
                                      html+='<tr><td colspan="3"><strong>Addresses with errors:</strong></td></tr>';
                                      for(i=0;i<response.notadded.length;i++){
                                          html+='<tr><td>'+(i+1)+'</td><td>'+response.notadded[i]['address']+'</td><td>'+response.notadded[i]['zip']+'</td></tr>';
                                      }
                                  }
                                  if(response.over.length>0){
                                      if($('#msg-error').html()!=''){
                                          $('#msg-error').append('<br/>');
                                      }
                                      $('#msg-error').append(response.over.length+' Addresses were over your limit.');
                                      html+='<tr><td colspan="3"><strong>Addresses over limit:</strong></td></tr>';
                                      for(i=0;i<response.over.length;i++){
                                          html+='<tr><td>'+(i+1)+'</td><td>'+response.over[i]['address']+'</td><td>'+response.over[i]['zip']+'</td></tr>';
                                      }
                                  }
                                  
                                  if(response.over.length==0 && response.notadded.length==0  && response.added.length==0){
                                      $('#msg-error').html('No valid addresses were found. Please check the formatting of your CSV file.');
                                  }
                                  html+='</table>';
                                  $('#upload-records').html(html);

                            }
                        });
            } else {
                loader('hide');
                alert('An error occurred!');
            }
        };
        formData.append('inventory_id', $('#upload-sign').val());
        xhr.send(formData);
}else{
        alert("Please select a file for import first.");
    }

  }
    
}

function addLocationByAddress(){
        //validate items entered
        var msg = '';
        if ($('#add-inventory').val() * 1 < 1) {
            msg = 'Please enter a sign type before continuing.';
        }else if ($('#add-address').val() ==''){
            msg = 'Please enter an address.';
        }else if ($('#add-address').val()==''){
            msg = 'Please enter a zip/postal code.';
        }
        if (msg === '') {
            loader('show');
            
            $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    type: 'address',
                    inventory_id: $('#add-inventory').val(),
                    address: $('#add-address').val(),
                    zip: $('#add-zip').val(),
                    user_id: $('#add-user').val(),
                    name:$('#add-user option:selected').html()
                },
                type: "POST",
                success: function (response) {
                    loader('hide');
                    $('#btn-address-done').html('Done');
                    response = jQuery.parseJSON(response);
                    if(response.err_msg==''){
                        map.panTo(new google.maps.LatLng(response.lat, response.lng));
                        map.setZoom(18);
                        
                        items.push(response);
                        
                        resetSearch();
                        
                        $('#add-address').val('');
                        $('#add-zip').val('');
                    }else{
                        loader('hide');
                        alert(response.err_msg);
                    }
                     
                }
            });
        }else {
                alert(msg);
            }
}
function showTaskStep(num) {
    if (USERS.length > 1) {
        if (!$('#toggleMapList div:first-of-type').hasClass('toggle-active')) {
            toggleMapList();
        }
        $('#content-options').hide();
        $('.filters').hide();
        if (num == 3) {
            if (assignMarkers > 0) {
                saveAssigned();
                $('#signtable').css('opacity', .4).css('pointer-events', 'none');
                $('#layout-header').append('<div id="div-load"><img src="' + APPLICATION_URL + '/images/ajax-loader.gif" style="width:25px;margin:20px 15px;"><div style="color:#888;display:inline-block">Saving...</div></div>');
            } else {
                showTaskStep(0);
            }

        } else if (num > 0) {
            $('#task-step' + num).show();
            if (num == 2) {
                assignId = $('#input-assign').val();
                selectMarkers();
            }
        } else {
            $('#div-load').remove();
            $('#content-options').show();
            $('#sign-filters').show();
            assignMarkers = [];
            clearMarkers();
        }
    } else {
        alert('You must add Team Members before you can assign tasks.');
    }
}
function saveAssigned() {
    $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    type: 'assign',
                    signs: assignMarkers,
                    user_id: assignId
                },
                type: "POST",
                success: function (response) {
                    assignMarkers = [];
                    items = jQuery.parseJSON(response);
                    filteredItems = items;
                    getMap();
                    showTaskStep(0);
                }
            });
}
function edit(id, marker) {
    $('#editModal').css('height', '220px');
    $('#row-replace').hide();
    $('#row-replace2').hide();
    markerId = marker;
    edit_type = "edit";
    edit_id = id;
    var result = $.grep(items, function (e) {
        return e.sign_id == id;
    });

    $('#edit-address').val(result[0].address);
    $('#edit-user').val(result[0].user_id);
    $('#edit-status').html('<option value="Place">Place Sign</option><option value="OK">Sign Placed</option><option value="OK">Sign OK</option><option value="Replace">Replace Sign</option><option value="OK">Sign Replaced</option><option value="Fix">Fix Sign</option><option value="OK">Sign Fixed</option><option value="Recover">Recover Sign</option><option value="Recovered">Sign Recovered</option>');
    $('#edit-status').val(result[0].status);
    $('#note').val(result[0].note);
    $('#editModal').modal('show');
    $('#inv-sign').val(result[0].num_material);
    setStatusOptions(result[0].status);
}
function editSave() {
    loader('show');
    $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    id: edit_id,
                    type: 'edit',
                    address: $('#edit-address').val(),
                    sign_id: $('#inv-sign').val(),
                    material_id: $('#inv-material').val(),
                    num_signs: $('#inv-num-sign').val(),
                    num_materials: $('#inv-num-material').val(),
                    status: $('#edit-status').val(),
                    note: $('#note').val(),
                    actionlog: $('#edit-status option:selected').html(),
                    user_id: $('#edit-user').val()
                },
                type: "POST",
                success: function (response) {
                    console.log(response);
                    if (isNumber(response)) {
                        if (response > 0) {
                            var result = $.grep(items, function (e) {
                                return e.sign_id == edit_id;
                            });
                            result[0].address = $('#edit-address').val();
                            result[0].status = $('#edit-status').val();
                            result[0].user_id = $('#edit-user').val();
                            result[0].inventory_id = $('#inv-sign').val();
                            result[0].note = $('#note').val();
                            result[0].name = $('#edit-user option:selected').html();
                            if ($('#edit-status').val() == "Recovered") {
                                items = $.grep(items, function (e) {
                                    return e.sign_id != result[0].sign_id;
                                });
                            }
                            $('#editModal').modal('hide');
                            filterItems();

                        } else if (response == -1) {
                            alert('Sorry, there was an error.');
                        }
                    } else {
                        alert('Sorry, there was an error.');
                    }
                }
            });
}
function selectMarkers() {

    for (var i = 0; i < currMarkers.length; i++) {
        google.maps.event.clearListeners(currMarkers[i], 'click');

        google.maps.event.addListener(currMarkers[i], "click", function (e) {
            var sign_id = this.id;
            var result = $.grep(items, function (e) {
                return e.sign_id == sign_id;
            });
            var status = result[0].status;
            var result2 = $.grep(assignMarkers, function (e) {
                return e.sign_id == sign_id;
            });
            if (result2.length > 0) {
                console.log('remove');
                result3 = $.grep(assignMarkers, function (e) {
                    return e.sign_id != sign_id;
                });
                assignMarkers = result3;
                this.setIcon(APPLICATION_URL + '/images/markers/' + status + '.svg');
            } else {
                console.log('add');
                assignMarkers.push(result[0]);
                this.setIcon(APPLICATION_URL + '/images/markers/' + status + '-checked.svg');
            }

        });
    }
}
function toggleMapList() {
    if ($('#toggleMapList div:first-of-type').hasClass('toggle-active')) {
        $('#toggleMapList div:first-of-type').removeClass('toggle-active');
        $('#toggleMapList div:nth-of-type(2)').addClass('toggle-active');
        $('#map').hide();
        $('#tabs-1').show();
        if ($('#itemGrid').html() == '') {
            getList();
        }
    } else {
        $('#toggleMapList div:first-of-type').addClass('toggle-active');
        $('#toggleMapList div:nth-of-type(2)').removeClass('toggle-active');
        $('#map').show();
        $('#tabs-1').hide();
    }
}
function toggleTrafficLayer(){
    
    if ($('#toggleTrafficLayer div:first-of-type').hasClass('toggle-active')) {
        $('#toggleTrafficLayer div:first-of-type').removeClass('toggle-active');
        $('#toggleTrafficLayer div:nth-of-type(2)').addClass('toggle-active');
        console.log('hide');
        trafficLayer.setMap(null);
    } else {
        $('#toggleTrafficLayer div:first-of-type').addClass('toggle-active');
        $('#toggleTrafficLayer div:nth-of-type(2)').removeClass('toggle-active');
       
        trafficLayer.setMap(map);
    }
}
function get_items() {
    $('#grid-list').load(APPLICATION_URL + '/log/list/format/html');
}
function evalStatus() {
    var result = $.grep(items, function (e) {
        return e.sign_id == edit_id;
    });
    $('#inv-num-sign').val(0);
    $('#inv-num-material').val(0);
    $('#inv-material').val(result[0]['material_id']);
    $('#inv-sign').val(result[0]['inventory_id']);

    if ($("#edit-status option:selected").text() === 'Sign Replaced' || $("#edit-status option:selected").text() === 'Sign Placed' || $("#edit-status option:selected").text() === 'Sign Fixed' || $('#edit-status').val() === "Recovered") {
        $('#editModal').css('height', '340px');
        $('#row-replace').show();
        $('#row-replace2').show();


        if ($('#edit-status').val() == "Recovered") {
            $('#lbl-material-used').html('Material Recovered');
            $('#lbl-sign-used').html('Sign Recovered');
            var html = '';
            for (i = 0; i <= 1; i++) {
                html += '<option value="' + i + '">' + i + '</option>';
            }
            $('#inv-num-sign').html(html);
            var html = '';
            for (i = 0; i <= result[0]['num_material'] * 1; i++) {
                html += '<option value="' + i + '">' + i + '</option>';
            }
            $('#inv-num-material').html(html);

        } else if ($('#edit-status').val() == "OK") {
            $('#lbl-material-used').html('Material Used');
            $('#lbl-sign-used').html('Sign Used');


            //populate sign quantity
            var html = '';
            for (i = 0; i <= 3; i++) {
                html += '<option value="' + i + '">' + i + '</option>';
            }
            $('#inv-num-sign').html(html);

            //populate inventory quantity
            var html = '';
            for (i = 0; i <= 6; i++) {
                html += '<option value="' + i + '">' + i + '</option>';
            }
            $('#inv-num-material').html(html);

            if (result[0]['status'] == 'Place' || result[0]['status'] == 'Replace') {
                $('#inv-num-material').val(result[0]['num_material']);
                $('#inv-num-sign').val('1');
            } else {
                $('#inv-num-sign').val('0');
                $('#inv-num-material').val('0');

            }


        }
    } else {
        $('#editModal').css('height', '220px');
        $('#row-replace').hide();
        $('#row-replace2').hide();
    }
}
function drop(id, marker) {
    markerId = marker;
    edit_id = id;
    var result = $.grep(items, function (e) {
        return e.sign_id == id;
    });
    edit_item = formatId(result[0].sign_id);
    edit_verb = "deleted";
    $('#msg-user-delete').html('Are you sure you want to delete sign: <strong><span id="delete-user">' + edit_item + '?</span></strong><span style="color:#CC0000"> The sign and materials used will not go back into inventory. If items are recovered from this location, please choose "Edit" and update status to “Recovered.”</span>');
    $('#deleteModal').modal('show');
}
function confirmDelete() {
    loader('show');
    $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    id: edit_id,
                    type: 'delete'
                },
                type: "POST",
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    if (isNumber(response)) {
                        if (response > 0) {
                            currMarkers[markerId].setMap(null);

                            var result = $.grep(items, function (e) {
                                return e.sign_id != edit_id;
                            });
                            items = result;
                            filterItems();
                        } else if (response == -1) {
                            alert('You can not delete yourself.')
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


//grid
var dataView;
var grid;
var RowNumberFormatter = function (row, cell, value, columnDef, dataContext) {
    return row + 1;
};
var columns = [
    {
        id: "rowNumber",
        name: "",
        field: "",
        formatter: RowNumberFormatter,
        width: 60,
        cannotTriggerInsert: true,
        resizable: false,
        unselectable: true,
        sortable: false,
        enableColumnReorder: true
    },
    {
        id: "sign_id",
        name: "Sign ID",
        field: "sign_id",
        width: 120,
        sortable: true,
        formatter: Slick.Formatters.SignIdFormatter
    },
    {
        id: "status",
        name: "Task",
        field: "status",
        width: 100,
        sortable: true
    },
    {
        id: "name",
        name: "Name",
        field: "name",
        width: 140,
        sortable: true
    },
    {
        id: "date_last",
        name: "Last Update",
        field: "date_last",
        width: 160,
        sortable: true,
        formatter: Slick.Formatters.LastUpdateFormatter
    },
    {id: "action", name: "Action", field: "", width: 160, sortable: true, formatter: Slick.Formatters.SignOptions}
];

var options = {
    rowHeight: 40,
    enableCellNavigation: false,
    enableColumnReorder: false,
    multiColumnSort: true
};

function initGrid() {

    dataView.beginUpdate();
    dataView.setItems(filteredItems, 'sign_id');
    dataView.endUpdate();

    grid = new Slick.Grid($("#itemGrid"), dataView, columns, options);

    grid.onSort.subscribe(function (e, args) {
        console.log("testing");
        var cols = args.sortCols;

        dataView.sort(function (dataRow1, dataRow2) {
            for (var i = 0, l = cols.length; i < l; i++) {
                var field = cols[i].sortCol.field;
                var sign = cols[i].sortAsc ? 1 : -1;
                var value1 = dataRow1[field], value2 = dataRow2[field];
                var result = (value1 == value2 ? 0 : (value1 > value2 ? 1 : -1)) * sign;
                if (result != 0) {
                    return result;
                }
            }
            return 0;
        });
        grid.invalidate();
        grid.render();


    });
}

function clearMarkers() {
    for (var i = 0; i < currMarkers.length; i++) {
        currMarkers[i].setMap(null);
    }
    markerCluster.clearMarkers();
    setMarkers();
}


function filterItems() {
    $('#input-sign').val('');
    filteredItems = items;
    if ($('#select-status').val() != '') {
        filteredItems = $.grep(filteredItems, function (e) {
            return e.status == $('#select-status').val();
        });
    }
    if ($('#select-volunteer').val() != '') {
        filteredItems = $.grep(filteredItems, function (e) {
            return e.user_id == $('#select-volunteer').val();
        });
    }
    if ($('#select-size').val() != '') {
        filteredItems = $.grep(filteredItems, function (e) {
            return e.inventory_id == $('#select-size').val();
        });
    }
    clearMarkers();
    if ($('#itemGrid').html() != '') {
        dataView.beginUpdate();
        dataView.setItems(filteredItems, 'sign_id');
        dataView.endUpdate();
        grid.invalidate();
        grid.render();
    }
    $('#num-records').html(filteredItems.length + ' Signs');

}
function formatId(id) {
    return id.substring(0, 3) + '-' + id.substring(3, 6) + '-' + id.substring(6, 9);
}
function resetSearch() {
    $('#input-sign').val('');
    $('#select-size').val('');
    $('#select-status').val('');
    $('#select-volunteer').val('');
    filterItems();
    getSearch();
}
function getSearch() {
    var num = 0;
    filteredItems = items;
    $('#select-volunteer').val('');
    $('#select-size').val('');
    $('#select-status').val('');
    if ($('#input-sign').val() != '') {
        //take out dashes if they exist
        var id = $('#input-sign').val();
        var strippedId = id.replace(/\D/g, '');

        //search signs
        filteredItems = $.grep(filteredItems, function (e) {
            return e.sign_id == strippedId;
        });

        //update display indicating number of matches
        if (filteredItems.length == 1) {
            $('#num-records').html('1 Sign');
        } else {
            $('#num-records').html('No Signs Found');
        }

        //update map
        clearMarkers();
        map.setCenter(new google.maps.LatLng(filteredItems[0].lat, filteredItems[0].lng));
        map.setZoom(18);

        //update list view
        dataView.beginUpdate();
        dataView.setItems(filteredItems, 'sign_id');
        dataView.endUpdate();
        grid.invalidate();
        grid.render();

    } else {
        filteredItems = items;
        markerCluster.clearMarkers();
        setMarkers();
    }
}
/**
 * Set the logical default status on the edit page based on what the current 
 *
 * @type string
 */
function setStatusOptions(status) {
    if (status) {
        $('#edit-status').val(status);
        if (status != 'OK') {
            $("#edit-status option").filter(function () {
                return this.text == 'Sign OK';
            }).remove();
        } else {
            $("#edit-status option").filter(function () {
                return this.text == 'Sign OK';
            }).attr('selected', true);
        }
        if (status != 'Place') {
            $("#edit-status option").filter(function () {
                return this.text == 'Sign Placed';
            }).remove();
            $("#edit-status option").filter(function () {
                return this.text == 'Place Sign';
            }).remove();
        }
    }
}
function setDefaultMap() {
    $.ajax(
            {
                url: APPLICATION_URL + "/sign/handler/format/html",
                data: {
                    type: 'setMap',
                    nelat: map.getBounds().getNorthEast().lat(),
                    nelng: map.getBounds().getNorthEast().lng(),
                    swlat: map.getBounds().getSouthWest().lat(),
                    swlng: map.getBounds().getSouthWest().lng(),
                },
                type: "POST",
                success: function (response) {
                    $('.msg-map').remove();
                    if(response==1){
                        $('#map').append('<div class="msg-map">Map view has been saved.</div>');
                    }else{
                        $('#map').append('<div class="msg-map msg-red">There was an error saving. Please try again later.</div>');
                    }
                    $('.msg-map').fadeIn('fast',function(){
                        setTimeout(function(){
                            $('.msg-map').fadeOut('fast',function(){
                                $('.msg-map').remove();
                            })
                        },2000);
                    })
                }
            });
}
function showHideFilters() {
    if ($('#all-filters').css('display') == 'none') {
        $('#all-filters').css('left', $('#btn-filters').offset().left);
        $('#all-filters').css('top', $('#btn-filters').offset().top + 40);
        $('#all-filters').show();
    } else {
        $('#all-filters').hide();
    }
}