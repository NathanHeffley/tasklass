<div class="navbar-menu">
    <div class="navbar-end">
        @if (Auth::check())
            <div class="navbar-item">
                <div class="field is-grouped">
                    <form action="/logout" method="POST" class="control">
                        {{ csrf_field() }}
                        <button class="button is-link" type="submit">Log out</button>
                    </form>
                    <p class="control"><a class="button is-primary" href="/todos">List</a></p>
                </div>
            </div>
        @else
            <div class="navbar-item">
                <div class="field is-grouped">
                    <p class="control"><a class="button is-link" href="/login">Log in</a></p>
                    <p class="control"><a class="button is-primary" href="/register">Register</a></p>
                </div>
            </div>
        @endif
    </div>
</div>