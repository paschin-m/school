<?php

namespace app\rbac;

use Yii;
use yii\rbac\Rule;
use app\models\UserRecord;

class UserRoleRule extends Rule
{
  /**
   * @inheritdoc
   */
  public $name = 'userRole';

  private $_assignments = [];

  /**
   * @inheritdoc
   */
  public function execute($user, $item, $params)
  {
    if ($role = $this->userRole($user)) {
      switch ($item->name) {
        case UserRecord::ROLE_SUPERUSER:
          return $role == UserRecord::ROLE_SUPERUSER;

        case UserRecord::ROLE_REGISTERED:
          return $role == UserRecord::ROLE_SUPERUSER || $role == UserRecord::ROLE_REGISTERED;

        case UserRecord::ROLE_GUEST:
          return in_array($role, [UserRecord::ROLE_SUPERUSER, UserRecord::ROLE_REGISTERED, UserRecord::ROLE_GUEST]);
      }
    }
    return false;
  }

  /**
   * @param integer|null $userId ID of user.
   * @return string|false
   */
  protected function userRole($userId)
  {
    $user = Yii::$app->user;
    if ($userId === null) {
      if ($user->isGuest) {
        return UserRecord::ROLE_GUEST;
      }
      return false;
    }
    if (!isset($this->_assignments[$userId])) {
      $role = false;
      if (!$user->isGuest && $user->id == $userId) {
        $role = $user->role;
      } elseif ($user->isGuest || $user->id != $userId) {
        $role = UserRecord::getRoleOfUser($userId);
      }
      $this->_assignments[$userId] = $role;
    }
    return $this->_assignments[$userId];
  }

  /**
   * Возвращает роль пользователя или `null`.
   * @return string|null
   */
  public function getRole()
  {
    $identity = $this->getIdentity();
    return $identity !== null ? $identity->role : null;
  }
}
