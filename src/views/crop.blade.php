<div class="row">
  <div class="col-md-8">
    <div class="crop-container">
      <img src="{{ $img->url . '?timestamp=' . $img->updated }}" class="img img-responsive">
    </div>
  </div>
  <div class="col-md-4">
    <div class="text-center">

      <div class="img-preview center-block"></div>
      <br>

      <div class="btn-group clearfix">
        <label class="btn btn-primary btn-aspectRatio active" onclick="changeAspectRatio(this, 16 / 9)">
          16:9
        </label>
        <label class="btn btn-primary btn-aspectRatio" onclick="changeAspectRatio(this, 4 / 3)">
          4:3
        </label>
        <label class="btn btn-primary btn-aspectRatio" onclick="changeAspectRatio(this, 1)">
          1:1
        </label>
        <label class="btn btn-primary btn-aspectRatio" onclick="changeAspectRatio(this, 2 / 3)">
          2:3
        </label>
        {{--<label class="btn btn-primary" onclick="changeAspectRatio(this, null)">
          Free
        </label>--}}
      </div>
      <br>
      <br>

      <button class="btn btn-primary" onclick="performCrop()">{{ trans('laravel-filemanager::lfm.btn-crop') }}</button>
      <button class="btn btn-info" onclick="loadItems()">{{ trans('laravel-filemanager::lfm.btn-cancel') }}</button>
      <form id='cropForm'>
        <input type="hidden" id="img" name="img" value="{{ $img->name }}">
        <input type="hidden" id="working_dir" name="working_dir" value="{{ $working_dir }}">
        <input type="hidden" id="dataX" name="dataX">
        <input type="hidden" id="dataY" name="dataY">
        <input type="hidden" id="dataWidth" name="dataWidth">
        <input type="hidden" id="dataHeight" name="dataHeight">
        <input type='hidden' name='_token' value='{{csrf_token()}}'>
      </form>
    </div>
  </div>

</div>

<script>
    var $image = null,
        options = {};

    $(document).ready(function () {
        var $dataX = $('#dataX'),
            $dataY = $('#dataY'),
            $dataHeight = $('#dataHeight'),
            $dataWidth = $('#dataWidth');

        $image = $('.crop-container > img');
        options = {
            aspectRatio: 16 / 9,
            preview: ".img-preview",
            strict: false,
            crop: function (data) {
                // Output the result data for cropping image.
                $dataX.val(Math.round(data.x));
                $dataY.val(Math.round(data.y));
                $dataHeight.val(Math.round(data.height));
                $dataWidth.val(Math.round(data.width));
            }
        };
        $image.cropper(options);
    });
    function changeAspectRatio(_this, aspectRatio) {
        options.aspectRatio = aspectRatio;
        $('.btn-aspectRatio.active').removeClass('active');
        $(_this).addClass('active');
        $('.img-preview').removeAttr('style');
        $image.cropper('destroy').cropper(options);
        return false;
    }
    function performCrop() {
      performLfmRequest('cropimage', {
        img: $("#img").val(),
        working_dir: $("#working_dir").val(),
        dataX: $("#dataX").val(),
        dataY: $("#dataY").val(),
        dataHeight: $("#dataHeight").val(),
        dataWidth: $("#dataWidth").val(),
        type: $('#type').val()
      }).done(loadItems);
    }
</script>
