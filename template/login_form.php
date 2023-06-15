<section class="section pt-0 pb-0 mb-0">
    <form id="loginForm">
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
                <button type="submit" id="submitButton" class="button is-primary">Submit</button>
            </div>
            <div class="control">
                <button type="reset" id="resetButton" class="button is-light">Reset</button>
            </div>
        </div>
    </form>
</section>
