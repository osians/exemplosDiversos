<?php

/**
 * Verifica se um password e' valido seguindo as seguintes regras:
 *
 * - Deve possuir 8 caracteres
 * - Deve ter letras e números em sua composição 
 * - Não haverá obrigatoriedade de a senha possuir letras maiúsculas, porém, se o usuário optar, sistema deve diferenciar letras maiúsculas e minúsculas 
 * - Não pode ter números sequenciais 
 * - Não pode ter data de nascimento 
 * - Não pode ter caracteres especiais
**/
function validarPassword($password)
{
    return (preg_match('/^(?=[a-zA-Z\d]*[a-z])(?=[a-zA-Z\d]*[A-Z])[a-zA-Z\d]{8}$/', $password));
}

var_dump(validarPassword('123456'));
var_dump(validarPassword('Co1Tia6H'));
var_dump(validarPassword('Wa24021986'));
