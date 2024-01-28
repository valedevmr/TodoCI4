<?php

namespace App\Interfaces;

use CodeIgniter\HTTP\RequestInterface;

interface AuthI
{
    public function authUser(array $data):array;
    public function gToken():array;
    public function vToken(RequestInterface $request);
}