<?php

/**
 * Calcula Modulo de um numero
 *
 * @param  int $dividendo
 * @param  int $divisor
 *
 * @return int
 */
function modulo($dividendo, $divisor) {
   return round($dividendo - (floor($dividendo / $divisor) * $divisor));
}

/**
 * Gera numeros Randomicos
 *
 * @param integer $total - quantidade de numeros
 *
 * @return array
 */
function getNumerosRandomicos($total = 10) {
   $numbers = array();
   for ($i = 0; $i < $total; $i++) {
      $numbers[$i] = rand(0, 9);
   }  
   return $numbers;
}

/**
 * Dado Um Array de numeros, determina o Digito Verificador para CPF
 *
 * @param array $numeros
 *
 * @return int
 */
function getDigitoVerificadorCpf($numeros = array()) {
   $digito = 0;
   $arraySize = count($numeros) + 1;

   foreach ($numeros as $index => $numero) {
      $digito += $numero * ($arraySize - $index);
   }
   
   $digito = 11 - (modulo($digito, 11));
   return ($digito >= 10) ? 0 : $digito;
}

/**
 * Gera um numero de CPF Fake
 *
 * @return String - 11 digitos numericos
 */
function gerarCpfFake()
{
   $numeros = getNumerosRandomicos(9);
   $numeros[] = getDigitoVerificadorCpf($numeros);
   $numeros[] = getDigitoVerificadorCpf($numeros);

   return implode('', $numeros);
}

echo gerarCpfFake();
