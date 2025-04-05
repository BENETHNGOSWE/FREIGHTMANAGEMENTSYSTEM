<div class="nav-header">
          @auth
          @php
              $clientLogo = auth()->user()->client->client_logo ?? 'client_default.png';
          @endphp
          <div class="d-flex justify-content-center">
              <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                  <img src="{{ asset('storage/uploads/' . $clientLogo) }}" width="130" alt="Client Logo" style="max-width: 100%; height: auto;" />
              </a>
          </div>
        @else
          <div class="d-flex justify-content-center">
              <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                  <img src="{{ asset('assets/images/profile/user.png') }}" width="20" alt="Default User" style="max-width: 100%; height: auto;" />
              </a>
          </div>
        @endauth

    <div class="nav-control">
      <div class="hamburger">
        <span class="line"></span><span class="line"></span><span class="line"></span>
      </div>
    </div>
  </div>