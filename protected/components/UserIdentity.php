<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {
    // Будем хранить id.
    protected $_id;

    // Данный метод вызывается один раз при аутентификации пользователя.
    public function authenticate(){

        // Производим стандартную аутентификацию, описанную в руководстве.

        $user = CmsUser::model()->find('LOWER(username)=?', array(strtolower($this->username)));
        if($user->ban==1) die ("Ваш пользователь забанен, для дополнительной инвормации обратитеть к администратору");

        if(($user===null) || (md5('lkjhgfd'.$this->password)!==$user->password))
            if(($user===null) || ($this->password!==$user->password))
                $this->errorCode = self::ERROR_USERNAME_INVALID;
              else {
            $this->_id = $user->id;
            $this->username = $user->username;
            CmsUser::model()->updateByPk($this->_id,array('data_avtor'=>time()));
            $this->errorCode = self::ERROR_NONE;
        }
        else {
            $this->_id = $user->id;
            $this->username = $user->username;
            CmsUser::model()->updateByPk($this->_id,array('data_avtor'=>time()));
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }



    public function getId(){
        return $this->_id;
    }
}