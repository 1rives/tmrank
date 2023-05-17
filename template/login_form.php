<form class="mx-6 my-2 px-6" id="loginForm">
    <div class="field">
        <label class="label">Player login</label>
        <div class="control has-icons-left has-icons-right">
            <input class="input" type="text" name="login" id="login" placeholder="Login input">
            <span class="icon is-small is-left">
                <i class="fas fa-user"></i>
            </span>
            <span class="icon is-small is-right">
                <!-- Error icon -->
            </span>
        </div>
        <p class="help is-danger"></p>
    </div>

    <div class="field is-grouped">
        <div class="control">
            <button type="submit" class="button is-link">Submit</button>
        </div>
        <div class="control">
            <button type="reset" class="button is-link is-light">Reset</button>
        </div>
    </div>
</form>