<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "skills".
 *
 * @property int $id
 * @property string $name
 *
 * @property UsersSkills[] $usersSkills
 * @property UsersRecord[] $users
 */
class SkillsRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skills';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 25],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Навык',
        ];
    }

    /**
     * Gets query for [[UsersSkills]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersSkills()
    {
        return $this->hasMany(UsersSkills::className(), ['skill_id' => 'id']);
    }

    /**
     * Gets query for [[UsersRecord]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(UsersRecord::className(), ['id' => 'user_id'])->viaTable('users_skills', ['skill_id' => 'id']);
    }
}
