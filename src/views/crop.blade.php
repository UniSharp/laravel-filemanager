<div class="row fill">
    <div class="col-md-8 fill">
        <div class="crop-container">
            <img src="{!! $img !!}" class="img-responsive">
        </div>
    </div>
    <div class="col-md-4 fill">
        <div class="img-preview"></div>
    </div>

</div>

<script>
    $('.crop-container > img').cropper({
        //aspectRatio: 16 / 9,
        preview: ".img-preview",
        crop: function(data) {
            // Output the result data for cropping image.
        }
    });
</script>