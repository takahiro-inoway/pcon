<div class="row">
  <div class="col-md-12">
    @if ($error->any())
    <div class="alert alert-denger">
      <ui>
        @foreach($error->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ui>
    </div>
    @endif
  </div>
</div>