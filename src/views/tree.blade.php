<div class="m-3 d-block d-lg-none">
  <h1 style="font-size: 1.5rem;">Laravel File Manager</h1>
  <small class="d-block">Ver 2.0</small>
  <div class="row mt-3">
    <div class="col-3">
      <img src="https://www.unisharp.com/img/favicon_unisharp_logo.png">
    </div>

    <div class="col-9">
      <p>Current usage :</p>
      <p>20 GB (Max : 1 TB)</p>
    </div>
  </div>
  <div class="progress mt-3" style="height: .5rem;">
    <div class="progress-bar progress-bar-striped progress-bar-animated w-75 bg-main" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
</div>

<ul class="nav nav-pills flex-column">
  @foreach($root_folders as $root_folder)
    <li class="nav-item active">
      <a class="nav-link" href="#" data-type="0" data-path="{{ $root_folder->path }}">
        <i class="fa fa-folder fa-fw"></i> {{ $root_folder->name }}
      </a>
    </li>
    @foreach($root_folder->children as $directory)
    <li class="nav-item sub-item">
      <a class="nav-link" href="#" data-type="0" data-path="{{ $directory->path }}">
        <i class="fa fa-folder fa-fw"></i> {{ $directory->name }}
      </a>
    </li>
    @endforeach
  @endforeach
</ul>
