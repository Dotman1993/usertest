<?php

namespace app\models;

use Yii;
use app\models\CityRecord;
use app\models\SkillsRecord;
use app\models\UsersSkills;

class UsersRecord extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['name', 'city_id'], 'required'],
            [['city_id'], 'integer'],
            [['name'], 'string', 'max' => 25],
        ];
    }
	
	public function transactions()
    {
        return [
            'default' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'city_id' => 'Место жительства',
			'skills' => 'Навыки',
        ];
    }
	
	public function getCity()
    {
        return $this->hasOne(CityRecord::className(), ['id' => 'city_id']);
    }
	
	public function getUsersSkills()
    {
        return $this->hasMany(UsersSkills::className(), ['user_id' => 'id'])->indexBy('skill_id');
    }
	
	public function getSkills()
    {
        return $this->hasMany(SkillsRecord::className(), ['id' => 'skill_id'])
            ->via('usersSkills')->indexBy('id');
    }
	
	public function beforeDelete()
	{
		if (!parent::beforeDelete()) {
			return false;
		}
		
		UsersSkills::deleteAll(['user_id' => $this->id]);
		
		return true;
	}
}
