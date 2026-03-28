<?php

$isPasswordCorrect = password_verify($_POST['password'], $existingHashFromDb);
