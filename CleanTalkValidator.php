<?php

/**
 * CleanTalk API CModel validator.
 *
 * Required set check property.
 *
 * @version 1.0.1
 * @author CleanTalk (welcome@cleantalk.ru)
 * @copyright (C) 2013 Сleantalk team (http://cleantalk.org)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 */
class CleanTalkValidator extends CValidator
{
    const CHECK_MESSAGE = 'message';
    const CHECK_USER = 'user';

    /**
     * message|user
     * @var string
     */
    public $check;

    /**
     * Email attribute name in model
     * @var string
     */
    public $emailAttribute;

    /**
     * Nickname attribute name in model
     * @var string
     */
    public $nickNameAttribute;

    /**
     * CleanTalk application component ID
     * @var string
     */
    public $apiComponentId = 'cleanTalk';

    /**
     * @inheritdoc
     * @var bool
     */
    public $skipOnError = true;

    /**
     * @inheritdoc
     */
    protected function validateAttribute($object, $attribute)
    {
        $this->checkValidateConfig($object);

        /**
         * @var CleanTalkApi $api
         */
        $api = Yii::app()->getComponent($this->apiComponentId);
        $email = property_exists($object, $this->emailAttribute) ? $object->{$this->emailAttribute} : '';
        $nick = property_exists($object, $this->nickNameAttribute) ? $object->{$this->nickNameAttribute} : '';

        if (self::CHECK_MESSAGE == $this->check) {
            if (!$api->isAllowMessage($object->$attribute, $email, $nick)) {
                $this->addError($object, $attribute, $this->getErrorMessage());
            }
        } elseif (self::CHECK_USER == $this->check) {
            if (!$api->isAllowUser($email, $nick)) {
                $this->addError($object, $attribute, $this->getErrorMessage());
            }
        }
    }

    /**
     * Check validator configuration
     * @param CModel $object
     * @throws CException
     */
    protected function checkValidateConfig(CModel $object)
    {
        if (!Yii::app()->hasComponent($this->apiComponentId)) {
            throw new CException(Yii::t(
                'cleantalk',
                'Application component "' . $this->apiComponentId . '" is not defined'
            ));
        } elseif (!in_array($this->check, [self::CHECK_MESSAGE, self::CHECK_USER])) {
            throw new CException(Yii::t(
                'cleantalk',
                'Validation check property is not defined or invalid'
            ));
        }
        // @todo:
        /* elseif (!property_exists($object, $this->emailAttribute)) {
                    throw new CException(Yii::t(
                        'cleantalk',
                        'Validation "emailAttribute" property is not defined or invalid'
                    ));
                } elseif (!property_exists($object, $this->nickNameAttribute)) {
                    throw new CException(Yii::t(
                        'cleantalk',
                        'Validation "nickNameAttribute" property is not defined or invalid'
                    ));
                }*/
    }

    /**
     * Get CleanTalk API deny message
     * @return string
     */
    protected function getErrorMessage()
    {
        return Yii::app()->getComponent($this->apiComponentId)->getValidationError();
    }
}