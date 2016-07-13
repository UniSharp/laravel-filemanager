<script>
var ds            = '/';
var home_dir      = ds + "{{ (Config::get('lfm.allow_multi_user')) ? Auth::user()->user_field : '' }}";
var shared_folder = ds + "{{ Config::get('lfm.shared_folder_name') }}";
var image_url     = "{{ asset(Config::get('lfm.images_url')) }}";
var file_url      = "{{ asset(Config::get('lfm.files_url')) }}";

$(document).ready(function () {
  bootbox.setDefaults({locale:"{{ Lang::get('laravel-filemanager::lfm.locale-bootbox') }}"});
  // load folders
  loadFolders();
  loadItems();
  setOpenFolders();
});

// ======================
// ==  Navbar actions  ==
// ======================

$('#nav-buttons a').click(function (e) {
  e.preventDefault();
});

$('#to-previous').click(function () {
  var working_dir = $('#working_dir').val();
  var last_ds = working_dir.lastIndexOf(ds);
  var previous_dir = working_dir.substring(0, last_ds);
  $('#working_dir').val(previous_dir);
  loadItems();
  setOpenFolders();
});

$('#add-folder').click(function () {
  bootbox.prompt("{{ Lang::get('laravel-filemanager::lfm.message-name') }}", function (result) {
    if (result !== null) {
      createFolder(result);
    }
  });
});

$('#upload-btn').click(function () {
  var options = {
    beforeSubmit:  showRequest,
    success:       showResponse,
    error:         showError
  };

  function showRequest(formData, jqForm, options) {
    $('#upload-btn').html('<i class="fa fa-refresh fa-spin"></i> {{ Lang::get("laravel-filemanager::lfm.btn-uploading") }}');
    return true;
  }

  function showResponse(responseText, statusText, xhr, $form)  {
    $('#uploadModal').modal('hide');
    $('#upload-btn').html('{{ Lang::get("laravel-filemanager::lfm.btn-upload") }}');
    if (responseText != 'OK'){
      notify(responseText);
    }
    $('#upload').val('');
    loadItems();
  }

  function showError(jqXHR, textStatus, errorThrown) {
    $('#upload-btn').html('{{ Lang::get("laravel-filemanager::lfm.btn-upload") }}');
    if (jqXHR.status == 413) {
      notify('{{ Lang::get("laravel-filemanager::lfm.error-too-large") }}');
    } else if (textStatus == 'error') {
      notify('{{ Lang::get("laravel-filemanager::lfm.error-other") }}' + errorThrown);
    } else {
      notify('{{ Lang::get("laravel-filemanager::lfm.error-other") }}' + textStatus + '<br>' + errorThrown);
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
  clickFolder($(this).data('id'));
});

function clickFolder(new_dir) {
  $('#working_dir').val(new_dir);
  setOpenFolders();
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
  $.ajax({
    type: 'GET',
    dataType: 'html',
    url: '{{ route("unisharp.lfm.getFolders") }}',
    data: {
      working_dir: $('#working_dir').val(),
      show_list: $('#show_list').val(),
      type: $('#type').val()
    },
    cache: false
  }).done(function (data) {
    $('#tree1').html(data);
  });
}

function loadItems() {
  var working_dir = $('#working_dir').val();
  console.log('Current working_dir : ' + working_dir);

  $.ajax({
    type: 'GET',
    dataType: 'html',
    url: '{{ route("unisharp.lfm.getItems") }}',
    data: {
      working_dir: working_dir,
      show_list: $('#show_list').val(),
      type: $('#type').val()
    },
    cache: false
  }).done(function (data) {
    $('#content').html(data);
    $('#nav-buttons').removeClass('hidden');
    $('.dropdown-toggle').dropdown();
    setOpenFolders();
  });
}

function createFolder(folder_name) {
  $.ajax({
    type: 'GET',
    dataType: 'text',
    url: '{{ route("unisharp.lfm.getAddfolder") }}',
    data: {
      name: folder_name,
      working_dir: $('#working_dir').val(),
      type: $('#type').val()
    },
    cache: false
  }).done(function (data) {
    if (data == 'OK') {
      loadFolders();
      loadItems();
      setOpenFolders();
    } else {
      notify(data);
    }
  });
}

function rename(item_name) {
  bootbox.prompt({
    title: "{{ Lang::get('laravel-filemanager::lfm.message-rename') }}",
    value: item_name,
    callback: function (result) {
      if (result !== null) {
        $.ajax({
          type: 'GET',
          dataType: 'text',
          url: '{{ route("unisharp.lfm.getRename") }}',
          data: {
            file: item_name,
            working_dir: $('#working_dir').val(),
            new_name: result,
            type: $('#type').val()
          },
          cache: false
        }).done(function (data) {
          if (data == 'OK') {
            loadItems();
            loadFolders();
          } else {
            notify(data);
          }
        });
      }
    }
  });
}

function trash(item_name) {
  bootbox.confirm("{{ Lang::get('laravel-filemanager::lfm.message-delete') }}", function (result) {
    if (result == true) {
      $.ajax({
        type: 'GET',
        dataType: 'text',
        url: '{{ route("unisharp.lfm.getDelete") }}',
        data: {
          working_dir: $('#working_dir').val(),
          items: item_name,
          type: $('#type').val()
        },
        cache: false
      }).done(function (data) {
        if (data != 'OK') {
          notify(data);
        } else {
          if ($('#working_dir').val() === home_dir || $('#working_dir').val() === shared_folder) {
            loadFolders();
          }
          loadItems();
        }
      });
    }
  });
}

function cropImage(image_name) {
  $.ajax({
    type: 'GET',
    dataType: 'text',
    url: '{{ route("unisharp.lfm.getCrop") }}',
    data: {
      img: image_name,
      working_dir: $('#working_dir').val(),
      type: $('#type').val()
    },
    cache: false
  }).done(function (data) {
    $('#nav-buttons').addClass('hidden');
    $('#content').html(data);
  });
}

function resizeImage(image_name) {
  $.ajax({
    type: 'GET',
    dataType: 'text',
    url: '{{ route("unisharp.lfm.getResize") }}',
    data: {
      img: image_name,
      working_dir: $('#working_dir').val(),
      type: $('#type').val()
    },
    cache: false
  }).done(function (data) {
    $('#nav-buttons').addClass('hidden');
    $('#content').html(data);
  });
}

function download(file_name) {
  location.href = '{{ route("unisharp.lfm.getDownload") }}?'
  + 'working_dir='
  + $('#working_dir').val()
  + '&type='
  + $('#type').val()
  + '&file='
  + file_name;
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

  function getFileUrl(file) {
    var path = $('#working_dir').val();
    var item_url = image_url;

    @if ("Images" !== $file_type)
    item_url = file_url;
    @endif

    if (path.indexOf(ds) === 0) {
      path = path.substring(1);
    }

    if (path != ds) {
      item_url = item_url + path + ds;
    }

    var url = item_url + file;
    url = url.replace(/\\/g, "/");

    return url;
  }

  var url = getFileUrl(file);
  var field_name = getUrlParam('field_name');

  if (window.opener || window.tinyMCEPopup || field_name || getUrlParam('CKEditorCleanUpFuncNum') || getUrlParam('CKEditor')) {
    if (window.tinyMCEPopup) {
      // use TinyMCE > 3.0 integration method
      useTinymce3(url);
      return;
    } else if (field_name) {
      // tinymce 4 and colorbox
      useTinymce4AndColorbox(url, field_name);
    } else if(getUrlParam('CKEditor')) {
      // use CKEditor 3.0 + integration method
      useCkeditor3(url);
    } else if (typeof data != 'undefined' && data['Properties']['Width'] != '') {
      // use FCKEditor 2.0 integration method
      useFckeditor2(url);
    } else {
      window.opener.SetUrl(url);
    }

    if (window.opener) {
      window.close();
    }
  } else {
    $.prompt(lg.fck_select_integration);
  }

  window.close();
}
//end useFile

function notImp() {
  bootbox.alert('Not yet implemented!');;
}

function notify(x) {
  bootbox.alert(x);
}

function fileView(x) {
  var rnd = makeRandom();
  var img_src = image_url + $('#working_dir').val() + ds + x;
  var img = "<img class='img img-responsive center-block' src='" + img_src + "'>";
  $('#fileview_body').html(img);
  $('#fileViewModal').modal();
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
