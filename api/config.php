<?php
return [
 'host'=>getenv('AMES_DB_HOST') ?: '127.0.0.1',
 'port'=>getenv('AMES_DB_PORT') ?: '3306',
 'database'=>getenv('AMES_DB_NAME') ?: 'ames3',
 'username'=>getenv('AMES_DB_USER') ?: 'root',
 'password'=>getenv('AMES_DB_PASS') !== false ? getenv('AMES_DB_PASS') : '',
];
