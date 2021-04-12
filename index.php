<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo ':(';
    exit;
} else {
  $users = ['hugo.dias@aluno.ufop.edu.br','bruno@ufop.edu.br','endhel.freitas@aluno.ufop.edu.br'];
  if(in_array($_SERVER['PHP_AUTH_USER'],$users) && $_SERVER['PHP_AUTH_PW'] == 'csi442'){
    header('Location: layout/index.html');
  }
}