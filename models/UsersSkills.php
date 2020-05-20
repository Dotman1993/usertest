<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_skills".
 *
 * @property int $user_id
 * @property int $skill_id
 *
 * @property UsersRecord $user
 * @property SkillsRecord $skill
 */
class UsersSkills extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_skills';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'skill_id'], 'required'],
            [['user_id', 'skill_id'], 'integer'],
            [['user_id', 'skill_id'], 'unique', 'targetAttribute' => ['user_id', 'skill_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['skill_id'], 'exist', 'skipOnError' => true, 'targetClass' => Skills::className(), 'targetAttribute' => ['skill_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID Пользователя',
            'skill_id' => 'ID Навыка',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UsersRecord::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Skill]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkill()
    {
        return $this->hasOne(SkillsRecord::className(), ['id' => 'skill_id']);
    }
}
