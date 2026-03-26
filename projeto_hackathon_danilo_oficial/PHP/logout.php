<?php
session_start();
session_destroy(); // Destrói o crachá
header("Location: ../login.html"); // Manda de volta para a tela de login
exit;