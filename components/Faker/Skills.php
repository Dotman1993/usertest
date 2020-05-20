<?php

namespace app\components\Faker;

class Skills extends \Faker\Provider\Base
{

    protected static $skillNameFormat = array(
        '{{word}}',
    );

    public function skillName()
    {
        $format = static::randomElement(static::$skillNameFormat);

        return $this->generator->parse($format);
    }
}