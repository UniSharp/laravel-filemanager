var lfm_route = location.origin + location.pathname;
var show_list;
var sort_type = 'alphabetic';
var multi_selection_enabled = false;
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
    });

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
        .click(function() {
          sort_type = sort.by;
          loadItems();
        })
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

  if (usingWysiwygEditor()) {
    $('#multi_selection_toggle').hide();
  }
});

// ======================
// ==  Navbar actions  ==
// ======================

$('#multi_selection_toggle').click(function () {
  multi_selection_enabled = !multi_selection_enabled;

  $('#multi_selection_toggle i')
    .toggleClass('fa-times', multi_selection_enabled)
    .toggleClass('fa-check-double', !multi_selection_enabled);

  if (!multi_selection_enabled) {
    clearSelected();
  }
});

$('#to-previous').click(function () {
  var previous_dir = getPreviousDir();
  if (previous_dir == '') return;
  goTo(previous_dir);
});

function toggleMobileTree(should_display) {
  if (should_display === undefined) {
    should_display = !$('#tree').hasClass('in');
  }
  $('#tree').toggleClass('in', should_display);
}

$('#show_tree').click(function (e) {
  toggleMobileTree();
});

$('#main').click(function (e) {
  if ($('#tree').hasClass('in')) {
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

$(document).on('click', '[data-action]', function() {
  window[$(this).data('action')]($(this).data('multiple') ? getSelectedItems() : getOneSelectedElement());
});

// ==========================
// ==  Multiple Selection  ==
// ==========================

function toggleSelected (e) {
  if (!multi_selection_enabled) {
    selected = [];
  }

  var sequence = $(e.target).closest('a').data('id');
  var element_index = selected.indexOf(sequence);
  if (element_index === -1) {
    selected.push(sequence);
  } else {
    selected.splice(element_index, 1);
  }

  updateSelectedStyle();
}

function clearSelected () {
  selected = [];

  multi_selection_enabled = false;

  updateSelectedStyle();
}

function updateSelectedStyle() {
  items.forEach(function (item, index) {
    $('[data-id=' + index + ']')
      .find('.square')
      .toggleClass('selected', selected.indexOf(index) > -1);
  });
  toggleActions();
}

function getOneSelectedElement(orderOfItem) {
  var index = orderOfItem !== undefined ? orderOfItem : selected[0];
  return items[index];
}

function getSelectedItems() {
  return selected.reduce(function (arr_objects, id) {
    arr_objects.push(getOneSelectedElement(id));
    return arr_objects
  }, []);
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

  $('[data-action=use]').toggleClass('d-none', !(many_selected && only_file));
  $('[data-action=rename]').toggleClass('d-none', !one_selected);
  $('[data-action=preview]').toggleClass('d-none', !(many_selected && only_file));
  $('[data-action=move]').toggleClass('d-none', !many_selected);
  $('[data-action=download]').toggleClass('d-none', !(many_selected && only_file));
  $('[data-action=resize]').toggleClass('d-none', !(one_selected && only_image));
  $('[data-action=crop]').toggleClass('d-none', !(one_selected && only_image));
  $('[data-action=trash]').toggleClass('d-none', !many_selected);
  $('[data-action=open]').toggleClass('d-none', !one_selected || only_file);
  $('#multi_selection_toggle').toggleClass('d-none', usingWysiwygEditor() || !many_selected);
  $('#actions').toggleClass('d-none', selected.length === 0);
  $('#fab').toggleClass('d-none', selected.length !== 0);
}

// ======================
// ==  Folder actions  ==
// ======================

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
  $('#content').html(data).removeClass('preserve_actions_space');
  clearSelected();
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
      var working_dir = response.working_dir;
      items = response.items;
      var hasItems = items.length !== 0;
      $('#empty').toggleClass('d-none', hasItems);
      $('#content').html('').removeAttr('class');

      if (hasItems) {
        $('#content').addClass(response.display).addClass('preserve_actions_space');

        items.forEach(function (item, index) {
          var template = $('#item-template').clone()
            .removeAttr('id class')
            .attr('data-id', index)
            .click(toggleSelected)
            .dblclick(function (e) {
              if (item.is_file) {
                use(getSelectedItems());
              } else {
                goTo(item.url);
              }
            });

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

      $('#working_dir').val(working_dir);
      console.log('Current working_dir : ' + working_dir);
      var breadcrumbs = [];
      var validSegments = working_dir.split('/').filter(function (e) { return e; });
      validSegments.forEach(function (segment, index) {
        if (index === 0) {
          // set root folder name as the first breadcrumb
          breadcrumbs.push($("[data-path='/" + segment + "']").text());
        } else {
          breadcrumbs.push(segment);
        }
      });

      $('#current_folder').text(breadcrumbs[breadcrumbs.length - 1]);
      $('#breadcrumbs > ol').html('');
      breadcrumbs.forEach(function (breadcrumb, index) {
        var li = $('<li>').addClass('breadcrumb-item').text(breadcrumb);

        if (index === breadcrumbs.length - 1) {
          li.addClass('active').attr('aria-current', 'page');
        } else {
          li.click(function () {
            // go to corresponding path
            goTo('/' + validSegments.slice(0, 1 + index).join('/'));
          });
        }

        $('#breadcrumbs > ol').append(li);
      });

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

function download(items) {
  items.forEach(function (item, index) {
    var data = defaultParameters();

    data['file'] = item.name;

    var token = getUrlParam('token');
    if (token) {
      data['token'] = token;
    }

    setTimeout(function () {
      location.href = lfm_route + '/download?' + $.param(data);
    }, index * 100);
  });
}

function open(item) {
  goTo(item.url);
}

function preview(items) {
  var carousel = $('#carouselTemplate').clone().attr('id', 'previewCarousel').removeClass('d-none');
  var imageTemplate = carousel.find('.carousel-item').clone().removeClass('active');
  var indicatorTemplate = carousel.find('.carousel-indicators > li').clone().removeClass('active');
  carousel.children('.carousel-inner').html('');
  carousel.children('.carousel-indicators').html('');
  carousel.children('.carousel-indicators,.carousel-control-prev,.carousel-control-next').toggle(items.length > 1);

  items.forEach(function (item, index) {
    var carouselItem = imageTemplate.clone()
      .addClass(index === 0 ? 'active' : '');

    if (item.thumb_url) {
      carouselItem.find('.carousel-image').css('background-image', 'url(\'' + item.url + '?timestamp=' + item.time + '\')');
    } else {
      carouselItem.find('.carousel-image').css('width', '50vh').append($('<div>').addClass('mime-icon ico-' + item.icon));
    }

    carouselItem.find('.carousel-label').attr('target', '_blank').attr('href', item.url)
      .append(item.name)
      .append($('<i class="fas fa-external-link-alt ml-2"></i>'));

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
  performLfmRequest('move', { items: items.map(function (item) { return item.name; }) })
    .done(refreshFoldersAndItems);
}

function getUrlParam(paramName) {
  var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
  var match = window.location.search.match(reParam);
  return ( match && match.length > 1 ) ? match[1] : null;
}

function use(items) {
  function useTinymce3(url) {
    if (!usingTinymce3()) { return; }

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

  function useTinymce4AndColorbox(url) {
    if (!usingTinymce4AndColorbox()) { return; }

    parent.document.getElementById(getUrlParam('field_name')).value = url;

    if(typeof parent.tinyMCE !== "undefined") {
      parent.tinyMCE.activeEditor.windowManager.close();
    }
    if(typeof parent.$.fn.colorbox !== "undefined") {
      parent.$.fn.colorbox.close();
    }
  }

  function useCkeditor3(url) {
    if (!usingCkeditor3()) { return; }

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
    if (!usingFckeditor2()) { return; }

    var p = url;
    var w = data['Properties']['Width'];
    var h = data['Properties']['Height'];
    window.opener.SetUrl(p,w,h);
  }

  var url = items[0].url;
  var callback = getUrlParam('callback');
  var useFileSucceeded = true;

  if (usingWysiwygEditor()) {
    useTinymce3(url);

    useTinymce4AndColorbox(url);

    useCkeditor3(url);

    useFckeditor2(url);
  } else if (callback && window[callback]) {
    window[callback](getSelectedItems());
  } else if (callback && parent[callback]) {
    parent[callback](getSelecteditems());
  } else if (window.opener) { // standalone button or other situations
    window.opener.SetUrl(getSelectedItems());
  } else {
    useFileSucceeded = false;
  }

  if (useFileSucceeded) {
    if (window.opener) {
      window.close();
    }
  } else {
    console.log('window.opener not found');
    // No editor found, open/download file using browser's default method
    window.open(url);
  }
}
//end useFile

// ==================================
// ==     WYSIWYG Editors Check    ==
// ==================================

function usingTinymce3() {
  return !!window.tinyMCEPopup;
}

function usingTinymce4AndColorbox() {
  return !!getUrlParam('field_name');
}

function usingCkeditor3() {
  return !!getUrlParam('CKEditor') || !!getUrlParam('CKEditorCleanUpFuncNum');
}

function usingFckeditor2() {
  return window.opener && typeof data != 'undefined' && data['Properties']['Width'] != '';
}

function usingWysiwygEditor() {
  return usingTinymce3() || usingTinymce4AndColorbox() || usingCkeditor3() || usingFckeditor2();
}

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
