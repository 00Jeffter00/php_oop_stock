<?php
if (isset($_SESSION["success"])) {
    echo '
                        <div class="alert w-100 alert-success" role="alert">
                            ✅ ' . $_SESSION["success"] . '
                        </div>
                    ';

    unset($_SESSION["success"]);
}
