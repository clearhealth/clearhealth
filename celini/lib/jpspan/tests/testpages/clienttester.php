<?php
switch ( @$_GET['test'] ) {
    case 'progress':
        header ('Content-Length: 10' );
        echo '1234567890';
    break;
}
?>