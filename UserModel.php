<?php

namespace smcodes\phpmvc;

use smcodes\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
    abstract public function getDisplayName() :string;
}