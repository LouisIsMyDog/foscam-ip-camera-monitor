<?php

class Session
{

    private $logged_in = false;
    public $is_admin   = false;
    public $user_id;
    public $user_name;
    public $message;

    public $settings = array(); // website settings

    public function __construct()
    {
        session_start();
        $this->checkMessage();
        $this->checkLogin();
        $this->checkAdmin();

        $this->settings();
    }

    public function isLoggedIn()
    {
        return $this->logged_in;
    }

    public function login($user)
    {

        if ($user) {

            $this->user_id   = $_SESSION['user_id']   = $user->id;
            $this->user_name = $_SESSION['user_name'] = $user->username;

            if (isset($this->user_id) && $this->user_id == 1) {
                $_SESSION['admin'] = $this->is_admin = true;

            } else {
                $_SESSION['admin'] = $this->is_admin = false;
            }

            $this->logged_in = true;
        }
    }

    public function logout()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }

        $this->logged_in = false;
        $this->is_admin  = false;

    }

    public function message($msg = "")
    {
        if (!empty($msg)) {
            $_SESSION['message'] = $msg;
        } else {
            return $this->message;
        }
    }

    private function checkLogin()
    {
        if (isset($_SESSION['user_id'])) {
            $this->user_id   = $_SESSION['user_id'];
            $this->user_name = $_SESSION['user_name'];
            $this->logged_in = true;
        } else {
            unset($this->user_id);
            unset($this->user_name);
            $this->logged_in = false;
        }
    }

    private function checkMessage()
    {

        if (isset($_SESSION['message'])) {
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            $this->message = "";
        }
    }

    private function checkAdmin()
    {
        if (isset($_SESSION['admin'])) {
            $this->is_admin = $_SESSION['admin'];
        }
    }

    public function dateFilter($dateFilter = "")
    {
        if (!empty($dateFilter)) {
            $_SESSION['dateFilter'] = $dateFilter;
        } else {
            return $this->dateFilter;
        }
    }
    public function settings()
    {

        $this->settings = $_SESSION['settings'];

        $this->settings['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

        if (isset($_POST['submit'])) {
            $this->settings['date']       = isset($_POST['date']) ? $_POST['date'] : date("m/d/Y");
            $this->settings['dateFilter'] = isset($_POST['date']) ? 1 : 0;
            $this->settings['order']      = isset($_POST['date']) ? 'ASC' : 'DESC';
            $this->settings['where']      = "AND date(time) = '" . date("Y-m-d", strtotime($this->settings['date'])) . "'";
            $this->settings['page']       = isset($_POST['date']) ? 1 : $this->settings['page'];

        }

        if (isset($_POST['clear'])) {
            $this->settings['date']       = date("m/d/Y");
            $this->settings['order']      = 'DESC';
            $this->settings['where']      = '';
            $this->settings['dateFilter'] = 0;
        }

        if (empty($_POST)) {
            $this->settings['order']      = isset($this->settings['order']) ? $this->settings['order'] : 'DESC';
            $this->settings['where']      = isset($this->settings['where']) ? $this->settings['where'] : '';
            $this->settings['dateFilter'] = isset($this->settings['dateFilter']) ? $this->settings['dateFilter'] : 0;
            // unset($_POST);
        }

        $_SESSION['settings'] = $this->settings;

    }
    public function setItem($array)
    {
        foreach ($array as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    public function getItem($name)
    {
        foreach ($_SESSION as $key => $value) {
            if ($name == $key):
                return $value;
            endif;
        }
    }

}

$session = new Session();
$message = $session->message();
