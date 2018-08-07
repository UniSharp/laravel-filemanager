var show_list;
var show_tree = false;
var sort_type = 'alphabetic';
var multi_selection_enabled = true;
var selected = [];
var items = [];

$.fn.fab = function (options) {
  var menu = this;
  menu.addClass('fab-wrapper');

  var toggler = $('<a>')
    .addClass('fab-button fab-toggle')
    .append($('<i>').addClass('fas fa-plus'))
    .click(function () {
      menu.toggleClass('fab-expand');
    })

  menu.append(toggler);

  options.buttons.forEach(function (button) {
    toggler.before(
      $('<a>').addClass('fab-button fab-action')
        .attr('data-label', button.label)
        .attr('id', button.attrs.id)
        .append($('<i>').addClass(button.icon))
        .click(function () {
          menu.removeClass('fab-expand');
        })
    );
  });
};

Array.prototype.toggleElement = function (element) {
  var element_index = this.indexOf(element);
  if (element_index === -1) {
    this.push(element);
  } else {
    this.splice(element_index, 1);
  }
};

$(document).ready(function () {
  $('#fab').fab({
    buttons: [
      {
        icon: 'fas fa-upload',
        label: lang['nav-upload'],
        attrs: {id: 'upload'}
      },
      {
        icon: 'fas fa-folder',
        label: lang['nav-new'],
        attrs: {id: 'add-folder'}
      }
    ]
  });

  actions.reverse().forEach(function (action) {
    $('#nav-buttons > ul').prepend(
      $('<li>').addClass('nav-item').append(
        $('<a>').addClass('nav-link d-none')
          .attr('data-action', action.name)
          .attr('data-multiple', action.multiple)
          .append($('<i>').addClass('fas fa-fw fa-' + action.icon))
          .append($('<span>').text(action.label))
      )
    );
  });

  sortings.forEach(function (sort) {
    $('#nav-buttons .dropdown-menu').append(
      $('<a>').addClass('dropdown-item').attr('data-sortby', sort.by)
        .append($('<i>').addClass('fas fa-fw fa-' + sort.icon))
        .append($('<span>').text(sort.label))
    );
  });
  loadFolders();
  performLfmRequest('errors')
    .done(function (response) {
      JSON.parse(response).forEach(function (message) {
        $('#alerts').append(
          $('<div>').addClass('alert alert-warning')
            .append($('<i>').addClass('fas fa-exclamation-circle'))
            .append(' ' + message)
        );
      });
    });

  $(window).on('dragenter', function(){
    $('#uploadModal').modal('show');
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

function toggleMobileTree(should_display) {
  if (should_display) {
    var position = '0px';
  } else {
    var position = '-' + $('#tree').width() + 'px';
  }

  $('#tree').animate({'left': position}, 1000, 'easeOutExpo', function () {
    show_tree = should_display;
  });
}

$('#show_tree').click(function (e) {
  toggleMobileTree(!show_tree);
});

$('#main').click(function (e) {
  if (show_tree) {
    toggleMobileTree(false);
  }
});

$(document).on('click', '#add-folder', function () {
  dialog(lang['message-name'], '', createFolder);
});

$(document).on('click', '#upload', function () {
  $('#uploadModal').modal('show');
});

$(document).on('click', '[data-display]', function() {
  show_list = $(this).data('display');
  loadItems();
});

$(document).on('click', '[data-sortby]', function() {
  sort_type = $(this).data('sortby');
  loadItems();
});

$(document).on('click', '[data-action]', function () {
  if ($(this).data('multiple')) {
    window[$(this).data('action')](getSelectedItems());
  } else {
    window[$(this).data('action')](getOneSelectedElement());
  }
});

// ======================
// ==  Folder actions  ==
// ======================

$(document).on('click', '#content a', function (e) {
  var element = $(e.target).closest('a');

  if (multi_selection_enabled) {
    selected.toggleElement(element.data('id'));
    element.find('.square').toggleClass('selected');
    toggleActions();
  } else {
    if (element.is_file) {
      use(getOneSelectedElement().url);
    } else {
      goTo(getOneSelectedElement().url);
    }
  }
});

function getOneSelectedElement(item_id) {
  if (item_id === undefined) {
    item_id = selected[0];
  }
  return items[item_id];
}

function getSelectedItems() {
  var arr_objects = [];
  selected.forEach(function (id, index) {
    arr_objects.push(getOneSelectedElement(id));
  });
  return arr_objects;
}

function toggleActions() {
  var one_selected = selected.length === 1;
  var many_selected = selected.length >= 1;
  var only_image = getSelectedItems()
    .filter(function (item) { return !item.is_image; })
    .length === 0;
  var only_file = getSelectedItems()
    .filter(function (item) { return !item.is_file; })
    .length === 0;

  $('[data-action=use]').toggleClass('d-none', !(many_selected && only_file))
  $('[data-action=rename]').toggleClass('d-none', !one_selected)
  $('[data-action=preview]').toggleClass('d-none', !(many_selected && only_file))
  $('[data-action=move]').toggleClass('d-none', !many_selected)
  $('[data-action=download]').toggleClass('d-none', !(one_selected && only_file))
  $('[data-action=resize]').toggleClass('d-none', !(one_selected && only_image))
  $('[data-action=crop]').toggleClass('d-none', !(one_selected && only_image))
  $('[data-action=trash]').toggleClass('d-none', !many_selected)
  $('#actions').toggleClass('d-none', selected.length === 0)
  $('#fab').toggleClass('d-none', selected.length !== 0)
}

$(document).on('click', '#tree a', function (e) {
  goTo($(e.target).closest('a').data('path'));
  toggleMobileTree(false);
});

function goTo(new_dir) {
  $('#working_dir').val(new_dir);
  loadItems();
}

function getPreviousDir() {
  var working_dir = $('#working_dir').val();
  return working_dir.substring(0, working_dir.lastIndexOf('/'));
}

function setOpenFolders() {
  $('#tree [data-path]').each(function (index, folder) {
    // close folders that are not parent
    var should_open = ($('#working_dir').val() + '/').startsWith($(folder).data('path') + '/');
    $(folder).children('i')
      .toggleClass('fa-folder-open', should_open)
      .toggleClass('fa-folder', !should_open);
  });

  $('#tree .nav-item').removeClass('active');
  $('#tree [data-path="' + $('#working_dir').val() + '"]').parent('.nav-item').addClass('active');
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
    beforeSend: function(request) {
      var token = getUrlParam('token');
      if (token !== null) {
        request.setRequestHeader("Authorization", 'Bearer ' + token);
      }
    },
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
  $('#nav-buttons > ul').addClass('d-none');
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
  loading(true);
  performLfmRequest('jsonitems', {show_list: show_list, sort_type: sort_type}, 'html')
    .done(function (data) {
      selected = [];
      var response = JSON.parse(data);
      items = response.items;
      var hasItems = items.length !== 0;
      $('#empty').toggleClass('d-none', hasItems);
      $('#content').html('').removeAttr('class');

      if (hasItems) {
        $('#content').addClass(response.display);

        items.forEach(function (item, index) {
          items[(new Date()).getTime()] = item;

          var template = $('#item-template').clone()
            .removeAttr('id class')
            .attr('data-id', index);

          if (item.thumb_url) {
            var image = $('<div>').css('background-image', 'url("' + item.thumb_url + '?timestamp=' + item.time + '")');
          } else {
            var image = $('<div>').addClass('mime-icon ico-' + item.icon);
          }

          template.find('.square').append(image);
          template.find('.item_name').text(item.name);
          template.find('time').text((new Date(item.time * 1000)).toLocaleString());

          $('#content').append(template);
        });
      }
      $('#nav-buttons > ul').removeClass('d-none');
      $('#working_dir').val(response.working_dir);
      console.log('Current working_dir : ' + $('#working_dir').val());
      var atRootFolder = getPreviousDir() == '';
      $('#to-previous').toggleClass('d-none invisible-lg', atRootFolder);
      $('#show_tree').toggleClass('d-none', !atRootFolder).toggleClass('d-block', atRootFolder);
      setOpenFolders();
      loading(false);
      toggleActions();
    });
}

function loading(show_loading) {
  $('#loading').toggleClass('d-none', !show_loading);
}

function createFolder(folder_name) {
  performLfmRequest('newfolder', {name: folder_name})
    .done(refreshFoldersAndItems);
}

// ==================================
// ==         File Actions         ==
// ==================================

function rename(item) {
  dialog(lang['message-rename'], item.name, function (new_name) {
    performLfmRequest('rename', {
      file: item.name,
      new_name: new_name
    }).done(refreshFoldersAndItems);
  });
}

function trash(items) {
  notify(lang['message-delete'], function () {
    performLfmRequest('delete', {
      items: items.map(function (item) { return item.name; })
    }).done(refreshFoldersAndItems)
  });
}

function crop(item) {
  performLfmRequest('crop', {img: item.name})
    .done(hideNavAndShowEditor);
}

function resize(item) {
  performLfmRequest('resize', {img: item.name})
    .done(hideNavAndShowEditor);
}

function download(item) {
  var data = defaultParameters();
  data['file'] = item.name;
  location.href = lfm_route + '/download?' + $.param(data);
}

function preview(items) {
  var carousel = $('#carouselTemplate').clone().attr('id', 'previewCarousel').removeClass('d-none');
  var imageTemplate = carousel.find('.carousel-item').clone().removeClass('active');
  var indicatorTemplate = carousel.find('.carousel-indicators > li').clone().removeClass('active');
  carousel.children('.carousel-inner').html('');
  carousel.children('.carousel-indicators').html('');

  items.forEach(function (item, index) {
    var carouselItem = imageTemplate.clone()
      .addClass(index === 0 ? 'active' : '');

    if (item.thumb_url) {
      carouselItem.find('.carousel-image').css('background-image', 'url(\'' + item.url + '?timestamp=' + item.time + '\')');
    } else {
      carouselItem.find('.carousel-image').css('width', '50vh').append($('<div>').addClass('mime-icon ico-' + item.icon));
    }

    carousel.children('.carousel-inner').append(carouselItem);

    var carouselIndicator = indicatorTemplate.clone()
      .addClass(index === 0 ? 'active' : '')
      .attr('data-slide-to', index);
    carousel.children('.carousel-indicators').append(carouselIndicator);
  });


  // carousel swipe control
  var touchStartX = null;

  carousel.on('touchstart', function (event) {
    var e = event.originalEvent;
    if (e.touches.length == 1) {
      var touch = e.touches[0];
      touchStartX = touch.pageX;
    }
  }).on('touchmove', function (event) {
    var e = event.originalEvent;
    if (touchStartX != null) {
      var touchCurrentX = e.changedTouches[0].pageX;
      if ((touchCurrentX - touchStartX) > 60) {
        touchStartX = null;
        carousel.carousel('prev');
      } else if ((touchStartX - touchCurrentX) > 60) {
        touchStartX = null;
        carousel.carousel('next');
      }
    }
  }).on('touchend', function () {
    touchStartX = null;
  });
  // end carousel swipe control

  notify(carousel);
}

function move(items) {
  performLfmRequest('move', {
      items: items.map(function (item) { return item.name; })
    })
    .done(refreshFoldersAndItems);
}

function getUrlParam(paramName) {
  var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
  var match = window.location.search.match(reParam);
  return ( match && match.length > 1 ) ? match[1] : null;
}

function use(items) {
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

  var url = items[0].url;
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
      window.opener.SetUrl(getSelectedItems());
    }

    if (window.opener) {
      window.close();
    }
  } else {
    // No editor found, open/download file using browser's default method
    window.open(url);
    // notify('window.opener not found');
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

function notify(body, callback) {
  $('#notify').find('.btn-primary').toggle(callback !== undefined);
  $('#notify').find('.btn-primary').unbind().click(callback);
  $('#notify').modal('show').find('.modal-body').html(body);
}

function dialog(title, value, callback) {
  $('#dialog').find('input').val(value);
  $('#dialog').on('shown.bs.modal', function () {
    $('#dialog').find('input').focus();
  });
  $('#dialog').find('.btn-primary').unbind().click(function (e) {
    callback($('#dialog').find('input').val());
  });
  $('#dialog').modal('show').find('.modal-title').text(title);
}
