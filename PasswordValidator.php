<?php

/**
 * Classe para validação de Passwords com Regras pre-estabelecidas
 *
 * @category SECLIB
 * @package SECURITY
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.0
 *
 * @author Wanderlei Santana <sans.pds@gmail.com>
 *
 * @method bool getValid()
 * @method string getPassword()
 * @method array getMessage()
 * @method PasswordValidator setMinChars($minChars)
 * @method integer getMinChars()
 * @method PasswordValidator setCheckUpperAndLowerLetters($value)
 * @method bool getCheckUpperAndLowerLetters()
 * @method bool check($password)
 */
class PasswordValidator
{
    /**
     * Indica se o Password é valido
     *
     * @var Boolean
     */
    protected $_valid = true;

    /**
     * Array com todas as mensagens de erros capturadas durante validacao.
     *
     * @var type
     */
    protected $_message = array();
    
    /**
     * Minimo de caracteres aceitos para o password
     *
     * @var type
     */
    protected $_minChars = 8;
    
    /**
     * Armazena o Password testado
     *
     * @var type
     */
    protected $_password = "";
    
    /**
     * Se true, faz validacao para letras maiusculas e minusculas
     *
     * @var type
     */
    protected $_checkUpperAndLowerLetters = true;
    
    /**
     * Indica se o password e validou ou nao
     *
     * @param bool $value
     *
     * @return PasswordValidator
     */
    protected function _setValid($value)
    {
        $this->_valid = $value;
        return $this;
    }
    
    /**
     * Informa se o Password esta valido
     *
     * @return bool
     */
    public function getValid()
    {
        return $this->_valid;
    }
    
    /**
     * Seta o Password a validar
     *
     * @param String $password
     *
     * @return PasswordValidator
     */
    protected function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Retorna o Password sendo testado
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Retorna Log de erros a seu estado anterior
     *
     * @return PassworValidator
     */
    protected function _resetMessage()
    {
        $this->_message = array();
        return $this;
    }
    
    /**
     * Adiciona uma mensagem de erro de validacao
     *
     * @param string $message
     *
     * @return PasswordValidator
     */
    protected function _setMessage($message)
    {
        $this->_message[] = $message;
        return $this;
    }
    
    /**
     * Retorna array com erros de validacao
     *
     * @return Array
     */
    public function getMessage()
    {
        return $this->_message;
    }
    
    /**
     * Seta o tamanho minimo de password aceito
     *
     * @param integer $minChars
     *
     * @return PasswordValidator
     */
    public function setMinChars($minChars)
    {
        $this->_minChars = $minChars;
        return $this;
    }
    
    /**
     * Retorna tamanho minimo aceito para password
     *
     * @return integer
     */
    public function getMinChars()
    {
        return $this->_minChars;
    }
    
    /**
     * Seta se deve ou nao validar Case Sensitive no password
     *
     * @param bool $value
     *
     * @return PasswordValidator
     */
    public function setCheckUpperAndLowerLetters($value)
    {
        $this->_checkUpperAndLowerLetters = $value;
        return $this;
    }
    
    /**
     * Informa se deve ou nao validar Case Sensitive
     *
     * @return bool
     */
    public function getCheckUpperAndLowerLetters()
    {
        return $this->_checkUpperAndLowerLetters;
    }
    
    /**
     * Verifica se um Password esta de acordo com regras preetabelecidas
     *
     * @param type $password
     *
     * @return bool
     */
    public function check($password)
    {
        $this->setPassword($password)
             ->_setValid(true)
             ->_resetMessage();
        
        $this->_hasMinChars();
        $this->_hasLettersAndNumbers();
        $this->_hasUpperAndLowercaseLetters();
        $this->_hasSequentialNumbers();
        $this->_hasDate();
        $this->_hasSpecialChars();
        
        return $this->getValid();
    }
    
    /**
     * Verifica se texto contem numero minimo de Caracteres exigidos
     *
     * @return PasswordValidator
     */
    protected function _hasMinChars()
    {
        if (strlen($this->getPassword()) < $this->getMinChars()) {
            $this->_setMessage("Não atende quantidade de caracteres minimo");
            $this->_setvalid(false);
        }
        return $this;
    }

    /**
     * Verifica se Password contem letras e numeros
     *
     * @return PasswordValidator
     */
    protected function _hasLettersAndNumbers()
    {
        if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $this->getPassword())) {
            $this->_setMessage("Não contem letras e Números");
            $this->_setvalid(false);
        }
        return $this;
    }
    
    /**
     * Verifica se Password tem ao menos uma letra maiuscula e minuscula
     *
     * @return PasswordValidator
     */
    protected function _hasUpperAndLowercaseLetters()
    {
        if (!$this->getCheckUpperAndLowerLetters()) {
            return $this;
        }
        
        $containsUpperLetter = preg_match('/[A-Z]/', $this->getPassword());
        $containsLowerLetter = preg_match('/[a-z]/', $this->getPassword());
        
        if (!$containsUpperLetter) {
            $this->_setMessage("Não contem letras maiusculas");
            $this->_setvalid(false);
        }
        
        if (!$containsLowerLetter) {
            $this->_setMessage("Não contem letras minusculas");
            $this->_setvalid(false);
        }
        
        return $this;
    }
    
    /**
     * Verifica se Password tem uma cadeira de numeros sequenciais
     *
     * @return PasswordValidator
     */
    protected function _hasSequentialNumbers()
    {
        $corrente = 1;
        $texto = preg_replace("/[^0-9]/", "", $this->getPassword());
        
        for ($i = 1; $i < strlen($texto); $i++) {
            if ($texto[$i] == ($texto[$i-1] + 1)) {
                $corrente++;
                if ($corrente >= $this->getMinChars()) {
                    $this->_setMessage("Contem sequencia de Numeros");
                    $this->_setvalid(false);
                }
                continue;
            }
            $corrente = 1;
        }
        return $this;
    }
    
    /**
     * Verifica se Password contem uma data no meio
     *
     * @return PasswordValidator
     */
    protected function _hasDate()
    {
        $pass = preg_replace('/[^0-9]/', '', $this->getPassword());

        if (
            (DateTime::createFromFormat('Ymd', $pass) !== FALSE) ||
            (DateTime::createFromFormat('dmY', $pass) !== FALSE) ||
            (DateTime::createFromFormat('mdY', $pass) !== FALSE)
        ) {
            $this->_setMessage("Contem uma data");
            $this->_setvalid(false);
        }
        
        return $this;
    }
    
    /**
     * Verifica se Password tem Caracteres Especiais
     *
     * @return PasswordValidator
     */
    protected function _hasSpecialChars()
    {
        if (preg_match('/[^a-zA-Z\d]/', $this->getPassword())) {
            $this->_setMessage("Contem Carateres Especiais");
            $this->_setvalid(false);
        }
        
        return $this;
    }
}


// Exemplo de uso: 01
$pv = new PasswordValidator();
$result = $pv->check('123456');
var_dump($result, $pv->getMessage());

// Exemplo de uso: 02
$result = $pv->check('Co1Tia6H');
var_dump($result, $pv->getMessage());

// Exemplo de uso: 03
$result = $pv->check('Wa24021986');
var_dump($result, $pv->getMessage());
