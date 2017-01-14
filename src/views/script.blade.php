<script>
$(document).ready(function () {
  bootbox.setDefaults({locale:"{{ $lang['locale-bootbox'] }}"});
  loadFolders();
});

// ======================
// ==  Navbar actions  ==
// ======================

$('#nav-buttons a').click(function (e) {
  e.preventDefault();
});

$('#to-previous').click(function () {
  var ds = '/';
  var working_dir = $('#working_dir').val();
  var last_ds = working_dir.lastIndexOf(ds);
  var previous_dir = working_dir.substring(0, last_ds);
  if (previous_dir == '') return;
  goTo(previous_dir);
});

$('#add-folder').click(function () {
  bootbox.prompt("{{ $lang['message-name'] }}", function (result) {
    createFolder(result);
  });
});

$('#upload-btn').click(function () {
  var options = {
    beforeSubmit:  showRequest,
    success:       showResponse,
    error:         showError
  };

  function showRequest(formData, jqForm, options) {
    $('#upload-btn').html("<i class='fa fa-refresh fa-spin'></i> {{ $lang['btn-uploading'] }}");
    return true;
  }

  function showResponse(responseText, statusText, xhr, $form)  {
    $('#uploadModal').modal('hide');
    $('#upload-btn').html("{{ $lang['btn-upload'] }}");
    if (responseText != 'OK'){
      notify(responseText);
    }
    $('input#upload').val('');
    loadItems();
  }

  function showError(jqXHR, textStatus, errorThrown) {
    $('#upload-btn').html("{{ $lang['btn-upload'] }}");
    if (jqXHR.status == 413) {
      notify("{{ $lang['error-too-large'] }}");
    } else if (textStatus == 'error') {
      notify("{{ $lang['error-other'] }}" + errorThrown);
    } else {
      notify("{{ $lang['error-other'] }}" + textStatus + '<br>' + errorThrown);
    }
  }

  $('#uploadForm').ajaxSubmit(options);
  return false;
});

$('#thumbnail-display').click(function () {
  $('#show_list').val(0);
  loadItems();
});

$('#list-display').click(function () {
  $('#show_list').val(1);
  loadItems();
});

// ======================
// ==  Folder actions  ==
// ======================

$(document).on('click', '.folder-item', function (e) {
  goTo($(this).data('id'));
});

function goTo(new_dir) {
  $('#working_dir').val(new_dir);
  loadItems();
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

function loadFolders() {
  performLfmRequest('{{ route("unisharp.lfm.getFolders") }}', {}, 'html')
    .done(function (data) {
      $('#tree').html(data);
      loadItems();
    });
}

function loadItems() {
  console.log('Current working_dir : ' + $('#working_dir').val());

  performLfmRequest('{{ route("unisharp.lfm.getItems") }}', {show_list: $('#show_list').val()}, 'html')
    .done(function (data) {
      $('#content').html(data);
      $('#nav-buttons').removeClass('hidden');
      setOpenFolders();
    });
}

function createFolder(folder_name) {
  performLfmRequest('{{ route("unisharp.lfm.getAddfolder") }}', {name: folder_name})
    .done(refreshFoldersAndItems);
}

function rename(item_name) {
  bootbox.prompt({
    title: "{{ $lang['message-rename'] }}",
    value: item_name,
    callback: function (result) {
      if (result !== null) {
        performLfmRequest('{{ route("unisharp.lfm.getRename") }}', {
          file: item_name,
          new_name: result
        }).done(refreshFoldersAndItems);
      }
    }
  });
}

function trash(item_name) {
  bootbox.confirm("{{ $lang['message-delete'] }}", function (result) {
    if (result == true) {
      performLfmRequest('{{ route("unisharp.lfm.getDelete") }}', {items: item_name})
        .done(refreshFoldersAndItems);
    }
  });
}

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
    url: url,
    data: data,
    cache: false
  });
}

var refreshFoldersAndItems = function (data) {
  if (data == 'OK') {
    loadFolders();
  } else {
    notify(data);
  }
};

var hideNavAndShowEditor = function (data) {
  $('#nav-buttons').addClass('hidden');
  $('#content').html(data);
}

function cropImage(image_name) {
  performLfmRequest('{{ route("unisharp.lfm.getCrop") }}', {img: image_name})
    .done(hideNavAndShowEditor);
}

function resizeImage(image_name) {
  performLfmRequest('{{ route("unisharp.lfm.getResize") }}', {img: image_name})
    .done(hideNavAndShowEditor);
}

function download(file_name) {
  var data = defaultParameters();
  data['file'] = file_name;
  location.href = '{{ route("unisharp.lfm.getDownload") }}?' + $.param(data);
}

// ==================================
// ==  Ckeditor, Bootbox, preview  ==
// ==================================

function useFile(file) {

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

  var url = getFileUrl(file);
  var field_name = getUrlParam('field_name');
  var is_ckeditor = getUrlParam('CKEditor');
  var is_fcke = typeof data != 'undefined' && data['Properties']['Width'] != '';

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
      window.opener.SetUrl(url);
    }

    if (window.opener) {
      window.close();
    }
  } else {
    // No WYSIWYG editor found, use custom method.
    window.opener.SetUrl(url);
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

function getFileUrl(file) {
  return $("[id=\"" + file + "\"]").data('url');
}

function fileView(file) {
  var rnd = makeRandom();
  bootbox.dialog({
    title: "{{ $lang['title-view'] }}",
    message: $('<img>')
      .addClass('img img-responsive center-block')
      .attr('src', getFileUrl(file)),
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

</script>
