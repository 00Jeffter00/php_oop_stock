<?php
if (isset($_SESSION["error"])) {
    echo '
                        <div class="alert w-100 alert-danger" role="alert">
                            ⚠️ ' . $_SESSION["error"] . '
                        </div>
                    ';

    unset($_SESSION["error"]);
}
