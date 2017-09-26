var show_list;
var show_tree = false;
var sort_type = 'alphabetic';
var multi_selection_enabled = true;
var selected = [];

Array.prototype.toggleElement = function (element) {
  var element_index = this.indexOf(element);
  if (element_index === -1) {
    this.push(element);
  } else {
    this.splice(element_index, 1);
  }
};

$(document).ready(function () {
  bootbox.setDefaults({locale:lang['locale-bootbox']});
  loadFolders();
  performLfmRequest('errors')
    .done(function (response) {
      JSON.parse(response).forEach(function (message) {
        $('#alerts').append(
          $('<div>').addClass('alert alert-warning')
            .append($('<i>').addClass('fa fa-exclamation-circle'))
            .append(' ' + message)
        );
      });
    });
});

// ======================
// ==  Navbar actions  ==
// ======================

$('#multi_selection_toggle').click(function () {
  multi_selection_enabled = !multi_selection_enabled;
});

$('#to-previous').click(function () {
  var previous_dir = getPreviousDir();
  if (previous_dir == '') return;
  goTo(previous_dir);
});

$('#show_tree').click(function () {
  $('#mobile_tree').animate({'left': '0px'}, 1000, 'easeOutExpo');
  setTimeout(function () {
    show_tree = true;
  }, 1000);
});

$('.row').click(function () {
  if (show_tree) {
    $('#mobile_tree').animate({'left': '-' + $('#mobile_tree').width() + 'px'}, 1000, 'easeOutExpo');
    show_tree = false;
  }
});

$(document).on('click', '#add-folder', function () {
  bootbox.prompt(lang['message-name'], function (result) {
    if (result == null) return;
    createFolder(result);
  });
});

$(document).on('click', '#upload', function () {
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

$('[data-display]').click(function() {
  show_list = $(this).data('display');
  loadItems();
});

$('[data-sortby]').click(function() {
  sort_type = $(this).data('sortby');
  loadItems();
});

$('[data-action]').click(function () {
  window[$(this).data('action')](getOneSelected());
});

// ======================
// ==  Folder actions  ==
// ======================

$(document).on('click', '#grid a, #list a', function (e) {
  var element = $(e.target).closest('a');
  var element_path = element.data('path');

  if (multi_selection_enabled) {
    selected.toggleElement(element_path);
    element.find('.square').toggleClass('selected');
    toggleActions();
  } else {
    if (element.data('type') === 0) {
      goTo(element_path);
    } else {
      useFile(element_path);
    }
  }
});

function getOneSelected(item_path) {
  return $('[data-path="' + item_path || selected[0] + '"]');
}

function getSelectedItems() {
  var arr_objects = [];
  selected.forEach(function (index, path) {
    arr_objects.push(getOneSelected(path));
  });
  return arr_objects;
}

function toggleActions() {
  var one_selected = selected.length === 1;
  var many_selected = selected.length >= 1;
  var only_image = getSelectedItems()
    .filter(function (item) { return item.data('image') === 0; })
    .length === 0;
  var only_file = getSelectedItems()
    .filter(function (item) { return item.data('type') === 0; })
    .length === 0;

  $('[data-action=use]').toggleClass('hide', !(many_selected && only_file))
  $('[data-action=rename]').toggleClass('hide', !one_selected)
  $('[data-action=preview]').toggleClass('hide', !(one_selected && only_image))
  $('[data-action=move]').toggleClass('hide', !(many_selected))
  $('[data-action=download]').toggleClass('hide', !(many_selected && only_file))
  $('[data-action=resize]').toggleClass('hide', !(one_selected && only_image))
  $('[data-action=crop]').toggleClass('hide', !(one_selected && only_image))
  $('[data-action=trash]').toggleClass('hide', !many_selected)
}

$(document).on('click', '#tree a', function (e) {
  goTo($(e.target).closest('a').data('path'));
});

function goTo(new_dir) {
  $('#working_dir').val(new_dir);
  loadItems();
}

function getPreviousDir() {
  var ds = '/';
  var working_dir = $('#working_dir').val();
  return working_dir.substring(0, working_dir.lastIndexOf(ds));
}

function dir_starts_with(str) {
  return $('#working_dir').val().indexOf(str) === 0;
}

function setOpenFolders() {
  $('[data-type=0]').each(function (index, folder) {
    // close folders that are not parent
    var should_open = dir_starts_with($(folder).data('path'));
    $(folder).children('i')
      .toggleClass('fa-folder-open', should_open)
      .toggleClass('fa-folder', !should_open);
  });
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
  $('#nav-buttons > ul').addClass('hide');
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
      selected = [];
      var response = JSON.parse(data);
      $('#content').html(response.html);
      $('#nav-buttons > ul').removeClass('hide');
      $('#working_dir').val(response.working_dir);
      $('#current_dir').text(response.working_dir);
      console.log('Current working_dir : ' + $('#working_dir').val());
      $('#to-previous').toggleClass('invisible', getPreviousDir() == '');
      setOpenFolders();
      $('#loading').addClass('hide');
      toggleActions();
    });
}

function createFolder(folder_name) {
  performLfmRequest('newfolder', {name: folder_name})
    .done(refreshFoldersAndItems);
}

// ==================================
// ==         File Actions         ==
// ==================================

function rename(item) {
  bootbox.prompt({
    title: lang['message-rename'],
    value: item.data('name'),
    callback: function (result) {
      if (result == null) return;
      performLfmRequest('rename', {
        file: item.data('name'),
        new_name: result
      }).done(refreshFoldersAndItems);
    }
  });
}

function trash(item) {
  bootbox.confirm(lang['message-delete'], function (result) {
    if (result == true) {
      performLfmRequest('delete', {items: item.data('name')})
        .done(refreshFoldersAndItems);
    }
  });
}

function crop(item) {
  performLfmRequest('crop', {img: item.data('name')})
    .done(hideNavAndShowEditor);
}

function resize(item) {
  performLfmRequest('resize', {img: item.data('name')})
    .done(hideNavAndShowEditor);
}

function download(item) {
  var data = defaultParameters();
  data['file'] = item.data('name');
  location.href = lfm_route + '/download?' + $.param(data);
}

function preview(item) {
  bootbox.dialog({
    title: lang['title-view'],
    message: $('<img>')
      .addClass('img img-responsive center-block')
      .attr('src', item.data('path') + '?timestamp=' + item.data('time')),
    size: 'large',
    onEscape: true,
    backdrop: true
  });
}

function move(item) {
  notImp();
}

function use(item) {
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

  var url = item.data('path');
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

// ==================================
// ==            Others            ==
// ==================================

function defaultParameters() {
  return {
    working_dir: $('#working_dir').val(),
    type: $('#type').val()
  };
}

function notImp() {
  notify('Not yet implemented!');
}

function notify(message) {
  bootbox.alert(message);
}
