<?php

class ProductsGroup extends Model
{

    public static $_tableName = 'products_group';

    /**
     * get all products groups that has public product
     *
     * @param bool $isWl
     * @return mixed
     */
    public function getPublic($isWl = false)
    {
        $select = $this->select()
            ->distinct()
            ->from(array('p_g' => self::$_tableName), array('id', 'name', 'supported_wl', 'ordered'))
            ->join(array('p' => Products::$_tableName), 'p.group_id = p_g.id', array())
            ->where('p.published = 1')
            ->order('p_g.ordered ASC')
            ->setIntegrityCheck(false);

        if ($isWl == true) {
            $select->where('p_g.supported_wl = 1');
        }

        return $this->fetchAll($select)->toArray();
    }

    /**
     * check for validation if group exist
     *
     * @param type $group
     * @return type
     */
    public function exist($group)
    {
        $select = $this->select()
            ->distinct()
            ->from(array('p_g' => self::$_tableName), '*')
            ->join(array('p' => Products::$_tableName), 'p.group_id = p_g.id', array())
            ->where("p.published = 1 AND p_g.name = '$group'")
            ->order('p_g.ordered ASC')
            ->setIntegrityCheck(false);
        return (count($this->fetchAll($select)->toArray())) ? true : false;
    }

    /**
     * check for validation if group exist
     *
     * @param type $group
     * @return type
     */
    public static function getGroupId($group)
    {
        $productsGroup = new ProductsGroup();
        $select = $productsGroup->select()
            ->distinct()
            ->from(array('p_g' => self::$_tableName), '*')
            ->join(array('p' => Products::$_tableName), 'p.group_id = p_g.id', array())
            ->where("p.published = 1 AND p_g.name = '$group'")
            ->order('p_g.ordered ASC')
            ->setIntegrityCheck(false);
        $group = $productsGroup->fetchAll($select)->toArray();
        return (isset($group[0]['id'])) ? $group[0]['id'] : null;
    }

    public function getPublicForWL($t)
    {
        $select = $this->select()
            ->distinct()
            ->from(array('p_g' => self::$_tableName), array('id', 'name', 'ordered'))
            ->join(array('p' => Products::$_tableName), 'p.group_id = p_g.id', array())
            ->where('p.published = 1 AND p_g.supported_wl = 1')
            ->order('p_g.ordered ASC')
            ->setIntegrityCheck(false);
        $groups = $this->fetchAll($select)->toArray();
        $groupsSorted = array();
        foreach ($groups as $group) {
            $groupsSorted[] = array(
                'id'   => $group['id'],
                'code' => $group['name'],
                'name' => $t->_($group['name'])
            );
        }
        return $groupsSorted;
    }
}
