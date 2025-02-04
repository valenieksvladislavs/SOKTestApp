<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4 col-sm-6">
      <h1 class="h3 mb-3 fw-normal text-center">Log in</h1>
      
      <div id="systemError" class="alert alert-danger d-none"></div>
      
      <form id="loginForm" method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input
            type="text"
            class="form-control"
            id="username"
            name="username"
            placeholder="Enter username"
            required
          />
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            placeholder="Enter password"
            required
          />
        </div>
        
        <div class="mb-3 form-check">
          <input
            type="checkbox"
            class="form-check-input"
            id="rememberMe"
            name="remember"
          />
          <label class="form-check-label" for="rememberMe">Remember me</label>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">
          Log in
        </button>
      </form>
    </div>
  </div>
</div>

<script src="/public/js/login.min.js"></script>
