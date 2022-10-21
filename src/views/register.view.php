<h1>Sign Up</h1>
<form action="" name="register" novalidate data-nocustom-validate>
    <div class="field">
        <label for="email">E-Mail address</label>
        <input type="email" id="email" name="email" placeholder="Your e-mail address" required>
    </div>
    <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Your username" required minlength="3" maxlength="255">
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password" required minlength="6" maxlength="255">
    </div>
    <div class="box-message"></div>
    <button>Sign Up</button>
</form>