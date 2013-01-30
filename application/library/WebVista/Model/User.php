<?php
/*****************************************************************************
*       User.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class WebVista_Model_User
{
    const ID_UNKNOWN = 'anonymous user';

    protected $_data = array(
        'id' => null
        );

    public function __construct($id = null)
    {
        if (null !== $id) {
            $this->_data['id'] = $id;
        }
    }

    public function getId()
    {
        if (null === $this->_data['id']) {
            return self::ID_UNKNOWN;
        } else {
            return $this->_data['id'];
        }
    }
}
