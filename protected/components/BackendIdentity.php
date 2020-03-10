<?php

namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class BackendIdentity extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'g_admin';
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function authenticate($mail, $password)
    {
        $user = static::findOne(['mail' => $mail, 'active' => 1]);

        if (!empty($user)) {
            if (Yii::$app->getSecurity()->validatePassword($user->salt . $password, $user->password)) {
                return $user;
            } else {
                return false;
            }
        }

        return false;
    }
}