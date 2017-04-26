var show_list;
var sort_type = 'alphabetic';

$(document).ready(function () {
  bootbox.setDefaults({locale:lang['locale-bootbox']});
  loadFolders();
  performLfmRequest('errors')
    .done(function (data) {
      var response = JSON.parse(data);
      for (var i = 0; i < response.length; i++) {
        $('#alerts').append(
          $('<div>').addClass('alert alert-warning')
            .append($('<i>').addClass('fa fa-exclamation-circle'))
            .append(' ' + response[i])
        );
      }
    });
});

// ======================
// ==  Navbar actions  ==
// ======================

$('#nav-buttons a').click(function (e) {
  e.preventDefault();
});

$('#to-previous').click(function () {
  var previous_dir = getPreviousDir();
  if (previous_dir == '') return;
  goTo(previous_dir);
});

$('#add-folder').click(function () {
  bootbox.prompt(lang['message-name'], function (result) {
    if (result == null) return;
    createFolder(result);
  });
});

$('#upload').click(function () {
  $('#uploadModal').modal('show');
});

$('#upload-btn').click(function () {
  $(this).html('')
    .append($('<i>').addClass('fa fa-refresh fa-spin'))
    .append(" " + lang['btn-uploading'])
    .addClass('disabled');

  function resetUploadForm() {
    $('#uploadModal').modal('hide');
    $('#upload-btn').html(lang['btn-upload']).removeClass('disabled');
    $('input#upload').val('');
  }

  $('#uploadForm').ajaxSubmit({
    success: function (data, statusText, xhr, $form) {
      resetUploadForm();
      refreshFoldersAndItems(data);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      displayErrorResponse(jqXHR);
      resetUploadForm();
    }
  });
});

$('#thumbnail-display').click(function () {
  show_list = 0;
  loadItems();
});

$('#list-display').click(function () {
  show_list = 1;
  loadItems();
});

$('#list-sort-alphabetic').click(function() {
  sort_type = 'alphabetic';
  loadItems();
});

$('#list-sort-time').click(function() {
  sort_type = 'time';
  loadItems();
});

// ======================
// ==  Folder actions  ==
// ======================

$(document).on('click', '.file-item', function (e) {
  useFile($(this).data('id'));
});

$(document).on('click', '.folder-item', function (e) {
  goTo($(this).data('id'));
});

function goTo(new_dir) {
  $('#working_dir').val(new_dir);
  loadItems();
}

function getPreviousDir() {
  var ds = '/';
  var working_dir = $('#working_dir').val();
  var last_ds = working_dir.lastIndexOf(ds);
  var previous_dir = working_dir.substring(0, last_ds);
  return previous_dir;
}

function dir_starts_with(str) {
  return $('#working_dir').val().indexOf(str) === 0;
}

function setOpenFolders() {
  var folders = $('.folder-item');

  for (var i = folders.length - 1; i >= 0; i--) {
    // close folders that are not parent
    if (! dir_starts_with($(folders[i]).data('id'))) {
      $(folders[i]).children('i').removeClass('fa-folder-open').addClass('fa-folder');
    } else {
      $(folders[i]).children('i').removeClass('fa-folder').addClass('fa-folder-open');
    }
  }
}

// ====================
// ==  Ajax actions  ==
// ====================

function performLfmRequest(url, parameter, type) {
  var data = defaultParameters();

  if (parameter != null) {
    $.each(parameter, function (key, value) {
      data[key] = value;
    });
  }

  return $.ajax({
    type: 'GET',
    dataType: type || 'text',
    url: lfm_route + '/' + url,
    data: data,
    cache: false
  }).fail(function (jqXHR, textStatus, errorThrown) {
    displayErrorResponse(jqXHR);
  });
}

function displayErrorResponse(jqXHR) {
  notify('<div style="max-height:50vh;overflow: scroll;">' + jqXHR.responseText + '</div>');
}

var refreshFoldersAndItems = function (data) {
  loadFolders();
  if (data != 'OK') {
    data = Array.isArray(data) ? data.join('<br/>') : data;
    notify(data);
  }
};

var hideNavAndShowEditor = function (data) {
  $('#nav-buttons > ul').addClass('hidden');
  $('#content').html(data);
}

function loadFolders() {
  performLfmRequest('folders', {}, 'html')
    .done(function (data) {
      $('#tree').html(data);
      loadItems();
    });
}

function loadItems() {
  performLfmRequest('jsonitems', {show_list: show_list, sort_type: sort_type}, 'html')
    .done(function (data) {
      var response = JSON.parse(data);
      $('#content').html(response.html);
      $('#nav-buttons > ul').removeClass('hidden');
      $('#working_dir').val(response.working_dir);
      $('#current_dir').text(response.working_dir);
      console.log('Current working_dir : ' + $('#working_dir').val());
      if (getPreviousDir() == '') {
        $('#to-previous').addClass('hide');
      } else {
        $('#to-previous').removeClass('hide');
      }
      setOpenFolders();
    });
}

function createFolder(folder_name) {
  performLfmRequest('newfolder', {name: folder_name})
    .done(refreshFoldersAndItems);
}

function rename(item_name) {
  bootbox.prompt({
    title: lang['message-rename'],
    value: item_name,
    callback: function (result) {
      if (result == null) return;
      performLfmRequest('rename', {
        file: item_name,
        new_name: result
      }).done(refreshFoldersAndItems);
    }
  });
}

function trash(item_name) {
  bootbox.confirm(lang['message-delete'], function (result) {
    if (result == true) {
      performLfmRequest('delete', {items: item_name})
        .done(refreshFoldersAndItems);
    }
  });
}

function cropImage(image_name) {
  performLfmRequest('crop', {img: image_name})
    .done(hideNavAndShowEditor);
}

function resizeImage(image_name) {
  performLfmRequest('resize', {img: image_name})
    .done(hideNavAndShowEditor);
}

function download(file_name) {
  var data = defaultParameters();
  data['file'] = file_name;
  location.href = lfm_route + '/download?' + $.param(data);
}

// ==================================
// ==  Ckeditor, Bootbox, preview  ==
// ==================================

function useFile(file_url) {

  function getUrlParam(paramName) {
    var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
    var match = window.location.search.match(reParam);
    return ( match && match.length > 1 ) ? match[1] : null;
  }

  function useTinymce3(url) {
    var win = tinyMCEPopup.getWindowArg("window");
    win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = url;
    if (typeof(win.ImageDialog) != "undefined") {
      // Update image dimensions
      if (win.ImageDialog.getImageData) {
        win.ImageDialog.getImageData();
      }

      // Preview if necessary
      if (win.ImageDialog.showPreviewImage) {
        win.ImageDialog.showPreviewImage(url);
      }
    }
    tinyMCEPopup.close();
  }

  function useTinymce4AndColorbox(url, field_name) {
    parent.document.getElementById(field_name).value = url;

    if(typeof parent.tinyMCE !== "undefined") {
      parent.tinyMCE.activeEditor.windowManager.close();
    }
    if(typeof parent.$.fn.colorbox !== "undefined") {
      parent.$.fn.colorbox.close();
    }
  }

  function useCkeditor3(url) {
    if (window.opener) {
      // Popup
      window.opener.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), url);
    } else {
      // Modal (in iframe)
      parent.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), url);
      parent.CKEDITOR.tools.callFunction(getUrlParam('CKEditorCleanUpFuncNum'));
    }
  }

  function useFckeditor2(url) {
    var p = url;
    var w = data['Properties']['Width'];
    var h = data['Properties']['Height'];
    window.opener.SetUrl(p,w,h);
  }

  var url = file_url;
  var field_name = getUrlParam('field_name');
  var is_ckeditor = getUrlParam('CKEditor');
  var is_fcke = typeof data != 'undefined' && data['Properties']['Width'] != '';
  var file_path = url.replace(route_prefix, '');

  if (window.opener || window.tinyMCEPopup || field_name || getUrlParam('CKEditorCleanUpFuncNum') || is_ckeditor) {
    if (window.tinyMCEPopup) { // use TinyMCE > 3.0 integration method
      useTinymce3(url);
    } else if (field_name) {   // tinymce 4 and colorbox
      useTinymce4AndColorbox(url, field_name);
    } else if(is_ckeditor) {   // use CKEditor 3.0 + integration method
      useCkeditor3(url);
    } else if (is_fcke) {      // use FCKEditor 2.0 integration method
      useFckeditor2(url);
    } else {                   // standalone button or other situations
      window.opener.SetUrl(url, file_path);
    }

    if (window.opener) {
      window.close();
    }
  } else {
    // No WYSIWYG editor found, use custom method.
    window.opener.SetUrl(url, file_path);
  }
}
//end useFile

function defaultParameters() {
  return {
    working_dir: $('#working_dir').val(),
    type: $('#type').val()
  };
}

function notImp() {
  bootbox.alert('Not yet implemented!');;
}

function notify(message) {
  bootbox.alert(message);
}

function fileView(file_url, timestamp) {
  var rnd = makeRandom();
  bootbox.dialog({
    title: lang['title-view'],
    message: $('<img>')
      .addClass('img img-responsive center-block')
      .attr('src', file_url + '?timestamp=' + timestamp),
    size: 'large',
    onEscape: true,
    backdrop: true
  });
}

function makeRandom() {
  var text = '';
  var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

  for (var i = 0; i < 20; i++) {
    text += possible.charAt(Math.floor(Math.random() * possible.length));
  }
  return text;
}
