<?php

class User {

    private $_db;
    private $_dbm;

    function __construct($db, $dbm) {
        $this->_db = $db;
        $this->_dbm = $dbm;
    }

	private function get_user_hash($username) {

		try {
			$stmt = $this->_db->prepare('SELECT password, username, id FROM members WHERE username = :username AND active = 1 ');
			$stmt->execute(array('username' => $username));

			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p>'.$e->getMessage().'</p>';
		}
	}

	public function isValidUsername($username) {
		if(strlen($username) < 3) return false;
		if(strlen($username) > 17) return false;
		if(!ctype_alnum($username)) return false;
		return true;
	}

	public function login($username,$password) {
		if(!$this->isValidUsername($username)) return false;
		if(strlen($password) < 3) return false;

		$row = $this->get_user_hash($username);

		if(password_verify($password, $row['password']) == 1) {

		    $_SESSION['loggedin'] = true;
		    $_SESSION['username'] = $row['username'];
		    $_SESSION['id'] = $row['id'];
		    return true;
		}
	}

	public function logout() {
		session_destroy();
	}

	public function is_logged_in() {
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
			return true;
		}
	}

	/**
     * Generates Entropy using the safest available method, falling back to less preferred methods depending on support
     *
     * @param int $bytes
     *
     * @return string Returns raw bytes
     */
    function generate_entropy($bytes){
        $buffer = '';
        $buffer_valid = false;
        if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }
        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($bytes);
            if ($buffer) {
                $buffer_valid = true;
            }
        }
        if (!$buffer_valid && is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $bytes) {
                $buffer .= fread($f, $bytes - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $bytes) {
                $buffer_valid = true;
            }
        }
        if (!$buffer_valid || strlen($buffer) < $bytes) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $bytes; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }
        return $buffer;
    }
}



?>