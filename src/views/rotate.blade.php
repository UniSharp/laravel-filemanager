<div class="row no-gutters">
  <div class="col-md-8 bg-light" id="work_space">
    <div id="rotate-container">
      <img id="image" src="{{ $img->url . '?timestamp=' . $img->time }}" class="img">
    </div>
  </div>
  <div class="col-xl-4">
    <div class="text-center">

      <div class="btn-group clearfix my-3">
        <button class="btn btn-outline-primary" onclick="rotateCounterclockwise()">{{ trans('laravel-filemanager::lfm.btn-counterclockwise') }}</button>
        <button class="btn btn-outline-primary" onclick="rotateClockwise()">{{ trans('laravel-filemanager::lfm.btn-clockwise') }}</button>
      </div>
      
      <div class="">
        <button class="btn btn-secondary" onclick="loadItems()">{{ trans('laravel-filemanager::lfm.btn-cancel') }}</button>
        <button class="btn btn-primary" onclick="performRotate()">{{ trans('laravel-filemanager::lfm.btn-confirm') }}</button>
      </div>

      <form id='RotateForm'>
        <input type="hidden" id="img" name="img" value="{{ $img->name }}">
        <input type="hidden" id="working_dir" name="working_dir" value="{{ $working_dir }}">
        <input type='hidden' name='_token' value='{{ csrf_token() }}'>
      </form>
    </div>
  </div>
</div>

<script>
    var options = {},
        $angle = 0,
        $image = document.getElementById('rotate-container');

    $(document).ready(function () {
    });

    function rotateClockwise(){

      $angle = (($angle + 90) % 360);
      $image.className = "rotate" + $angle;

    }

    function rotateCounterclockwise(){

      $angle = (($angle - 90 + 360) % 360);
      $image.className = "rotate" + $angle;

    }

    function performRotate() {

      performLfmRequest('rotateimage', {
        img: $("#img").val(),
        working_dir: $("#working_dir").val(),
        type: $('#type').val(),
        angle: -$angle
      }).done(loadItems);

    }
</script>
